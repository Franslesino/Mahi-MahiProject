<div id="globalDeleteModal" class="hidden fixed inset-0 bg-black/70 z-[1000] flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-6 text-center relative overflow-hidden">
        <div class="absolute -top-10 -left-12 w-28 h-28 bg-red-100 rounded-full opacity-60"></div>
        <div class="absolute -bottom-16 -right-14 w-32 h-32 bg-orange-100 rounded-full opacity-60"></div>
        <div class="relative z-10 space-y-3">
            <div class="w-14 h-14 mx-auto bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl shadow">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 id="globalDeleteTitle" class="text-xl font-bold text-gray-900">Hapus item ini?</h3>
            <p id="globalDeleteMessage" class="text-sm text-gray-600">Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex flex-col gap-2 mt-3">
                <button id="globalDeleteConfirm" class="w-full px-4 py-3 bg-red-600 text-white rounded-2xl font-semibold hover:bg-red-700 transition">
                    Ya, hapus
                </button>
                <button id="globalDeleteCancel" class="w-full px-4 py-3 bg-gray-100 text-gray-800 rounded-2xl font-semibold hover:bg-gray-200 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('globalDeleteModal');
    if (!modal) return;

    const titleEl = document.getElementById('globalDeleteTitle');
    const messageEl = document.getElementById('globalDeleteMessage');
    const confirmBtn = document.getElementById('globalDeleteConfirm');
    const cancelBtn = document.getElementById('globalDeleteCancel');
    let targetForm = null;

    const openModal = (form) => {
        targetForm = form;
        const customMessage = form.getAttribute('data-confirm') || 'Tindakan ini tidak bisa dibatalkan.';
        const customTitle = form.getAttribute('data-confirm-title') || 'Hapus item ini?';
        if (titleEl) titleEl.textContent = customTitle;
        if (messageEl) messageEl.textContent = customMessage;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        confirmBtn?.focus();
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        targetForm = null;
    };

    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (form.dataset.skipConfirm === '1') return;
            e.preventDefault();
            openModal(form);
        });
    });

    confirmBtn?.addEventListener('click', () => {
        if (!targetForm) return;
        targetForm.dataset.skipConfirm = '1';
        targetForm.submit();
        delete targetForm.dataset.skipConfirm;
        closeModal();
    });

    cancelBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/components/delete-modal.blade.php ENDPATH**/ ?>