<div
    x-data="pwaInstall()"
    x-cloak
    class="fixed bottom-6 z-50 ltr:right-6 rtl:left-6"
    x-show="visible"
>
    {{-- Floating install button --}}
    <button
        x-show="! showIosSheet"
        @click="install()"
        class="fi-btn group flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-xl shadow-blue-500/30 transition hover:scale-[1.03] hover:shadow-blue-500/50 active:scale-95"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        <span>{{ __('Download app') }}</span>
        <span
            @click.stop="dismiss()"
            class="ms-1 rounded-full p-0.5 text-blue-100 opacity-70 transition hover:bg-white/20 hover:opacity-100"
        >&times;</span>
    </button>

    {{-- iOS instructions sheet --}}
    <div
        x-show="showIosSheet"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-8 opacity-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="translate-y-4 opacity-0"
        class="w-72 rounded-2xl bg-white p-5 text-gray-800 shadow-2xl ring-1 ring-black/5 dark:bg-gray-800 dark:text-gray-100 dark:ring-white/10"
    >
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold">{{ __('Install the app') }}</h3>
            <button @click="showIosSheet = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">&times;</button>
        </div>
        <p class="mb-2 text-xs leading-5">{{ __('On iPhone or iPad, tap the share icon in Safari, then choose “Add to Home Screen”.') }}</p>
        <p class="text-xs leading-5">{{ __('On Android, tap the menu in Chrome, then choose “Install app”.') }}</p>
        <div class="mt-4 flex justify-end">
            <button
                @click="showIosSheet = false"
                class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700"
            >{{ __('Close') }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pwaInstall', () => ({
            deferredPrompt: null,
            visible: false,
            showIosSheet: false,

            init() {
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches
                    || window.navigator.standalone === true

                if (isStandalone || localStorage.getItem('pwa-dismissed')) {
                    return
                }

                window.addEventListener('beforeinstallprompt', (event) => {
                    event.preventDefault()
                    this.deferredPrompt = event
                    this.visible = true
                })

                const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent)
                    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)

                if (isIos && !isStandalone) {
                    this.visible = true
                }

                window.addEventListener('appinstalled', () => {
                    this.visible = false
                    this.deferredPrompt = null
                })
            },

            install() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt()
                    this.deferredPrompt.userChoice.then(() => {
                        this.deferredPrompt = null
                        this.visible = false
                    })

                    return
                }

                this.showIosSheet = true
            },

            dismiss() {
                this.visible = false
                localStorage.setItem('pwa-dismissed', '1')
            },
        }))
    })

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {})
        })
    }
</script>
