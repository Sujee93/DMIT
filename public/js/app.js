/*
 * Shoe Distribution - front-end behaviour.
 * Plain JavaScript, no build step. Loaded with `defer` on every page.
 * Never uses innerHTML with user-supplied data.
 */
(function () {
    'use strict';

    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

    /* ---------- Layout: sidebar, dropdown, alerts ---------- */
    function initLayout() {
        const sidebar = $('[data-sidebar]');
        const backdrop = $('[data-sidebar-backdrop]');
        const toggleSidebar = (open) => {
            sidebar?.classList.toggle('is-open', open);
            backdrop?.classList.toggle('is-open', open);
        };
        $('[data-sidebar-toggle]')?.addEventListener('click', () => toggleSidebar(!sidebar.classList.contains('is-open')));
        backdrop?.addEventListener('click', () => toggleSidebar(false));

        $$('[data-dropdown-toggle]').forEach((button) => {
            const menu = button.parentElement.querySelector('[data-dropdown]');
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                const open = !menu.classList.contains('is-open');
                menu.classList.toggle('is-open', open);
                button.setAttribute('aria-expanded', String(open));
            });
        });
        document.addEventListener('click', () => {
            $$('[data-dropdown].is-open').forEach((menu) => menu.classList.remove('is-open'));
        });

        $$('[data-dismiss]').forEach((button) => {
            button.addEventListener('click', () => button.closest('.alert')?.remove());
        });
        $$('[data-dismissable]').forEach((alert) => setTimeout(() => alert.remove(), 6000));
    }

    /* ---------- Forms: confirmations, double-submit guard, print ---------- */
    function initForms() {
        $$('form[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(form.dataset.confirm)) {
                    event.preventDefault();
                }
            });
        });

        $$('form[data-submit-once]').forEach((form) => {
            form.addEventListener('submit', () => {
                $$('button[type="submit"]', form).forEach((button) => {
                    // Defer so the button's own value is still submitted.
                    setTimeout(() => { button.disabled = true; }, 0);
                });
            });
        });

        $$('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));

        // Selects that reload the page with a query parameter (e.g. supplier balance lookup).
        $$('[data-navigate-param]').forEach((select) => {
            select.addEventListener('change', () => {
                const url = new URL(window.location.href);
                if (select.value) {
                    url.searchParams.set(select.dataset.navigateParam, select.value);
                } else {
                    url.searchParams.delete(select.dataset.navigateParam);
                }
                window.location.assign(url.toString());
            });
        });
    }

    /* ---------- Payment form: show cheque fields only for cheques ---------- */
    function initPaymentForms() {
        $$('[data-payment-form]').forEach((form) => {
            const method = $('[data-method]', form);
            const reference = $('[data-reference]', form);
            const update = () => {
                const isCheque = method.value === 'cheque';
                $$('.cheque-only', form).forEach((el) => el.classList.toggle('hidden', !isCheque));
                if (reference) {
                    reference.placeholder = isCheque ? 'Cheque number' : (method.value === 'card' ? 'Card slip / approval no.' : 'Optional');
                }
            };
            method?.addEventListener('change', update);
            update();
        });
    }

    /* ---------- Invoice builder ---------- */
    function initInvoiceForm() {
        const root = $('[data-invoice-form]');
        if (!root) return;

        const products = JSON.parse($('[data-products]', root).textContent || '[]');
        const byLabel = new Map(products.map((p) => [`${p.code} — ${p.name}`, p]));
        const byCode = new Map(products.map((p) => [String(p.code).toUpperCase(), p]));

        const tbody = $('[data-items]', root);
        const template = $('[data-row-template]', root);
        const search = $('[data-product-search]', root);
        const emptyState = $('[data-items-empty]', root);
        const discountInput = $('[data-discount]', root);
        let nextIndex = parseInt(tbody.dataset.nextIndex, 10) || 0;

        const toCents = (value) => Math.round((parseFloat(value) || 0) * 100);
        const format = (cents) => (cents / 100).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function findProduct(text) {
            const value = text.trim();
            if (!value) return null;
            if (byLabel.has(value)) return byLabel.get(value);
            if (byCode.has(value.toUpperCase())) return byCode.get(value.toUpperCase());
            const matches = products.filter((p) => `${p.code} ${p.name}`.toLowerCase().includes(value.toLowerCase()));
            return matches.length === 1 ? matches[0] : null;
        }

        function field(row, name) {
            return row.querySelector(`[data-field="${name}"]`);
        }

        function addProduct(product) {
            // Same product twice? Bump the quantity instead of adding a duplicate line.
            const existing = $$('[data-item-row]', tbody).find((row) => field(row, 'product_id').value === String(product.id));
            if (existing) {
                const qty = field(existing, 'quantity');
                qty.value = (parseInt(qty.value, 10) || 0) + 1;
                qty.focus();
                recalc();
                return;
            }

            const fragment = template.content.cloneNode(true);
            const row = fragment.querySelector('[data-item-row]');
            const index = String(nextIndex++);
            row.querySelectorAll('[name]').forEach((input) => {
                input.name = input.name.replace('__INDEX__', index);
            });
            row.dataset.defaultPrice = product.price;
            field(row, 'product_id').value = product.id;
            field(row, 'label').textContent = `${product.code} — ${product.name}`;
            field(row, 'description').value = product.description || '';
            field(row, 'quantity').value = 1;
            field(row, 'unit_cost').value = product.cost;
            field(row, 'unit_price').value = product.price;
            field(row, 'discount_percent').value = 0;
            tbody.appendChild(fragment);
            field(row, 'quantity').select();
            recalc();
        }

        function recalc() {
            let subtotal = 0;
            let cost = 0;
            const rows = $$('[data-item-row]', tbody);
            rows.forEach((row) => {
                const qty = parseInt(field(row, 'quantity').value, 10) || 0;
                const price = toCents(field(row, 'unit_price').value);
                const unitCost = toCents(field(row, 'unit_cost').value);
                const discount = Math.min(Math.max(parseFloat(field(row, 'discount_percent').value) || 0, 0), 100);
                // Same rounding as the server: whole cents after the line discount.
                const lineTotal = Math.round(qty * price * (100 - discount) / 100);
                subtotal += lineTotal;
                cost += qty * unitCost;
                field(row, 'line_total').textContent = format(lineTotal);

                const defaultPrice = row.dataset.defaultPrice;
                const changed = defaultPrice !== '' && defaultPrice !== undefined && toCents(defaultPrice) !== price;
                field(row, 'unit_price').classList.toggle('price-changed', changed);
                field(row, 'unit_price').title = changed ? `Default price: ${format(toCents(defaultPrice))}` : '';
            });

            const discount = Math.min(toCents(discountInput.value), subtotal);
            const total = subtotal - discount;
            $('[data-subtotal]', root).textContent = format(subtotal);
            $('[data-total]', root).textContent = format(total);
            $('[data-cost]', root).textContent = format(cost);
            $('[data-profit]', root).textContent = format(total - cost);
            emptyState.classList.toggle('hidden', rows.length > 0);
        }

        function addFromSearch() {
            const product = findProduct(search.value);
            if (!product) {
                search.classList.add('is-invalid');
                return;
            }
            search.classList.remove('is-invalid');
            search.value = '';
            addProduct(product);
        }

        search.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                addFromSearch();
            }
        });
        search.addEventListener('input', () => {
            search.classList.remove('is-invalid');
            // Picking from the datalist fills the exact label: add immediately.
            if (byLabel.has(search.value)) addFromSearch();
        });
        $('[data-add-product]', root).addEventListener('click', addFromSearch);

        tbody.addEventListener('input', recalc);
        tbody.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-row]');
            if (button) {
                button.closest('[data-item-row]').remove();
                recalc();
            }
        });
        discountInput.addEventListener('input', recalc);

        // Stop Enter in item inputs from submitting the whole invoice.
        root.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && event.target.matches('input:not([type="submit"])')) {
                event.preventDefault();
            }
        });

        recalc();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initLayout();
        initForms();
        initPaymentForms();
        initInvoiceForm();
    });
})();
