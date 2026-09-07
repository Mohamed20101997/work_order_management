<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Attachment extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['attachable_type', 'attachable_id', 'user_id', 'mime_type', 'size'];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('attachments')
            ->nonQueued();

        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85)
            ->width(1200)
            ->performOnCollections('attachments')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(800)
            ->sharpen(10)
            ->performOnCollections('attachments')
            ->nonQueued();
    }

    public function getThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('attachments', 'thumb');
    }

    public function getWebPUrl(): ?string
    {
        return $this->getFirstMediaUrl('attachments', 'webp');
    }

    public function getMediumUrl(): ?string
    {
        return $this->getFirstMediaUrl('attachments', 'medium');
    }
}
