<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Attachment;
use App\Models\Inspection;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    private const TYPES = [
        'assets' => [Asset::class, 'assets.view'],
        'work-orders' => [WorkOrder::class, 'work-orders.view'],
        'inspections' => [Inspection::class, 'inspections.view'],
    ];

    public function store(Request $request, string $type, int $id): JsonResponse|RedirectResponse
    {
        Gate::authorize('attachments.upload');

        abort_unless(isset(self::TYPES[$type]), 404);
        [$class] = self::TYPES[$type];
        $model = $class::findOrFail($id);

        $validated = $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $uploaded = [];

        foreach ($validated['files'] as $file) {
            $media = $model
                ->addMedia($file)
                ->toMediaCollection('attachments');

            $media->load('media');

            $uploaded[] = [
                'id' => $media->id,
                'url' => $media->getUrl('attachments'),
                'thumbnail' => $media->getUrl('attachments', 'thumb'),
                'webp' => $media->getUrl('attachments', 'webp'),
                'medium' => $media->getUrl('attachments', 'medium'),
                'original_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'created_at' => $media->created_at->toISOString(),
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploaded) . ' file(s) uploaded successfully.',
                'files' => $uploaded,
            ]);
        }

        return back()->with('success', count($uploaded) . ' file(s) uploaded.');
    }

    public function show(Attachment $attachment): StreamedResponse
    {
        Gate::authorize($this->viewAbility($attachment));

        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        return Storage::disk('local')->response($attachment->path, $attachment->original_name);
    }

    public function destroy(Request $request, Attachment $attachment): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->id === $attachment->user_id || $user->role === 'admin', 403);

        if ($attachment->media) {
            $attachment->media->each->delete();
        }

        $attachment->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Attachment deleted.']);
        }

        return back()->with('success', 'Attachment deleted.');
    }

    public function destroyByMediaId(Request $request, int $mediaId): JsonResponse
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);

        Gate::authorize('attachments.upload');

        $attachment = Attachment::where('id', $media->model_id)->first();
        abort_unless($attachment && ($request->user()->id === $attachment->user_id || $request->user()->role === 'admin'), 403);

        $media->delete();

        return response()->json(['success' => true, 'message' => 'Media deleted.']);
    }

    public function index(Request $request, string $type, int $id): JsonResponse
    {
        [$class] = self::TYPES[$type];
        $model = $class::findOrFail($id);

        $attachments = $model->getMedia('attachments')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumbnail' => $media->getUrl('thumb'),
            'webp' => $media->getUrl('webp'),
            'original_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'created_at' => $media->created_at->toISOString(),
        ]);

        return response()->json(['attachments' => $attachments]);
    }

    private function viewAbility(Attachment $attachment): string
    {
        foreach (self::TYPES as [$class, $ability]) {
            if ($attachment->attachable_type === $class) {
                return $ability;
            }
        }

        abort(404);
    }
}
