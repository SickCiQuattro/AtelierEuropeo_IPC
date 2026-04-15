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

        $updatedCount = Project::whereIn('id', $projectIds)
            ->update(['status' => $validated['status']]);

        $statusLabels = [
            'draft' => 'Bozza',
            'published' => 'Pubblicato',
            'completed' => 'Completato',
        ];

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
