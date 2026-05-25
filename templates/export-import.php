<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-page-header">
        <h1 class="dct-page-title">Export / <span class="accent">Import</span></h1>
    </div>

    <div class="dct-grid-2">
        <div class="dct-card">
            <div class="dct-card-title">Export Data</div>
            <p style="color:var(--dct-muted);margin-bottom:20px;font-size:14px;">Download all your data as a JSON file. This includes projects, stakeholders, assignments, and transactions.</p>
            
            <div style="margin-bottom:16px;">
                <div style="font-size:13px;color:var(--dct-muted);margin-bottom:8px;">Tables to export:</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span class="dct-badge dct-badge-transfer">Projects</span>
                    <span class="dct-badge dct-badge-transfer">Stakeholders</span>
                    <span class="dct-badge dct-badge-transfer">Project Stakeholders</span>
                    <span class="dct-badge dct-badge-transfer">Transactions</span>
                </div>
            </div>

            <button class="dct-btn dct-btn-accent" id="dct-export-btn" onclick="DCT_APP.ExportImport.export()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export All Data
            </button>
            
            <div id="dct-export-notice" class="dct-alert" style="margin-top:16px;"></div>
        </div>

        <div class="dct-card">
            <div class="dct-card-title">Import Data</div>
            <p style="color:var(--dct-muted);margin-bottom:20px;font-size:14px;">Upload a JSON file previously exported from Daily Cash Tracker. Existing data will be updated and new data will be added.</p>
            
            <form id="dct-import-form" enctype="multipart/form-data" style="margin-bottom:16px;">
                <div class="dct-form-group">
                    <label class="dct-label">Select JSON File <span class="req">*</span></label>
                    <input type="file" id="dct-import-file" name="import_file" accept=".json" class="dct-input" required>
                    <p style="font-size:12px;color:var(--dct-muted);margin-top:4px;">Only JSON files exported from this plugin are supported.</p>
                </div>

                <div class="dct-form-group">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--dct-text);">
                        <input type="checkbox" id="dct-import-confirm" required>
                        <span>I understand that this will update existing data and cannot be undone.</span>
                    </label>
                </div>

                <button type="submit" class="dct-btn dct-btn-primary" id="dct-import-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Import Data
                </button>
            </form>

            <div id="dct-import-progress" style="display:none;margin-top:20px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px;">
                    <span id="dct-progress-status">Preparing...</span>
                    <span id="dct-progress-percent">0%</span>
                </div>
                <div style="width:100%;height:24px;background:#e2e8f0;border-radius:12px;overflow:hidden;">
                    <div id="dct-progress-bar" style="width:0%;height:100%;background:linear-gradient(90deg,var(--dct-accent),var(--dct-accent2));transition:width 0.3s ease;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:8px;font-size:12px;color:var(--dct-muted);">
                    <span id="dct-progress-rows">0 / 0 rows imported</span>
                    <span id="dct-progress-table">Initializing...</span>
                </div>
            </div>

            <div id="dct-import-notice" class="dct-alert" style="margin-top:16px;"></div>
        </div>
    </div>

    <div class="dct-card" style="margin-top:24px;">
        <div class="dct-card-title">Important Notes</div>
        <ul style="color:var(--dct-muted);font-size:14px;line-height:1.6;padding-left:20px;">
            <li><strong>Export:</strong> Creates a JSON file containing all your current data.</li>
            <li><strong>Import:</strong> Updates existing records and adds new ones. Duplicate IDs will be updated.</li>
            <li><strong>Backup:</strong> Always create a backup before importing data.</li>
            <li><strong>File Format:</strong> Only JSON files exported from this plugin are supported.</li>
        </ul>
    </div>
</div>
