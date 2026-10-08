const area = document.querySelector('#area');
const paving = document.querySelector('#paving');
const removal = document.querySelector('#removal');
const form = document.querySelector('#calculator-form');

if (area && paving && removal && form) {
    const format = (value) => value.toLocaleString('lv-LV', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';

    const updateEstimate = () => {
        const squareMeters = Number(area.value);
        if (!Number.isFinite(squareMeters) || squareMeters < 1 || squareMeters > Number(area.max)) {
            return;
        }
        const selectedBase = document.querySelector('input[name="base"]:checked');
        if (!selectedBase || !paving.selectedOptions[0]) {
            return;
        }
        const pavingPrice = Number(paving.selectedOptions[0].dataset.price);
        const basePrice = Number(selectedBase.dataset.price);
        const removalPrice = removal.checked ? Number(removal.dataset.price) : 0;

        document.querySelector('#paving-name').textContent = paving.selectedOptions[0]?.dataset.name || 'Bruģis';
        document.querySelector('#paving-total').textContent = format(squareMeters * pavingPrice);
        document.querySelector('#base-total').textContent = format(squareMeters * basePrice);
        document.querySelector('#removal-total').textContent = format(squareMeters * removalPrice);
        document.querySelector('#removal-line').hidden = !removal.checked;
        document.querySelector('#total').textContent = format(squareMeters * (pavingPrice + basePrice + removalPrice)).replace(' €', '');
    };

    form.addEventListener('input', updateEstimate);
    form.addEventListener('change', updateEstimate);
}
