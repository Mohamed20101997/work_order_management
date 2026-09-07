<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'asset_id', 'reported_problem', 'diagnosis', 'work_performed', 'notes',
        'status', 'priority', 'assigned_to', 'due_date', 'started_at', 'completed_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkOrderStatus::class,
            'priority' => Priority::class,
            'due_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $workOrder): void {
            $workOrder->created_by ??= auth()->id();
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function partUsages(): HasMany
    {
        return $this->hasMany(PartUsage::class);
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

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', WorkOrderStatus::Completed);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->open()->whereNotNull('due_date')->whereDate('due_date', '<', today());
    }

    public function isOverdue(): bool
    {
        return $this->status !== WorkOrderStatus::Completed
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function assignNumber(): void
    {
        $this->update(['number' => sprintf('WO-%06d', $this->id)]);
    }
}
