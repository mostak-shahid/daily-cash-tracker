<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-page-header">
        <h1 class="dct-page-title">Stakeholders</h1>
    </div>

    <div class="dct-grid-2">
        <!-- FORM -->
        <div class="dct-card">
            <div class="dct-card-title">Add / Edit Stakeholder</div>
            <div id="dct-stakeholder-notice" class="dct-alert"></div>
            <form id="dct-stakeholder-form">
                <input type="hidden" id="dct-stakeholder-id">
                <div class="dct-form-group">
                    <label class="dct-label">Full Name <span class="req">*</span></label>
                    <input type="text" id="dct-sh-name" class="dct-input" placeholder="e.g. Mostak" required>
                </div>
                <div class="dct-form-group">
                    <label class="dct-label">Phone</label>
                    <input type="text" id="dct-sh-phone" class="dct-input" placeholder="01XXXXXXXXX">
                </div>
                <div class="dct-form-group">
                    <label class="dct-label">Address</label>
                    <input type="text" id="dct-sh-address" class="dct-input" placeholder="City / Area">
                </div>
                <div class="dct-form-group">
                    <label class="dct-label">Description</label>
                    <textarea id="dct-sh-desc" class="dct-textarea" placeholder="Role or notes…"></textarea>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="dct-btn dct-btn-accent">Save Stakeholder</button>
                    <button type="button" id="dct-stakeholder-cancel" class="dct-btn dct-btn-outline" style="display:none;">Cancel</button>
                </div>
            </form>
        </div>

        <!-- LIST -->
        <div class="dct-card" style="padding:0;overflow:hidden;">
            <div style="padding:20px 24px 0;"><div class="dct-card-title mb-0">All Stakeholders</div></div>
            <div class="dct-table-wrap" style="border:none;border-radius:0;">
                <table class="dct-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dct-stakeholders-list">
                        <tr><td colspan="5" class="text-center" style="padding:30px;color:#64748b;">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
