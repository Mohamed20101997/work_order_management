<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center gap-1 text-sm text-slate-300 hover:text-white" aria-label="Switch language">
        <span>{{ __('Language') }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" class="absolute right-0 mt-1 w-40 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black/5">
        <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('English') }}</a>
        <a href="{{ route('locale.switch', ['locale' => 'ar']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Arabic') }}</a>
    </div>
</div>
