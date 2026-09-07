{{-- Expects: $activities --}}
<div class="rounded-xl bg-white p-4 shadow-sm">
    <h3 class="mb-3 font-semibold">{{ __('History') }}</h3>
    <ul class="space-y-3 text-sm">
        @forelse ($activities as $activity)
            <li class="border-l-2 border-blue-500 pl-3">
                <p>{{ $activity->description }}</p>
                <p class="text-xs text-gray-500">
                    {{ $activity->user?->name ?? __('System') }} &middot; {{ $activity->created_at->format('Y-m-d H:i') }} ({{ $activity->created_at->diffForHumans() }})
                </p>
            </li>
        @empty
            <li class="text-gray-500">{{ __('No history yet.') }}</li>
        @endforelse
    </ul>
</div>
