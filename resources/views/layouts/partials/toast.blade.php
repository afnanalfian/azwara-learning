{{-- Inject Toast dari Laravel Session --}}
<script>
    window.__toasts = @json(session('toasts', []));
</script>

@php session()->forget('toasts'); @endphp

{{-- 🔔 Toast Container --}}
<div
    x-data="toastManager()"
    x-init="init()"
    class="fixed top-24 right-6 z-[9999] space-y-3 w-80"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-cloak
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-10"
            class="border shadow-md cursor-pointer select-none"
            :class="theme(toast.type)"
            @click="remove(toast.id)"
            @mouseenter="pause(toast)"
            @mouseleave="resume(toast)"
        >
            {{-- HEADER TYPE --}}
            <div class="px-3 py-1 text-xs font-semibold uppercase border-b"
                 :class="headerTheme(toast.type)"
                 x-text="toast.type">
            </div>

            {{-- BODY --}}
            <div class="px-3 py-2 text-sm">
                <span x-text="toast.message"></span>
            </div>

            {{-- PROGRESS BAR --}}
            <div class="h-1 bg-black/10 overflow-hidden">
                <div
                    class="h-full"
                    :class="progressTheme(toast.type)"
                    :style="`
                        animation: toast-progress ${toast.timeout}ms linear forwards;
                        animation-play-state: ${toast.paused ? 'paused' : 'running'};
                    `"
                ></div>
            </div>
        </div>
    </template>
</div>
