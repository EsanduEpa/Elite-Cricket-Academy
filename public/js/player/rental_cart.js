document.addEventListener('DOMContentLoaded', function () {
    setMinimumDates();
    updateRentalCartCount();
});

function getRentalCartUrlRoot() {
    const page = document.getElementById('rentalCartPage');
    return page && page.dataset && page.dataset.urlroot ? page.dataset.urlroot : '';
}

function getRentalCartEndpoint(path) {
    const root = getRentalCartUrlRoot();
    return root ? `${root}/player/${path}` : `/player/${path}`;
}

async function updateRentalCartCount() {
    const cartCountNodes = document.querySelectorAll('.cart-count');
    if (!cartCountNodes.length) return;

    try {
        const response = await fetch(getRentalCartEndpoint('rentalCartSummary'), { credentials: 'same-origin' });
        const payload = await response.json();
        if (payload && payload.success) {
            const count = Number(payload.cart_count || 0);
            cartCountNodes.forEach((node) => {
                node.textContent = String(count);
            });
        }
    } catch (error) {
        // keep server-rendered badge
    }
}

function setMinimumDates() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];

    dateInputs.forEach((input) => {
        input.min = today;
        if (!input.value) {
            input.value = today;
        }
    });
}
