// ══════════════════════════════════════════════════════════
//  SIKEMA — app.js
//  Interaktivitas: sidebar, modal, alert, delete confirm
// ══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar mobile toggle ──────────────────────────────
    const sidebarToggle   = document.getElementById('sidebar-toggle');
    const sidebarEl       = document.getElementById('app-sidebar');
    const sidebarOverlay  = document.getElementById('sidebar-overlay');

    function openSidebar()  { sidebarEl?.classList.remove('-translate-x-full'); sidebarOverlay?.classList.remove('hidden'); }
    function closeSidebar() { sidebarEl?.classList.add('-translate-x-full');    sidebarOverlay?.classList.add('hidden'); }

    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);

    // ── Submenu collapse ───────────────────────────────────
    document.querySelectorAll('[data-submenu-toggle]').forEach(btn => {
        const targetId = btn.dataset.submenuToggle;
        const target   = document.getElementById(targetId);
        const icon     = btn.querySelector('[data-chevron]');

        // auto-open if a child is active
        if (target?.querySelector('.nav-sub-active')) {
            target.classList.remove('hidden');
            icon?.classList.add('rotate-0');
            icon?.classList.remove('-rotate-90');
        }

        btn.addEventListener('click', () => {
            const isHidden = target?.classList.toggle('hidden');
            icon?.classList.toggle('-rotate-90', isHidden);
            icon?.classList.toggle('rotate-0', !isHidden);
        });
    });

    // ── Generic Modal open/close ───────────────────────────
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        const modal = document.getElementById(btn.dataset.modalOpen);
        btn.addEventListener('click', () => {
            modal?.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        const modal = document.getElementById(btn.dataset.modalClose);
        btn.addEventListener('click', () => {
            modal?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    });

    // Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', e => {
            if (e.target === backdrop) {
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });

    // ── Delete confirmation ────────────────────────────────
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const msg  = btn.dataset.confirmDelete || 'Yakin ingin menghapus data ini?';
            const form = document.getElementById(btn.dataset.formId);
            if (confirm(msg)) form?.submit();
        });
    });

    // ── Auto-dismiss alert ─────────────────────────────────
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const ms = parseInt(el.dataset.autoDismiss) || 4000;
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, ms);
    });

    // ── Inline edit modal pre-fill ─────────────────────────
    document.querySelectorAll('[data-edit-fill]').forEach(btn => {
        btn.addEventListener('click', () => {
            const data   = JSON.parse(btn.dataset.editFill);
            const formId = btn.dataset.editForm;
            const form   = document.getElementById(formId);
            if (!form) return;
            Object.entries(data).forEach(([key, val]) => {
                const el = form.querySelector(`[name="${key}"]`);
                if (el) el.value = val;
            });
            const modal = document.getElementById(btn.dataset.modalOpen);
            modal?.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    });

    // ── Format angka Rupiah (input) ────────────────────────
    document.querySelectorAll('[data-rupiah]').forEach(input => {
        input.addEventListener('input', e => {
            let val = e.target.value.replace(/\D/g, '');
            e.target.value = val;
        });
    });

    // ── Toggle password visibility ─────────────────────────
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        const targetId = btn.dataset.togglePassword;
        const input    = document.getElementById(targetId);
        btn.addEventListener('click', () => {
            const isPassword = input?.type === 'password';
            if (input) input.type = isPassword ? 'text' : 'password';
            // swap icon text
            btn.querySelectorAll('[data-eye]').forEach(el => el.classList.toggle('hidden'));
        });
    });

    // ── Penerimaan: checkbox tagihan & hitung total ────────
    const tagihanForm = document.getElementById('form-penerimaan');
    if (tagihanForm) {
        const checkboxes = tagihanForm.querySelectorAll('[data-tagihan-nominal]');
        const totalEl    = document.getElementById('total-bayar-display');
        const totalInput = document.getElementById('total-bayar-input');

        function hitungTotal() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) total += parseInt(cb.dataset.tagihanNominal) || 0;
            });
            if (totalEl)    totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            if (totalInput) totalInput.value = total;
        }

        checkboxes.forEach(cb => cb.addEventListener('change', hitungTotal));
        hitungTotal();
    }

    // ── Print kwitansi ─────────────────────────────────────
    document.getElementById('btn-print')?.addEventListener('click', () => window.print());

});
