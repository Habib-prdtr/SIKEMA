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
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // red-500
                    cancelButtonColor: '#6b7280', // gray-500
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form?.submit();
                    }
                });
            } else {
                if (confirm(msg)) form?.submit();
            }
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

    // ── Realtime AJAX Search & Auto-Filter Global (Tanpa Reload) ──
    let realtimeSearchTimer = null;

    function rebindDynamicButtons() {
        if (typeof window.bindPilihSiswaButtons === 'function') window.bindPilihSiswaButtons();
        if (typeof window.bindCatatDashboardButtons === 'function') window.bindCatatDashboardButtons();
    }

    function updatePageContentFromHtml(htmlText, requestUrl, activeInputElement = null) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');

        // Check for specific search result containers first
        const specificIds = ['dash-siswa-search-results', 'catat-siswa-search-results'];
        let updatedSpecific = false;

        specificIds.forEach(id => {
            const currentEl = document.getElementById(id);
            const newEl = doc.getElementById(id);
            if (currentEl && newEl) {
                currentEl.outerHTML = newEl.outerHTML;
                updatedSpecific = true;
            }
        });

        if (!updatedSpecific) {
            const currentMain = document.querySelector('main');
            const newMain = doc.querySelector('main');

            if (currentMain && newMain) {
                const currentCards = Array.from(currentMain.querySelectorAll('.card'));
                const newCards = Array.from(newMain.querySelectorAll('.card'));

                currentCards.forEach((card, idx) => {
                    const isFormCard = activeInputElement && card.contains(activeInputElement);

                    if (isFormCard) {
                        const currentTableWrapper = card.querySelector('.table-wrapper, table');
                        const newTableWrapper = newCards[idx]?.querySelector('.table-wrapper, table');

                        if (currentTableWrapper && newTableWrapper) {
                            currentTableWrapper.outerHTML = newTableWrapper.outerHTML;
                        }
                    } else if (newCards[idx]) {
                        card.outerHTML = newCards[idx].outerHTML;
                    }
                });
            }
        }

        // Update URL bar seamlessly without reload
        if (requestUrl) {
            window.history.replaceState(null, '', requestUrl);
        }

        rebindDynamicButtons();
    }

    function performRealtimeAjaxSearch(form, inputElement = null) {
        if (!form || form.getAttribute('method')?.toUpperCase() !== 'GET') return;

        const actionUrl = form.getAttribute('action') || window.location.pathname;
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value !== '') {
                params.append(key, value);
            }
        }

        const queryString = params.toString();
        const requestUrl = actionUrl + (queryString ? '?' + queryString : '');

        if (inputElement) {
            inputElement.classList.add('bg-emerald-50/40');
        }

        fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/xhtml+xml, */*'
            }
        })
        .then(response => response.text())
        .then(htmlText => {
            updatePageContentFromHtml(htmlText, requestUrl, inputElement);
        })
        .catch(err => console.error('Realtime AJAX search error:', err))
        .finally(() => {
            if (inputElement) {
                inputElement.classList.remove('bg-emerald-50/40');
            }
        });
    }

    // Realtime typing search on GET forms (Debounced 300ms, NO RELOAD)
    document.addEventListener('input', (e) => {
        const target = e.target;
        if (!target) return;

        const isSearchInput = target.matches('form[method="GET"] input[name="cari"], form[method="GET"] input[name="q"], form[method="GET"] input[name="search"], form[method="GET"] input[type="text"], form[method="GET"] input[type="search"], form[method="GET"] input[type="number"]');

        if (isSearchInput) {
            const form = target.closest('form');
            if (!form || form.getAttribute('method')?.toUpperCase() !== 'GET') return;

            clearTimeout(realtimeSearchTimer);
            realtimeSearchTimer = setTimeout(() => {
                performRealtimeAjaxSearch(form, target);
            }, 300);
        }
    });

    // Realtime dropdown filter on GET forms (Instant, NO RELOAD)
    document.addEventListener('change', (e) => {
        const target = e.target;
        if (!target) return;

        if (target.matches('form[method="GET"] select')) {
            const form = target.closest('form');
            if (!form || form.getAttribute('method')?.toUpperCase() !== 'GET') return;

            if (target.closest('.modal-backdrop') || target.hasAttribute('data-no-auto-submit')) return;

            performRealtimeAjaxSearch(form, null);
        }
    });

});
