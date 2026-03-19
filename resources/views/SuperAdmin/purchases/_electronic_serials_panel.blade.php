<div class="border rounded p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0">Electronic product details & serials</h6>
            <p class="text-muted small mb-0">Add product serials / IMEIs. You can add multiple entries.</p>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm toggle-electronics-panel-btn">Hide</button>
    </div>

    <div class="electronics-panel-body mt-3">
        <div class="row g-3 align-items-end mb-2 js-warranty-controls">
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input js-same-expiry-toggle" type="checkbox" value="1">
                    <label class="form-check-label">
                        Same warranty expiry for all
                    </label>
                </div>
            </div>
            <div class="col-md-4 js-shared-expiry-wrap d-none">
                <label class="form-label">Warranty Expiry (All)</label>
                <input type="date" class="form-control js-shared-expiry-input">
            </div>
        </div>

        <div class="serial-entries-container"></div>
        <button type="button" class="btn btn-primary btn-sm add-serial-btn mt-2">Add serial</button>
    </div>
</div>
