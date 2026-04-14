<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-page-header">
        <h1 class="dct-page-title">Cash Entry</h1>
    </div>

    <div class="dct-grid-2" style="margin-bottom:0;">
        <!-- FORM -->
        <div class="dct-card">
                <div class="dct-card-title">Record a Transaction</div>
                <div id="dct-txn-notice" class="dct-alert"></div>
                <form id="dct-txn-form">
                    <input type="hidden" id="dct-txn-id" value="">

                <div class="dct-form-group">
                    <label class="dct-label">Project <span class="req">*</span></label>
                    <select id="dct-txn-project" class="dct-select">
                        <option value="">— Select Project —</option>
                    </select>
                </div>

                <div class="dct-form-group">
                    <label class="dct-label">Transaction Type <span class="req">*</span></label>
                    <select id="dct-txn-type" class="dct-select">
                        <option value="transfer">💸 Cash Transfer (person → person)</option>
                        <option value="expense">🧾 Expense / Bill Payment</option>
                    </select>
                </div>

                <div class="dct-form-group">
                    <label class="dct-label" id="dct-txn-from-label">From (Giver)</label>
                    <select id="dct-txn-from" class="dct-select">
                        <option value="">— Select project first —</option>
                    </select>
                </div>

                <div class="dct-form-group" id="dct-txn-to-wrap">
                    <label class="dct-label">To (Receiver)</label>
                    <select id="dct-txn-to" class="dct-select">
                        <option value="">— Select project first —</option>
                    </select>
                </div>

                <div class="dct-form-group" id="dct-txn-to-wrap">
                    <label class="dct-label">Category</label>
                    <select id="dct-txn-category" class="dct-select">
                        <option value="Cash In">Cash In</option>
                        <option value="Labor">Labor</option>
                        <option value="Materials">Materials</option>
                        <option value="Equipment">Equipment</option>
                        <option value="Subcontractor">Subcontractor</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="dct-form-group" id="dct-txn-to-wrap">
                    <label class="dct-label">Phase</label>
                    <select id="dct-txn-phase" class="dct-select">
                        <option value="Site Preparation">Site Preparation</option>
                        <option value="Pilling">Pilling</option>
                        <option value="Basement">Basement</option>
                        <option value="Ground Floor">Ground Floor</option>
                        <option value="First Floor">First Floor</option>
                        <option value="Second Floor">Second Floor</option>
                        <option value="Third Floor">Third Floor</option>
                    </select>
                </div>

                <div class="dct-form-group">
                    <label class="dct-label">Amount (৳) <span class="req">*</span></label>
                    <input type="number" id="dct-txn-amount" class="dct-input" placeholder="0.00" step="0.01" min="0.01" required>
                </div>

                <div class="dct-form-group">
                    <label class="dct-label">Date <span class="req">*</span></label>
                    <input type="date" id="dct-txn-date" class="dct-input" required>
                </div>

                <div class="dct-form-group">
                    <label class="dct-label">Notes / Description</label>
                    <textarea id="dct-txn-desc" class="dct-textarea" placeholder="Optional notes about this transaction…"></textarea>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="dct-btn dct-btn-accent">Record Transaction</button>
                    <button type="button" id="dct-txn-cancel" class="dct-btn dct-btn-outline" style="display:none;">Cancel</button>
                </div>
            </form>
        </div>

        <!-- FILTERS + LIST -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="dct-card" style="padding:16px 20px;">
                <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));gap:10px;align-items:flex-end;">
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">Project</label>
                        <select id="dct-filter-project" class="dct-select">
                            <option value="">All</option>
                        </select>
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">Type</label>
                        <select id="dct-filter-type" class="dct-select">
                            <option value="">All</option>
                            <option value="transfer">Transfer</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">From</label>
                        <select id="dct-filter-from" class="dct-select">
                            <option value="">All</option>
                        </select>
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">To</label>
                        <select id="dct-filter-to" class="dct-select">
                            <option value="">All</option>
                        </select>
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">Category</label>
                        <select id="dct-filter-category" class="dct-select">
                            <option value="">All</option>
                            <option value="Cash In">Cash In</option>
                            <option value="Labor">Labor</option>
                            <option value="Materials">Materials</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Subcontractor">Subcontractor</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">Phase</label>
                        <select id="dct-filter-phase" class="dct-select">
                            <option value="">All</option>
                            <option value="Site Preparation">Site Preparation</option>
                            <option value="Pilling">Pilling</option>
                            <option value="Basement">Basement</option>
                            <option value="Ground Floor">Ground Floor</option>
                            <option value="First Floor">First Floor</option>
                            <option value="Second Floor">Second Floor</option>
                            <option value="Third Floor">Third Floor</option>
                        </select>
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">Date From</label>
                        <input type="date" id="dct-filter-date-from" class="dct-input">
                    </div>
                    <div>
                        <label class="dct-label" style="margin-bottom:4px;">Date To</label>
                        <input type="date" id="dct-filter-date-to" class="dct-input">
                    </div>
                    <div>
                        <button id="dct-filter-btn" class="dct-btn dct-btn-primary" style="width:100%;">Filter</button>
                    </div>
                    <div>
                        <button id="dct-filter-reset" class="dct-btn dct-btn-outline" style="width:100%;">Reset</button>
                    </div>
                </div>
            </div>

            <div class="dct-card" style="padding:0;overflow:hidden;flex:1;">
                <div style="padding:16px 20px 0;"><div class="dct-card-title mb-0">Recent Transactions</div></div>
                <div class="dct-table-wrap" style="border:none;border-radius:0;">
                    <table class="dct-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Category</th>
                                <th>Phase</th>
                                <th>Amount</th>
                                <th>Notes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="dct-txn-list">
                            <tr><td colspan="10" class="text-center" style="padding:30px;color:#64748b;">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
