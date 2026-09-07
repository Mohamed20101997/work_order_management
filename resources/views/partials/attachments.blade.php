{{-- Expects: $attachable (model with attachments loaded), $type (assets|work-orders|inspections) --}}
<div class="rounded-xl bg-white p-4 shadow-sm">
    <h3 class="mb-3 font-semibold">{{ __('Attachments') }}</h3>
    <ul class="space-y-2 text-sm">
        @forelse ($attachable->attachments as $attachment)
            <li class="flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('attachments.show', $attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                    {{ $attachment->original_name }}
                </a>
                <span class="text-xs text-gray-500">
                    {{ number_format($attachment->size / 1024) }} KB &middot; {{ $attachment->user?->name ?? __('Unknown') }}
                </span>
                @if (auth()->id() === $attachment->user_id || auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm(__('Delete this attachment?'))">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs text-red-600 hover:underline">{{ __('Delete') }}</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="text-gray-500">{{ __('No attachments yet.') }}</li>
        @endforelse
    </ul>
    @can('attachments.upload')
        <form method="POST" action="{{ route('attachments.store', [$type, $attachable->id]) }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-center gap-2">
            @csrf
            <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.pdf" required class="text-sm">
            <button class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white hover:bg-slate-700">{{ __('Upload') }}</button>
        </form>
        <p class="mt-1 text-xs text-gray-500">JPG, PNG, WEBP or PDF. Max 10 MB. On mobile you can take a photo directly.</p>
    @endcan
</div>
