<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminProjectController extends Controller
{
    /**
     * Display admin projects index with server-side filters.
     */
    public function index(Request $request)
    {
        $hasCountryColumn = Schema::hasColumn('projects', 'country');

        $query = Project::query()
            ->with('category')
            ->withCount([
                'application as approved_applications_count' => function ($q) {
                    $q->where('status', 'approved');
                },
            ]);

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sum_description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $allowedStatuses = ['published', 'draft', 'completed'];
        $status = $request->input('status');
        if ($status && in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        }

        if ($request->filled('country')) {
            $country = trim((string) $request->input('country'));

            if ($hasCountryColumn) {
                $query->where('country', $country);
            } else {
                $query->where('location', 'like', "%{$country}%");
            }
        }

        $today = Carbon::today();
        $deadline = $request->input('deadline');

        if ($deadline === '7') {
            $query->whereNotNull('expire_date')
                ->whereDate('expire_date', '>=', $today)
                ->whereDate('expire_date', '<=', $today->copy()->addDays(7));
        } elseif ($deadline === '30') {
            $query->whereNotNull('expire_date')
                ->whereDate('expire_date', '>=', $today)
                ->whereDate('expire_date', '<=', $today->copy()->addDays(30));
        } elseif ($deadline === 'expired') {
            $query->whereNotNull('expire_date')
                ->whereDate('expire_date', '<', $today);
        }

        $query->orderByRaw("CASE status WHEN 'published' THEN 1 WHEN 'draft' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
            ->orderBy('expire_date', 'asc')
            ->orderBy('created_at', 'desc');

        $projects = $query->paginate(10);
        $projects->appends($request->query());

        if ($hasCountryColumn) {
            $availableCountries = Project::whereNotNull('country')
                ->where('country', '!=', '')
                ->distinct()
                ->orderBy('country')
                ->pluck('country');
        } else {
            $availableCountries = Project::whereNotNull('location')
                ->pluck('location')
                ->map(function ($location) {
                    $normalizedLocation = trim((string) $location);
                    if ($normalizedLocation === '') {
                        return null;
                    }

                    $parts = array_map('trim', explode(',', $normalizedLocation));
                    $derivedCountry = end($parts);

                    return $derivedCountry !== false && $derivedCountry !== ''
                        ? $derivedCountry
                        : $normalizedLocation;
                })
                ->filter()
                ->unique()
                ->sort(function ($a, $b) {
                    return strcasecmp((string) $a, (string) $b);
                })
                ->values();
        }

        return view('admin.projects.index', compact('projects', 'availableCountries'));
    }

    /**
     * Bulk update status for selected projects.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'project_ids' => ['required', 'array', 'min:1'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
            'status' => ['required', 'in:draft,published,completed'],
        ]);

        $projectIds = collect($validated['project_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $projects = Project::query()
            ->whereIn('id', $projectIds)
            ->get(['id', 'status']);

        if ($projects->count() !== count($projectIds)) {
            return redirect()->back()->with('error', 'Uno o più progetti selezionati non sono più disponibili. Riprova.');
        }

        $statusLabels = [
            Project::STATUS_DRAFT => 'Bozza',
            Project::STATUS_PUBLISHED => 'Pubblicato',
            Project::STATUS_COMPLETED => 'Completato',
        ];

        $selectedStatuses = $projects
            ->pluck('status')
            ->map(fn ($status) => strtolower((string) $status))
            ->unique()
            ->values();

        if ($selectedStatuses->count() > 1) {
            $statusList = $selectedStatuses->map(fn ($status) => $statusLabels[$status] ?? ucfirst($status))->implode(', ');

            return redirect()->back()->with(
                'warning',
                'Hai selezionato progetti con stati diversi (' . $statusList . '). Seleziona solo progetti con lo stesso stato per l\'aggiornamento in blocco.'
            );
        }

        $currentStatus = $selectedStatuses->first();
        $nextStatus = Project::nextStatusTransition($currentStatus);

        if ($nextStatus === null) {
            return redirect()->back()->with(
                'warning',
                'I progetti completati non possono cambiare stato.'
            );
        }

        if ($validated['status'] !== $nextStatus) {
            return redirect()->back()->with(
                'warning',
                'Stato non consentito per i progetti selezionati. Da ' . ($statusLabels[$currentStatus] ?? ucfirst((string) $currentStatus)) . ' è possibile passare solo a ' . ($statusLabels[$nextStatus] ?? ucfirst((string) $nextStatus)) . '.'
            );
        }

        $updatedCount = Project::whereIn('id', $projectIds)
            ->update(['status' => $validated['status']]);

        return redirect()->back()->with(
            'success',
            "Stato aggiornato a {$statusLabels[$validated['status']]} per {$updatedCount} progetti."
        );
    }

    /**
     * Bulk delete selected projects.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'project_ids' => ['required', 'array', 'min:1'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
        ]);

        $projectIds = collect($validated['project_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $deletedCount = Project::whereIn('id', $projectIds)->delete();

        return redirect()->back()->with('success', "{$deletedCount} progetti eliminati con successo.");
    }
}
