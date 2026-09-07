<?php

namespace App\Models;

use App\Enums\AssetStatus;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Asset extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'asset_number', 'asset_type_id', 'name', 'serial_number', 'manufacturer', 'model',
        'company_id', 'description', 'location', 'received_date', 'expected_release_date',
        'released_date', 'status', 'priority', 'notes', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'expected_release_date' => 'date',
            'released_date' => 'date',
            'status' => AssetStatus::class,
            'priority' => Priority::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            $asset->created_by ??= auth()->id();
        });

        static::updating(function (self $asset): void {
            $asset->updated_by = auth()->id() ?? $asset->updated_by;
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('public');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term): void {
            $q->where('asset_number', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('serial_number', 'like', "%{$term}%");
        }));
    }

    public function assignNumber(): void
    {
        $this->update(['asset_number' => sprintf('%s-%06d', $this->assetType->code, $this->id)]);
    }
}
