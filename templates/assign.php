<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-page-header">
        <h1 class="dct-page-title">Assign Stakeholders to Projects</h1>
    </div>

    <div class="dct-grid-2">
        <div class="dct-card">
            <div class="dct-card-title">Make Assignment</div>
            <div id="dct-assign-notice" class="dct-alert"></div>

            <div class="dct-form-group">
                <label class="dct-label">Select Project <span class="req">*</span></label>
                <select id="dct-assign-project" class="dct-select">
                    <option value="">Loading…</option>
                </select>
            </div>
            <div class="dct-form-group">
                <label class="dct-label">Select Stakeholder <span class="req">*</span></label>
                <select id="dct-assign-stakeholder" class="dct-select">
                    <option value="">Loading…</option>
                </select>
            </div>
            <button id="dct-assign-btn" class="dct-btn dct-btn-accent">Assign →</button>
        </div>

        <div class="dct-card">
            <div class="dct-card-title">Currently Assigned Stakeholders</div>
            <p style="font-size:13px;color:#64748b;margin-top:0;">Select a project on the left to view assignments.</p>
            <div id="dct-assigned-wrap" style="margin-top:12px;"></div>
        </div>
    </div>
</div>
