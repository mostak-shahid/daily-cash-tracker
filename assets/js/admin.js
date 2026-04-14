/* Daily Cash Tracker — Admin JS */
(function ($) {
    'use strict';

    const DCT_APP = {

        notify(el, msg, type) {
            const $el = $(el);
            $el.removeClass('dct-success dct-error').addClass('dct-' + type).html(msg).show();
            if (type === 'success') setTimeout(() => $el.fadeOut(400), 3000);
        },

        ajax(action, data, cb) {
            $.post(DCT.ajax_url, $.extend({ action, nonce: DCT.nonce }, data), function (res) {
                cb(res);
            }).fail(() => cb({ success: false, data: 'Server error.' }));
        },

        /* ── FORMAT ── */
        money(n) {
            return parseFloat(n || 0).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        /* ════════════════ PROJECTS ════════════════ */

        Projects: {
            $list: null,
            data: [],

            init() {
                this.$list = $('#dct-projects-list');
                this.load();
                this.bindForm();
            },

            load() {
                DCT_APP.ajax('dct_get_projects', {}, (res) => {
                    if (!res.success) return;
                    this.data = res.data;
                    this.render();
                });
            },

            render() {
                if (!this.data.length) {
                    this.$list.html('<tr><td colspan="4" class="text-center" style="padding:30px;color:#64748b;">No projects yet.</td></tr>');
                    return;
                }
                let html = '';
                this.data.forEach(p => {
                    html += `<tr>
                        <td><strong>${this.esc(p.name)}</strong></td>
                        <td>${this.esc(p.address)}</td>
                        <td>${this.esc(p.description)}</td>
                        <td><div class="actions">
                            <button class="dct-btn dct-btn-outline dct-btn-sm" onclick="DCT_APP.Projects.edit(${p.id})">Edit</button>
                            <button class="dct-btn dct-btn-danger dct-btn-sm" onclick="DCT_APP.Projects.del(${p.id})">Delete</button>
                        </div></td>
                    </tr>`;
                });
                this.$list.html(html);
            },

            esc(str) { return $('<div>').text(str || '').html(); },

            bindForm() {
                $('#dct-project-form').on('submit', (e) => {
                    e.preventDefault();
                    const $btn = $('#dct-project-form .dct-btn-accent');
                    $btn.prop('disabled', true);
                    const data = {
                        id:          $('#dct-project-id').val(),
                        name:        $('#dct-project-name').val().trim(),
                        address:     $('#dct-project-address').val().trim(),
                        description: $('#dct-project-desc').val().trim(),
                    };
                    DCT_APP.ajax('dct_save_project', data, (res) => {
                        $btn.prop('disabled', false);
                        DCT_APP.notify('#dct-project-notice', res.data.message || res.data, res.success ? 'success' : 'error');
                        if (res.success) { this.resetForm(); this.load(); }
                    });
                });
                $('#dct-project-cancel').on('click', () => this.resetForm());
            },

            edit(id) {
                const p = this.data.find(x => x.id == id);
                if (!p) return;
                $('#dct-project-id').val(p.id);
                $('#dct-project-name').val(p.name);
                $('#dct-project-address').val(p.address);
                $('#dct-project-desc').val(p.description);
                $('#dct-project-cancel').show();
                $('html, body').animate({ scrollTop: $('#dct-project-form').offset().top - 80 }, 300);
            },

            del(id) {
                if (!confirm('Delete this project? All transactions will also be deleted.')) return;
                DCT_APP.ajax('dct_delete_project', { id }, (res) => {
                    DCT_APP.notify('#dct-project-notice', res.data.message || res.data, res.success ? 'success' : 'error');
                    if (res.success) this.load();
                });
            },

            resetForm() {
                $('#dct-project-form')[0].reset();
                $('#dct-project-id').val('');
                $('#dct-project-cancel').hide();
            }
        },

        /* ════════════════ STAKEHOLDERS ════════════════ */

        Stakeholders: {
            $list: null,
            data: [],

            init() {
                this.$list = $('#dct-stakeholders-list');
                this.load();
                this.bindForm();
            },

            load() {
                DCT_APP.ajax('dct_get_stakeholders', {}, (res) => {
                    if (!res.success) return;
                    this.data = res.data;
                    this.render();
                });
            },

            render() {
                if (!this.data.length) {
                    this.$list.html('<tr><td colspan="5" class="text-center" style="padding:30px;color:#64748b;">No stakeholders yet.</td></tr>');
                    return;
                }
                let html = '';
                this.data.forEach(s => {
                    html += `<tr>
                        <td><strong>${this.esc(s.name)}</strong></td>
                        <td>${this.esc(s.phone)}</td>
                        <td>${this.esc(s.address)}</td>
                        <td>${this.esc(s.description)}</td>
                        <td><div class="actions">
                            <button class="dct-btn dct-btn-outline dct-btn-sm" onclick="DCT_APP.Stakeholders.edit(${s.id})">Edit</button>
                            <button class="dct-btn dct-btn-danger dct-btn-sm" onclick="DCT_APP.Stakeholders.del(${s.id})">Delete</button>
                        </div></td>
                    </tr>`;
                });
                this.$list.html(html);
            },

            esc(str) { return $('<div>').text(str || '').html(); },

            bindForm() {
                $('#dct-stakeholder-form').on('submit', (e) => {
                    e.preventDefault();
                    const $btn = $('#dct-stakeholder-form .dct-btn-accent');
                    $btn.prop('disabled', true);
                    const data = {
                        id:          $('#dct-stakeholder-id').val(),
                        name:        $('#dct-sh-name').val().trim(),
                        phone:       $('#dct-sh-phone').val().trim(),
                        address:     $('#dct-sh-address').val().trim(),
                        description: $('#dct-sh-desc').val().trim(),
                    };
                    DCT_APP.ajax('dct_save_stakeholder', data, (res) => {
                        $btn.prop('disabled', false);
                        DCT_APP.notify('#dct-stakeholder-notice', res.data.message || res.data, res.success ? 'success' : 'error');
                        if (res.success) { this.resetForm(); this.load(); }
                    });
                });
                $('#dct-stakeholder-cancel').on('click', () => this.resetForm());
            },

            edit(id) {
                const s = this.data.find(x => x.id == id);
                if (!s) return;
                $('#dct-stakeholder-id').val(s.id);
                $('#dct-sh-name').val(s.name);
                $('#dct-sh-phone').val(s.phone);
                $('#dct-sh-address').val(s.address);
                $('#dct-sh-desc').val(s.description);
                $('#dct-stakeholder-cancel').show();
                $('html, body').animate({ scrollTop: $('#dct-stakeholder-form').offset().top - 80 }, 300);
            },

            del(id) {
                if (!confirm('Delete this stakeholder?')) return;
                DCT_APP.ajax('dct_delete_stakeholder', { id }, (res) => {
                    DCT_APP.notify('#dct-stakeholder-notice', res.data.message || res.data, res.success ? 'success' : 'error');
                    if (res.success) this.load();
                });
            },

            resetForm() {
                $('#dct-stakeholder-form')[0].reset();
                $('#dct-stakeholder-id').val('');
                $('#dct-stakeholder-cancel').hide();
            }
        },

        /* ════════════════ ASSIGN ════════════════ */

        Assign: {
            projects: [],
            stakeholders: [],
            assigned: [],
            currentProject: null,

            init() {
                this.loadAll();
                $('#dct-assign-project').on('change', () => {
                    this.currentProject = $('#dct-assign-project').val();
                    this.loadAssigned();
                });
                $('#dct-assign-btn').on('click', () => this.assign());
            },

            loadAll() {
                DCT_APP.ajax('dct_get_projects', {}, (r) => {
                    if (!r.success) return;
                    this.projects = r.data;
                    const $sel = $('#dct-assign-project');
                    $sel.empty().append('<option value="">— Select Project —</option>');
                    r.data.forEach(p => $sel.append(`<option value="${p.id}">${$('<div>').text(p.name).html()}</option>`));
                });
                DCT_APP.ajax('dct_get_stakeholders', {}, (r) => {
                    if (!r.success) return;
                    this.stakeholders = r.data;
                    const $sel = $('#dct-assign-stakeholder');
                    $sel.empty().append('<option value="">— Select Stakeholder —</option>');
                    r.data.forEach(s => $sel.append(`<option value="${s.id}">${$('<div>').text(s.name).html()}</option>`));
                });
            },

            loadAssigned() {
                if (!this.currentProject) { $('#dct-assigned-wrap').empty(); return; }
                DCT_APP.ajax('dct_get_project_stakeholders', { project_id: this.currentProject }, (r) => {
                    if (!r.success) return;
                    this.assigned = r.data;
                    this.renderAssigned();
                });
            },

            renderAssigned() {
                const $wrap = $('#dct-assigned-wrap');
                if (!this.assigned.length) { $wrap.html('<span style="color:#64748b;font-size:13px;">No stakeholders assigned yet.</span>'); return; }
                let html = '<div class="dct-assign-grid">';
                this.assigned.forEach(s => {
                    html += `<span class="dct-tag">${$('<div>').text(s.name).html()}
                        <button class="remove" onclick="DCT_APP.Assign.unassign(${s.id})" title="Remove">×</button>
                    </span>`;
                });
                html += '</div>';
                $wrap.html(html);
            },

            assign() {
                const pid = $('#dct-assign-project').val();
                const sid = $('#dct-assign-stakeholder').val();
                if (!pid || !sid) { DCT_APP.notify('#dct-assign-notice', 'Please select both project and stakeholder.', 'error'); return; }
                DCT_APP.ajax('dct_assign_stakeholder', { project_id: pid, stakeholder_id: sid }, (r) => {
                    DCT_APP.notify('#dct-assign-notice', r.data.message || r.data, r.success ? 'success' : 'error');
                    if (r.success) this.loadAssigned();
                });
            },

            unassign(sid) {
                const pid = this.currentProject;
                if (!pid) return;
                DCT_APP.ajax('dct_unassign_stakeholder', { project_id: pid, stakeholder_id: sid }, (r) => {
                    DCT_APP.notify('#dct-assign-notice', r.data.message || r.data, r.success ? 'success' : 'error');
                    if (r.success) this.loadAssigned();
                });
            }
        },

        /* ════════════════ TRANSACTIONS ════════════════ */

        Transactions: {
            data: [],
            projects: [],
            allStakeholders: [],

            init() {
                this.loadProjects();
                this.bindForm();
                this.bindFilters();
                this.load({});
                $('#dct-txn-type').on('change', () => this.typeToggle());
                this.typeToggle();
            },

            typeToggle() {
                const type = $('#dct-txn-type').val();
                if (type === 'expense') {
                    $('#dct-txn-to-wrap').hide();
                    $('#dct-txn-from-label').text('Paid By *');
                } else {
                    $('#dct-txn-to-wrap').show();
                    $('#dct-txn-from-label').text('From (Giver)');
                }
            },

            loadProjects() {
                DCT_APP.ajax('dct_get_projects', {}, (r) => {
                    if (!r.success) return;
                    this.projects = r.data;
                    ['#dct-txn-project', '#dct-filter-project'].forEach(sel => {
                        const $s = $(sel);
                        const hasAll = sel === '#dct-filter-project';
                        $s.empty().append(`<option value="">${hasAll ? 'All Projects' : '— Select Project —'}</option>`);
                        r.data.forEach(p => $s.append(`<option value="${p.id}">${$('<div>').text(p.name).html()}</option>`));
                    });
                });
                DCT_APP.ajax('dct_get_stakeholders', {}, (r) => {
                    if (!r.success) return;
                    this.allStakeholders = r.data;
                });
            },

            populateStakeholders(projectId, selIds = []) {
                const holders = projectId
                    ? this.allStakeholders  /* could filter by project here */
                    : this.allStakeholders;

                const opts = '<option value="">— None —</option>' +
                    holders.map(s => `<option value="${s.id}">${$('<div>').text(s.name).html()}</option>`).join('');
                $('#dct-txn-from, #dct-txn-to').html(opts);
                selIds.forEach((v, i) => { ['#dct-txn-from', '#dct-txn-to'][i] && $((['#dct-txn-from', '#dct-txn-to'][i])).val(v); });
            },

            bindForm() {
                $('#dct-txn-project').on('change', function () {
                    DCT_APP.Transactions.populateStakeholders($(this).val());
                });

                $('#dct-txn-date').val(new Date().toISOString().split('T')[0]);

                $('#dct-txn-form').on('submit', (e) => {
                    e.preventDefault();
                    const $btn = $('#dct-txn-form .dct-btn-accent');
                    $btn.prop('disabled', true);
                    const data = {
                        project_id:          $('#dct-txn-project').val(),
                        from_stakeholder_id: $('#dct-txn-from').val(),
                        to_stakeholder_id:   $('#dct-txn-to').val(),
                        transaction_type:    $('#dct-txn-type').val(),
                        amount:              $('#dct-txn-amount').val(),
                        description:         $('#dct-txn-desc').val().trim(),
                        transaction_date:    $('#dct-txn-date').val(),
                    };
                    DCT_APP.ajax('dct_save_transaction', data, (res) => {
                        $btn.prop('disabled', false);
                        DCT_APP.notify('#dct-txn-notice', res.data.message || res.data, res.success ? 'success' : 'error');
                        if (res.success) { $('#dct-txn-form')[0].reset(); $('#dct-txn-date').val(new Date().toISOString().split('T')[0]); this.load({}); }
                    });
                });
            },

            bindFilters() {
                $('#dct-filter-btn').on('click', () => {
                    this.load({
                        project_id: $('#dct-filter-project').val(),
                    });
                });
            },

            load(filters) {
                DCT_APP.ajax('dct_get_transactions', filters, (res) => {
                    if (!res.success) return;
                    this.data = res.data;
                    this.render();
                });
            },

            render() {
                const $list = $('#dct-txn-list');
                if (!this.data.length) {
                    $list.html('<tr><td colspan="7" class="text-center" style="padding:30px;color:#64748b;">No transactions found.</td></tr>');
                    return;
                }
                let html = '';
                this.data.forEach(t => {
                    const badge = t.transaction_type === 'expense'
                        ? '<span class="dct-badge dct-badge-expense">Expense</span>'
                        : '<span class="dct-badge dct-badge-transfer">Transfer</span>';
                    const from = t.from_name || '—';
                    const to   = t.transaction_type === 'expense' ? '<em>Bill/Expense</em>' : (t.to_name || '—');
                    const amtCls = t.transaction_type === 'expense' ? 'dct-amount-out' : 'dct-amount-in';
                    html += `<tr>
                        <td>${t.transaction_date}</td>
                        <td>${$('<div>').text(t.project_name || '').html()}</td>
                        <td>${badge}</td>
                        <td>${$('<div>').text(from).html()}</td>
                        <td>${to}</td>
                        <td class="${amtCls}">৳ ${DCT_APP.money(t.amount)}</td>
                        <td>${$('<div>').text(t.description || '').html()}</td>
                        <td><button class="dct-btn dct-btn-danger dct-btn-sm" onclick="DCT_APP.Transactions.del(${t.id})">Del</button></td>
                    </tr>`;
                });
                $list.html(html);
            },

            del(id) {
                if (!confirm('Delete this transaction?')) return;
                DCT_APP.ajax('dct_delete_transaction', { id }, (res) => {
                    DCT_APP.notify('#dct-txn-notice', res.data.message || res.data, res.success ? 'success' : 'error');
                    if (res.success) this.load({});
                });
            }
        },

        /* ════════════════ SUMMARY ════════════════ */

        Summary: {
            init() {
                this.loadSelects();
                $('#dct-summary-btn').on('click', () => this.load());
                $('#dct-all-summary-btn').on('click', () => this.loadAll());
            },

            loadSelects() {
                DCT_APP.ajax('dct_get_stakeholders', {}, (r) => {
                    if (!r.success) return;
                    const $s = $('#dct-summary-stakeholder');
                    $s.empty().append('<option value="">— Select Stakeholder —</option>');
                    r.data.forEach(s => $s.append(`<option value="${s.id}">${$('<div>').text(s.name).html()}</option>`));
                });
                DCT_APP.ajax('dct_get_projects', {}, (r) => {
                    if (!r.success) return;
                    ['#dct-summary-project', '#dct-all-summary-project'].forEach(sel => {
                        const $s = $(sel);
                        $s.empty().append('<option value="">All Projects</option>');
                        r.data.forEach(p => $s.append(`<option value="${p.id}">${$('<div>').text(p.name).html()}</option>`));
                    });
                });
            },

            load() {
                const sid = $('#dct-summary-stakeholder').val();
                const pid = $('#dct-summary-project').val();
                if (!sid) { DCT_APP.notify('#dct-summary-notice', 'Please select a stakeholder.', 'error'); return; }
                DCT_APP.ajax('dct_get_summary', { stakeholder_id: sid, project_id: pid }, (res) => {
                    if (!res.success) { DCT_APP.notify('#dct-summary-notice', res.data, 'error'); return; }
                    this.renderSingle(res.data);
                });
            },

            renderSingle(d) {
                const sh = d.stakeholder;
                const initial = sh.name.charAt(0).toUpperCase();
                const balCls  = d.balance >= 0 ? 'positive' : 'negative';

                let html = `
                <div class="dct-profile">
                    <div class="dct-avatar">${initial}</div>
                    <div class="dct-profile-info">
                        <h3>${$('<div>').text(sh.name).html()}</h3>
                        <p>${$('<div>').text(sh.phone || '').html()} ${sh.address ? '· ' + $('<div>').text(sh.address).html() : ''}</p>
                    </div>
                </div>
                <div class="dct-grid-3" style="margin-bottom:24px;">
                    <div class="dct-stat green">
                        <div class="dct-stat-label">Total Received</div>
                        <div class="dct-stat-value positive">৳ ${DCT_APP.money(d.received)}</div>
                    </div>
                    <div class="dct-stat red">
                        <div class="dct-stat-label">Total Given Out</div>
                        <div class="dct-stat-value negative">৳ ${DCT_APP.money(d.given)}</div>
                    </div>
                    <div class="dct-stat orange">
                        <div class="dct-stat-label">Expenses Paid</div>
                        <div class="dct-stat-value" style="color:var(--dct-accent)">৳ ${DCT_APP.money(d.expenses)}</div>
                    </div>
                </div>
                <div class="dct-stat blue" style="margin-bottom:24px;">
                    <div class="dct-stat-label">Net Balance (Received − Given − Expenses)</div>
                    <div class="dct-stat-value ${balCls}">৳ ${DCT_APP.money(d.balance)}</div>
                </div>`;

                if (d.transactions && d.transactions.length) {
                    html += `<div class="dct-card-title">Transaction History</div>
                    <div class="dct-table-wrap"><table class="dct-table">
                    <thead><tr><th>Date</th><th>Project</th><th>Type</th><th>From</th><th>To</th><th>Amount</th><th>Notes</th></tr></thead>
                    <tbody>`;
                    d.transactions.forEach(t => {
                        const badge = t.transaction_type === 'expense'
                            ? '<span class="dct-badge dct-badge-expense">Expense</span>'
                            : '<span class="dct-badge dct-badge-transfer">Transfer</span>';
                        const to = t.transaction_type === 'expense' ? '<em>Bill/Expense</em>' : ($('<div>').text(t.to_name || '—').html());
                        html += `<tr>
                            <td>${t.transaction_date}</td>
                            <td>${$('<div>').text(t.project_name||'').html()}</td>
                            <td>${badge}</td>
                            <td>${$('<div>').text(t.from_name||'—').html()}</td>
                            <td>${to}</td>
                            <td class="${t.transaction_type==='expense'?'dct-amount-out':'dct-amount-in'}">৳ ${DCT_APP.money(t.amount)}</td>
                            <td>${$('<div>').text(t.description||'').html()}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                } else {
                    html += '<p style="color:#64748b;font-size:14px;">No transactions found.</p>';
                }

                $('#dct-summary-result').html(html).show();
            },

            loadAll() {
                const pid = $('#dct-all-summary-project').val();
                DCT_APP.ajax('dct_get_all_summary', { project_id: pid }, (res) => {
                    if (!res.success) return;
                    this.renderAll(res.data);
                });
            },

            renderAll(data) {
                if (!data.length) { $('#dct-all-summary-result').html('<p style="color:#64748b;">No data.</p>').show(); return; }
                let html = '<div class="dct-table-wrap"><table class="dct-table"><thead><tr><th>Stakeholder</th><th>Received</th><th>Given Out</th><th>Expenses</th><th>Net Balance</th></tr></thead><tbody>';
                data.forEach(s => {
                    const balCls = s.balance >= 0 ? 'dct-amount-in' : 'dct-amount-out';
                    html += `<tr>
                        <td><strong>${$('<div>').text(s.name).html()}</strong></td>
                        <td class="dct-amount-in">৳ ${DCT_APP.money(s.received)}</td>
                        <td class="dct-amount-out">৳ ${DCT_APP.money(s.given)}</td>
                        <td style="color:var(--dct-accent);font-weight:700;">৳ ${DCT_APP.money(s.expenses)}</td>
                        <td class="${balCls}">৳ ${DCT_APP.money(s.balance)}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                $('#dct-all-summary-result').html(html).show();
            }
        },

        /* ════════════════ DASHBOARD ════════════════ */

        Dashboard: {
            init() {
                DCT_APP.ajax('dct_get_projects', {}, (r) => {
                    $('#dct-dash-projects').text(r.success ? r.data.length : 0);
                });
                DCT_APP.ajax('dct_get_stakeholders', {}, (r) => {
                    $('#dct-dash-stakeholders').text(r.success ? r.data.length : 0);
                });
                DCT_APP.ajax('dct_get_transactions', {}, (r) => {
                    if (!r.success) return;
                    $('#dct-dash-txns').text(r.data.length);
                    const total = r.data.reduce((s, t) => s + parseFloat(t.amount||0), 0);
                    $('#dct-dash-total').text('৳ ' + DCT_APP.money(total));
                });
            }
        },

        /* ════════════════ TABS ════════════════ */

        initTabs() {
            $(document).on('click', '.dct-tab', function () {
                const $tab = $(this);
                const pane = $tab.data('tab');
                const $container = $tab.closest('.dct-tab-container');
                $container.find('.dct-tab').removeClass('active');
                $container.find('.dct-tab-pane').removeClass('active');
                $tab.addClass('active');
                $container.find('#' + pane).addClass('active');
            });
        },

        init() {
            this.initTabs();

            if ($('#dct-projects-list').length)     this.Projects.init();
            if ($('#dct-stakeholders-list').length)  this.Stakeholders.init();
            if ($('#dct-assign-project').length)     this.Assign.init();
            if ($('#dct-txn-list').length)           this.Transactions.init();
            if ($('#dct-summary-stakeholder').length) this.Summary.init();
            if ($('#dct-dash-projects').length)      this.Dashboard.init();
        }
    };

    $(document).ready(() => DCT_APP.init());

})(jQuery);
