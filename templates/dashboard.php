<?php if (!defined('ABSPATH')) exit; ?>
<div class="dct-wrap">
    <div class="dct-dash-hero">
        <h2>Daily Cash Tracker</h2>
        <p>Monitor your project cash flow across all stakeholders in real time.</p>
    </div>

    <div class="dct-grid-3" style="margin-bottom:24px;">
        <div class="dct-stat blue">
            <div class="dct-stat-label">Total Projects</div>
            <div class="dct-stat-value" id="dct-dash-projects">—</div>
        </div>
        <div class="dct-stat green">
            <div class="dct-stat-label">Stakeholders</div>
            <div class="dct-stat-value" id="dct-dash-stakeholders">—</div>
        </div>
        <div class="dct-stat orange">
            <div class="dct-stat-label">Transactions</div>
            <div class="dct-stat-value" id="dct-dash-txns">—</div>
        </div>
    </div>

    <div class="dct-card">
        <div class="dct-card-title">Total Cash Moved</div>
        <div class="dct-stat-value" id="dct-dash-total" style="font-size:36px;">—</div>
    </div>

    <div class="dct-card">
        <div class="dct-card-title">Quick Links</div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a class="dct-btn dct-btn-primary" href="<?= admin_url('admin.php?page=dct-projects') ?>">+ Add Project</a>
            <a class="dct-btn dct-btn-primary" href="<?= admin_url('admin.php?page=dct-stakeholders') ?>">+ Add Stakeholder</a>
            <a class="dct-btn dct-btn-accent"  href="<?= admin_url('admin.php?page=dct-transactions') ?>">+ Cash Entry</a>
            <a class="dct-btn dct-btn-outline" href="<?= admin_url('admin.php?page=dct-summary') ?>">View Summary</a>
        </div>
    </div>
</div>
