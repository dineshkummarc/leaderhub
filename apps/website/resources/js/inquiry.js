const STORAGE_KEY = 'leaderhub_inquiry';
const MAX_ITEMS = 50;

export function getCart() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || { items: [] };
    } catch {
        return { items: [] };
    }
}

export function saveCart(cart) {
    cart.updated_at = new Date().toISOString();
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateBar();
}

export function addItem(productId, name, slug, qty = 1) {
    const cart = getCart();

    // Check max items limit
    if (cart.items.length >= MAX_ITEMS && !cart.items.find(i => i.product_id === productId)) {
        alert('Maksimal ' + MAX_ITEMS + ' produk per inquiry');
        return;
    }

    const existing = cart.items.find(i => i.product_id === productId);
    if (existing) {
        existing.qty += qty;
    } else {
        cart.items.push({
            product_id: productId,
            name,
            slug,
            qty,
            added_at: new Date().toISOString()
        });
    }
    saveCart(cart);
}

export function removeItem(productId) {
    const cart = getCart();
    cart.items = cart.items.filter(i => i.product_id !== productId);
    saveCart(cart);
}

export function updateQty(productId, qty) {
    const cart = getCart();
    const item = cart.items.find(i => i.product_id === productId);
    if (item) {
        item.qty = Math.max(1, parseInt(qty) || 1);
    }
    saveCart(cart);
}

export function clearCart() {
    localStorage.removeItem(STORAGE_KEY);
    updateBar();
}

export function getCount() {
    return getCart().items.reduce((sum, i) => sum + i.qty, 0);
}

export function updateBar() {
    const count = getCount();
    const bar = document.getElementById('inquiry-bar');
    const countEls = document.querySelectorAll('#inquiry-count, #inquiry-bar-count');
    if (count > 0) {
        bar?.classList.remove('hidden');
        countEls.forEach(el => { el.textContent = count; });
    } else {
        bar?.classList.add('hidden');
    }
}

// Global function untuk dipanggil dari onclick di HTML
window.addToInquiry = function (productId) {
    const el = document.querySelector(`[data-product-id="${productId}"]`);
    const name = el?.dataset.name || 'Product';
    const slug = el?.dataset.slug || '';
    addItem(productId, name, slug);
    alert('Ditambahkan ke daftar minat');
};

// Update bar saat page load
document.addEventListener('DOMContentLoaded', updateBar);
