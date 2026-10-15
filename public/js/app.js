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
            const amount = $('[data-amount]', form);
            const picker = $('[data-cheque-picker]', form);
            const chequeBoxes = picker ? $$('input[type="checkbox"]', picker) : [];

            const chequeTotal = () => {
                const cents = chequeBoxes.filter((box) => box.checked).reduce((sum, box) => sum + toCents(box.dataset.chequeAmount), 0);
                $('[data-cheque-total]', form).textContent = formatCents(cents);
            };

            const update = () => {
                const isCheque = method.value === 'cheque';
                const usesCustomerCheques = method.value === 'customer_cheque';

                $$('.cheque-only', form).forEach((el) => el.classList.toggle('hidden', !isCheque));
                $$('.customer-cheque-only', form).forEach((el) => el.classList.toggle('hidden', !usesCustomerCheques));
                $$('.not-customer-cheque', form).forEach((el) => el.classList.toggle('hidden', usesCustomerCheques));

                // Hidden inputs must not be submitted or block the browser's validation.
                if (amount) {
                    amount.disabled = usesCustomerCheques;
                    amount.required = !usesCustomerCheques;
                }
                if (reference) {
                    reference.disabled = usesCustomerCheques;
                    reference.placeholder = isCheque ? 'Cheque number' : (method.value === 'card' ? 'Card slip / approval no.' : 'Optional');
                }
                chequeBoxes.forEach((box) => { box.disabled = !usesCustomerCheques; });
            };

            method?.addEventListener('change', update);
            chequeBoxes.forEach((box) => box.addEventListener('change', chequeTotal));
            update();
            if (picker) chequeTotal();
        });
    }

    /* ---------- Line builders (invoice & GRN) ---------- */
    const toCents = (value) => Math.round((parseFloat(value) || 0) * 100);
    const formatCents = (cents) => (cents / 100).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const field = (row, name) => row.querySelector(`[data-field="${name}"]`);

    /**
     * Shared behaviour for forms that build a list of product lines:
     * search box + datalist, add / bump quantity, remove, and recalculation.
     * fillRow(row, product) fills a freshly added row; recalc() updates totals.
     */
    function lineBuilder(root, fillRow, recalc) {
        const products = JSON.parse($('[data-products]', root).textContent || '[]');
        const byLabel = new Map(products.map((p) => [`${p.code} — ${p.name}`, p]));
        const byCode = new Map(products.map((p) => [String(p.code).toUpperCase(), p]));

        const tbody = $('[data-items]', root);
        const template = $('[data-row-template]', root);
        const search = $('[data-product-search]', root);
        let nextIndex = parseInt(tbody.dataset.nextIndex, 10) || 0;

        function findProduct(text) {
            const value = text.trim();
            if (!value) return null;
            if (byLabel.has(value)) return byLabel.get(value);
            if (byCode.has(value.toUpperCase())) return byCode.get(value.toUpperCase());
            const matches = products.filter((p) => `${p.code} ${p.name}`.toLowerCase().includes(value.toLowerCase()));
            return matches.length === 1 ? matches[0] : null;
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
            field(row, 'product_id').value = product.id;
            field(row, 'label').textContent = `${product.code} — ${product.name}`;
            field(row, 'quantity').value = 1;
            fillRow(row, product);
            tbody.appendChild(fragment);
            field(row, 'quantity').select();
            recalc();
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
        $('[data-discount]', root)?.addEventListener('input', recalc);

        // Stop Enter in line inputs from submitting the whole form.
        root.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && event.target.matches('input:not([type="submit"])')) {
                event.preventDefault();
            }
        });

        recalc();

        return { rows: () => $$('[data-item-row]', tbody) };
    }

    function initInvoiceForm() {
        const root = $('[data-invoice-form]');
        if (!root) return;

        const emptyState = $('[data-items-empty]', root);
        const discountInput = $('[data-discount]', root);
        let builder;

        function recalc() {
            if (!builder) return;
            let subtotal = 0;
            let grossTotal = 0;
            let lineDiscounts = 0;
            let cost = 0;
            const rows = builder.rows();
            rows.forEach((row) => {
                const qty = parseInt(field(row, 'quantity').value, 10) || 0;
                const price = toCents(field(row, 'unit_price').value);
                const unitCost = toCents(field(row, 'unit_cost').value);
                const gross = qty * price;
                const lineDiscount = Math.min(Math.max(toCents(field(row, 'discount').value), 0), gross);
                const lineTotal = gross - lineDiscount;
                subtotal += lineTotal;
                grossTotal += gross;
                lineDiscounts += lineDiscount;
                cost += qty * unitCost;
                field(row, 'line_total').textContent = formatCents(lineTotal);

                const defaultPrice = row.dataset.defaultPrice;
                const changed = defaultPrice !== '' && defaultPrice !== undefined && toCents(defaultPrice) !== price;
                field(row, 'unit_price').classList.toggle('price-changed', changed);
                field(row, 'unit_price').title = changed ? `Default price: ${formatCents(toCents(defaultPrice))}` : '';

                // Stock check (the server enforces it too).
                const available = row.dataset.available;
                if (available !== '' && available !== undefined) {
                    const short = qty > parseInt(available, 10);
                    const hint = field(row, 'stock');
                    hint.textContent = short ? `Only ${available} in stock` : `${available} available`;
                    hint.classList.toggle('is-short', short);
                    field(row, 'quantity').classList.toggle('is-invalid', short);
                }
            });

            const discount = Math.min(toCents(discountInput.value), subtotal);
            const total = subtotal - discount;
            $('[data-gross]', root).textContent = formatCents(grossTotal);
            $('[data-line-discounts]', root).textContent = formatCents(lineDiscounts);
            $('[data-total-discount]', root).textContent = formatCents(lineDiscounts + discount);
            $('[data-total]', root).textContent = formatCents(total);
            $('[data-cost]', root).textContent = formatCents(cost);
            $('[data-profit]', root).textContent = formatCents(total - cost);
            emptyState.classList.toggle('hidden', rows.length > 0);
        }

        builder = lineBuilder(root, (row, product) => {
            row.dataset.defaultPrice = product.price;
            row.dataset.available = product.available;
            field(row, 'description').value = product.description || '';
            field(row, 'unit_cost').value = product.cost;
            field(row, 'unit_price').value = product.price;
            field(row, 'discount').value = '0.00';
        }, recalc);
        recalc();
    }

    function initGrnForm() {
        const root = $('[data-grn-form]');
        if (!root) return;

        const emptyState = $('[data-items-empty]', root);
        const discountInput = $('[data-discount]', root);
        let builder;

        function recalc() {
            if (!builder) return;
            let subtotal = 0;
            let units = 0;
            const rows = builder.rows();
            rows.forEach((row) => {
                const qty = parseInt(field(row, 'quantity').value, 10) || 0;
                const lineTotal = qty * toCents(field(row, 'unit_cost').value);
                units += qty;
                subtotal += lineTotal;
                field(row, 'line_total').textContent = formatCents(lineTotal);
            });

            const discount = Math.min(toCents(discountInput.value), subtotal);
            $('[data-units]', root).textContent = units.toLocaleString();
            $('[data-subtotal]', root).textContent = formatCents(subtotal);
            $('[data-total]', root).textContent = formatCents(subtotal - discount);
            emptyState.classList.toggle('hidden', rows.length > 0);
        }

        builder = lineBuilder(root, (row, product) => {
            field(row, 'unit_cost').value = product.cost;
            field(row, 'stock').textContent = `In stock now: ${product.stock}`;
        }, recalc);
        recalc();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initLayout();
        initForms();
        initPaymentForms();
        initInvoiceForm();
        initGrnForm();
    });
})();
