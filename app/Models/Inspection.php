<?php

namespace App\Models;

use App\Enums\InspectionResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'asset_id', 'work_order_id', 'inspector_id',
        'inspection_date', 'result', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
            'result' => InspectionResult::class,
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function assignNumber(): void
    {
        $this->update(['number' => sprintf('INS-%06d', $this->id)]);
    }
}
