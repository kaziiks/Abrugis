document.addEventListener('DOMContentLoaded', () => {
    const lengthInput = document.querySelector('#area-length');
    const widthInput = document.querySelector('#area-width');
    const areaInput = document.querySelector('#area');
    const result = document.querySelector('#area-helper-result');
    const useAreaButton = document.querySelector('#use-calculated-area');

    if (!lengthInput || !widthInput || !areaInput || !result || !useAreaButton) return;

    let appliedArea = null;

    const clearAppliedArea = () => {
        if (areaInput.value === appliedArea) {
            areaInput.value = '';
        }

        appliedArea = null;
    };

    const updateArea = () => {
        const length = lengthInput.valueAsNumber;
        const width = widthInput.valueAsNumber;

        if (!Number.isFinite(length) || !Number.isFinite(width) || length <= 0 || width <= 0) {
            result.textContent = result.dataset.emptyMessage;
            useAreaButton.disabled = true;
            delete useAreaButton.dataset.area;
            clearAppliedArea();
            return;
        }

        const area = length * width;

        if (area < 1 || area > 100000) {
            result.textContent = result.dataset.rangeMessage;
            useAreaButton.disabled = true;
            delete useAreaButton.dataset.area;
            clearAppliedArea();
            return;
        }

        const formattedArea = area.toLocaleString(document.documentElement.lang, {
            maximumFractionDigits: 2,
        });

        result.textContent = result.dataset.areaTemplate.replace(':area', formattedArea);
        useAreaButton.dataset.area = area.toFixed(2);
        useAreaButton.disabled = false;
    };

    lengthInput.addEventListener('input', updateArea);
    widthInput.addEventListener('input', updateArea);

    useAreaButton.addEventListener('click', () => {
        areaInput.value = useAreaButton.dataset.area;
        appliedArea = areaInput.value;
        areaInput.focus();
    });
});