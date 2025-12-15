<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

<script>
(() => {
    const colors = {
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        warning: 'bg-amber-500',
        info: 'bg-sky-600',
    };

    function showToast({ type = 'info', message = '', duration = 4000 } = {}) {
        const container = document.getElementById('toast-container');
        if (!container || !message) return;

        const el = document.createElement('div');
        el.className = `pointer-events-auto max-w-sm shadow-xl rounded-xl text-white px-4 py-3 flex items-start gap-3 transition transform duration-300 ease-out ${colors[type] || colors.info} bg-opacity-95`;
        el.style.opacity = '0';
        el.style.transform = 'translateX(16px)';

        el.innerHTML = `
            <div class="text-sm leading-snug">
                ${message}
            </div>
            <button aria-label="Close" class="ml-auto text-white/80 hover:text-white font-semibold">×</button>
        `;

        const closeBtn = el.querySelector('button');
        const remove = () => {
            el.style.opacity = '0';
            el.style.transform = 'translateX(16px)';
            setTimeout(() => el.remove(), 250);
        };
        closeBtn.addEventListener('click', remove);

        container.appendChild(el);
        // Trigger transition
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateX(0)';
        });

        setTimeout(remove, duration);
    }

    window.notify = showToast;

    // Flash messages from backend
    const flashes = [];
    @if (session('success'))
        flashes.push({ type: 'success', message: @json(session('success')) });
    @endif
    @if (session('status'))
        flashes.push({ type: 'info', message: @json(session('status')) });
    @endif
    @if (session('error'))
        flashes.push({ type: 'error', message: @json(session('error')) });
    @endif
    @if (session('warning'))
        flashes.push({ type: 'warning', message: @json(session('warning')) });
    @endif

    if (flashes.length) {
        window.addEventListener('DOMContentLoaded', () => {
            flashes.forEach(showToast);
        });
    }
})();
</script>
