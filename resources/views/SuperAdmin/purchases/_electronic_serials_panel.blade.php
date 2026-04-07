<div class="border rounded p-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h6 class="mb-0">Electronic product details &amp; serials</h6>
            <p class="text-muted small mb-0">
                Add product serials / IMEIs. You can add multiple entries.
                <span class="js-warranty-coverage-hint fw-semibold text-primary ms-1" style="display:none;"></span>
            </p>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            {{-- "Same warranty expiry" moved here, on the same line as the title --}}
            <div class="form-check mb-0">
                <input class="form-check-input js-same-expiry-toggle" type="checkbox" value="1" id="sameExpiryToggle">
                <label class="form-check-label small fw-semibold" for="sameExpiryToggle">
                    Same warranty expiry for all
                </label>
            </div>
            <div class="js-shared-expiry-wrap d-none">
                <input type="date" class="form-control form-control-sm js-shared-expiry-input" style="width:160px;">
            </div>
            <span class="badge bg-primary serial-counter" style="display:none;">0 serials</span>
            <button type="button" class="btn btn-outline-secondary btn-sm toggle-electronics-panel-btn">Hide</button>
        </div>
    </div>

    <div class="electronics-panel-body mt-3">
        <div class="serial-entries-container"></div>
        <button type="button" class="btn btn-primary btn-sm add-serial-btn mt-2">Add serial</button>
    </div>
</div>
