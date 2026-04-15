<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-page-header">
        <h1 class="dct-page-title">Cash Summary</h1>
    </div>

    <div class="dct-tab-container">
        <div class="dct-tabs">
            <button class="dct-tab active" data-tab="dct-tab-single">Individual Summary</button>
            <button class="dct-tab" data-tab="dct-tab-all">All Stakeholders Overview</button>
        </div>

        <!-- Individual -->
        <div id="dct-tab-single" class="dct-tab-pane active">
            <div class="dct-card">
                <div class="dct-card-title">Select Stakeholder</div>
                <div id="dct-summary-notice" class="dct-alert"></div>
                <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <label class="dct-label">Stakeholder <span class="req">*</span></label>
                        <select id="dct-summary-stakeholder" class="dct-select">
                            <option value="">— Select Stakeholder —</option>
                        </select>
                    </div>
                    <div style="flex:1;min-width:200px;">
                        <label class="dct-label">Filter by Project</label>
                        <select id="dct-summary-project" class="dct-select">
                            <option value="">All Projects</option>
                        </select>
                    </div>
                    <div style="flex:1;min-width:150px;">
                        <label class="dct-label">From Date</label>
                        <input type="date" id="dct-summary-date-from" class="dct-input">
                    </div>
                    <div style="flex:1;min-width:150px;">
                        <label class="dct-label">To Date</label>
                        <input type="date" id="dct-summary-date-to" class="dct-input">
                    </div>
                    <button id="dct-summary-btn" class="dct-btn dct-btn-accent" style="white-space:nowrap;">Load Summary →</button>
                </div>
            </div>

            <div id="dct-summary-result" style="display:none;"></div>
        </div>

        <!-- All -->
        <div id="dct-tab-all" class="dct-tab-pane">
            <div class="dct-card">
                <div class="dct-card-title">Overview — All Stakeholders</div>
                <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;">
                    <div style="flex:1;min-width:200px;">
                        <label class="dct-label">Filter by Project</label>
                        <select id="dct-all-summary-project" class="dct-select">
                            <option value="">All Projects</option>
                        </select>
                    </div>
                    <div style="flex:1;min-width:150px;">
                        <label class="dct-label">From Date</label>
                        <input type="date" id="dct-all-summary-date-from" class="dct-input">
                    </div>
                    <div style="flex:1;min-width:150px;">
                        <label class="dct-label">To Date</label>
                        <input type="date" id="dct-all-summary-date-to" class="dct-input">
                    </div>
                    <button id="dct-all-summary-btn" class="dct-btn dct-btn-primary">Load Overview</button>
                </div>
                <div id="dct-all-summary-result"></div>
            </div>
        </div>
    </div>
</div>
