/**
 * Cashier Sidebar Resizer
 * Adds a drag handle on the right edge of the sidebar to adjust width.
 * Updates main content margin to follow the sidebar width.
 */
function makeSidebarResizable(sidebar) {
    if (!sidebar) return;
    if (sidebar.dataset.resizable === 'true') return; // already attached

    sidebar.dataset.resizable = 'true';

    const handle = document.createElement('div');
    handle.style.cssText = `
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 100%;
        background: rgba(255,255,255,0.2);
        cursor: ew-resize;
        z-index: 10000;
        transition: background 0.2s;
    `;
    handle.title = 'Drag to resize sidebar';
    sidebar.appendChild(handle);

    let isResizing = false;
    let startX = 0;
    let startWidth = 0;

    function onMouseDown(e) {
        isResizing = true;
        startX = e.clientX;
        startWidth = parseInt(sidebar.style.width, 10) || sidebar.offsetWidth;
        document.body.style.cursor = 'ew-resize';
        document.body.style.userSelect = 'none';
        e.preventDefault();
    }

    function onMouseMove(e) {
        if (!isResizing) return;
        const newWidth = startWidth + e.clientX - startX;
        if (newWidth >= 180 && newWidth <= 400) {
            sidebar.style.width = newWidth + 'px';
            // Update page content margin
            const page = document.querySelector('.products-page, .categories-page, .inventory-page, .stockin-page, .purchase-page');
            if (page) {
                page.style.marginLeft = newWidth + 'px';
            }
        }
    }

    function onMouseUp() {
        if (!isResizing) return;
        isResizing = false;
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
        // Persist new width in localStorage
        localStorage.setItem('cashierSidebarWidth', sidebar.style.width);
    }

    handle.addEventListener('mousedown', onMouseDown);
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);

    // Restore saved width on load
    const savedWidth = localStorage.getItem('cashierSidebarWidth');
    if (savedWidth) {
        sidebar.style.width = savedWidth;
        const page = document.querySelector('.products-page, .categories-page, .inventory-page, .stockin-page, .purchase-page');
        if (page) {
            page.style.marginLeft = savedWidth;
        }
    }
}
