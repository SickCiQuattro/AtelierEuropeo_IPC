<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    protected $table = 'projects';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'title',
        'user_id',
        'category_id',
        'association_id',
        'image_path',
        'status',
        'requested_people',
        'location',
        'start_date',
        'end_date',
        'expire_date',
        'sum_description',
        'full_description',
        'requirements',
        'travel_conditions',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expire_date' => 'date',
    ];

    /**
     * Filtri condivisi da catalogo e portfolio: testo, categoria, durata, data di inizio.
     */
    public function scopeApplyCatalogFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sum_description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && is_array($request->category)) {
            $query->whereIn('category_id', $request->category);
        }

        if ($request->filled('duration') && is_array($request->duration)) {
            // Giorni tra start_date e end_date, valido sia su SQLite sia su MySQL
            $days = DB::connection()->getDriverName() === 'sqlite'
                ? 'CAST(julianday(end_date) - julianday(start_date) AS INTEGER)'
                : 'DATEDIFF(end_date, start_date)';

            $ranges = [
                'short' => "{$days} < 15",
                'medium' => "{$days} BETWEEN 15 AND 60",
                'long' => "{$days} BETWEEN 60 AND 180",
                'very_long' => "{$days} > 180",
            ];
            $selected = array_intersect_key($ranges, array_flip($request->duration));

            if ($selected) {
                $query->where(function ($q) use ($selected) {
                    foreach ($selected as $condition) {
                        $q->orWhereRaw($condition);
                    }
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        return $query;
    }

    protected static function booted(): void
    {
        // Pulisce i file orfani; il delete a cascata del DB non li tocca
        static::deleting(function (Project $project) {
            $image = $project->image_path;
            if ($image && !str_starts_with($image, 'http') && !str_starts_with($image, 'img/')) {
                Storage::disk('public')->delete($image);
            }

            $project->application()->whereNotNull('document_path')->pluck('document_path')
                ->each(fn ($path) => Storage::disk('local')->delete($path));
        });
    }

    public static function allowedStatusTransitions(?string $currentStatus): array
    {
        return match (strtolower((string) $currentStatus)) {
            self::STATUS_DRAFT => [self::STATUS_DRAFT, self::STATUS_PUBLISHED],
            self::STATUS_PUBLISHED => [self::STATUS_PUBLISHED, self::STATUS_COMPLETED],
            self::STATUS_COMPLETED => [self::STATUS_COMPLETED],
            default => [self::STATUS_DRAFT, self::STATUS_PUBLISHED],
        };
    }

    public static function nextStatusTransition(?string $currentStatus): ?string
    {
        return match (strtolower((string) $currentStatus)) {
            self::STATUS_DRAFT => self::STATUS_PUBLISHED,
            self::STATUS_PUBLISHED => self::STATUS_COMPLETED,
            default => null,
        };
    }

    public static function canTransitionStatus(?string $currentStatus, string $targetStatus): bool
    {
        return in_array(strtolower($targetStatus), self::allowedStatusTransitions($currentStatus), true);
    }

    /**
     * Get the correct image URL for this project
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('img/projects/default.png'); // Fallback di default
        }

        // Se inizia con http, è un URL esterno (es. da faker)
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        // Se inizia con img/, è un path pubblico (es. immagini di default)
        if (str_starts_with($this->image_path, 'img/')) {
            return asset($this->image_path);
        }

        // Altrimenti è un file caricato su storage/app/public.
        // Usiamo una route dedicata per evitare dipendenza dal symlink public/storage.
        if (\Illuminate\Support\Facades\Route::has('project.image')) {
            return route('project.image', ['path' => ltrim($this->image_path, '/')]);
        }

        return asset('storage/' . $this->image_path);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function association()
    {
        return $this->belongsTo(Association::class, 'association_id');
    }

    public function application()
    {
        return $this->hasMany(Application::class, 'project_id');
    }

    public function testimonial()
    {
        return $this->hasMany(Testimonial::class, 'project_id');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorites', 'project_id', 'user_id')
                    ->withTimestamps();
    }
}
