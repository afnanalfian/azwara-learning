window.toastManager = function () {
    return {
        toasts: [],
        counter: 0,
        initialized: false,

        init() {
            if (this.initialized) return;
            this.initialized = true;

            (window.__toasts || []).forEach(t => {
                this.push(t.type, t.message, t.timeout ?? 3000);
            });

            window.__toasts = [];
        },

        push(type, message, timeout = 3000) {
            const id = ++this.counter;

            const toast = {
                id,
                type,
                message,
                timeout,
                visible: true,
                paused: false,
                remaining: timeout,
                startedAt: Date.now(),
                timer: null,
            };

            this.toasts.push(toast);
            this.startTimer(toast);
        },

        startTimer(toast) {
            toast.startedAt = Date.now();

            toast.timer = setTimeout(() => {
                this.remove(toast.id);
            }, toast.remaining);
        },

        pause(toast) {
            if (toast.paused) return;

            toast.paused = true;
            clearTimeout(toast.timer);

            const elapsed = Date.now() - toast.startedAt;
            toast.remaining -= elapsed;
        },

        resume(toast) {
            if (!toast.paused) return;

            toast.paused = false;
            this.startTimer(toast);
        },

        remove(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (!toast) return;

            toast.visible = false;

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 200);
        },

        theme(type) {
            return [
                'border',
                'bg-white text-gray-800',
                'dark:bg-gray-900 dark:text-gray-100',
                {
                    success: 'border-emerald-500',
                    error: 'border-red-500',
                    warning: 'border-amber-500',
                    info: 'border-blue-500',
                }[type]
            ].join(' ');
        },

        headerTheme(type) {
            return [
                'border-b text-xs font-semibold uppercase',
                'bg-gray-50 text-gray-600',
                'dark:bg-gray-800 dark:text-gray-300',
                {
                    success: 'border-emerald-500',
                    error: 'border-red-500',
                    warning: 'border-amber-500',
                    info: 'border-blue-500',
                }[type]
            ].join(' ');
        },

        progressTheme(type) {
            return {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                warning: 'bg-amber-400',
                info: 'bg-blue-500',
            }[type];
        },
    };
};
