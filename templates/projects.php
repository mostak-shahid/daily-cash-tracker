<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-page-header">
        <h1 class="dct-page-title">Projects</h1>
    </div>

    <div class="dct-grid-2">
        <!-- FORM -->
        <div class="dct-card">
            <div class="dct-card-title">Add / Edit Project</div>
            <div id="dct-project-notice" class="dct-alert"></div>
            <form id="dct-project-form">
                <input type="hidden" id="dct-project-id">
                <div class="dct-form-group">
                    <label class="dct-label">Project Name <span class="req">*</span></label>
                    <input type="text" id="dct-project-name" class="dct-input" placeholder="e.g. Project One" required>
                </div>
                <div class="dct-form-group">
                    <label class="dct-label">Address</label>
                    <input type="text" id="dct-project-address" class="dct-input" placeholder="Project location">
                </div>
                <div class="dct-form-group">
                    <label class="dct-label">Description</label>
                    <textarea id="dct-project-desc" class="dct-textarea" placeholder="Optional notes…"></textarea>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="dct-btn dct-btn-accent">Save Project</button>
                    <button type="button" id="dct-project-cancel" class="dct-btn dct-btn-outline" style="display:none;">Cancel</button>
                </div>
            </form>
        </div>

        <!-- LIST -->
        <div class="dct-card" style="padding:0;overflow:hidden;">
            <div style="padding:20px 24px 0;"><div class="dct-card-title mb-0">All Projects</div></div>
            <div class="dct-table-wrap" style="border:none;border-radius:0;">
                <table class="dct-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dct-projects-list">
                        <tr><td colspan="4" class="text-center" style="padding:30px;color:#64748b;">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
