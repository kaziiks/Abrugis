document.addEventListener('DOMContentLoaded', () => {
    const compareCard = document.querySelector('.compare-card');
    const compareStateButtons = document.querySelectorAll('.compare-toggle-option');
    const compareBefore = document.querySelector('#compare-before');
    const compareAfter = document.querySelector('#compare-after');
    const compareFilters = document.querySelectorAll('.compare-filter');

    const compareData = {
        betons: ['/storage/pirmsunpec/pirmsbetona.jpg', '/storage/pirmsunpec/pecbetona.jpg'],
        klinkers: ['/storage/pirmsunpec/pirmsklinkera.jpg', '/storage/pirmsunpec/pecklinkera.jpg'],
        granits: ['/storage/pirmsunpec/pirmsgranita.jpg', '/storage/pirmsunpec/pecgranita.jpg'],
    };

    let activeFilter = 'betons';

    const setCompareState = (showAfter) => {
        if (!compareCard) return;

        compareCard.classList.toggle('is-after', showAfter);
        compareStateButtons.forEach((button) => {
            const isActive = button.dataset.compareState === (showAfter ? 'after' : 'before');
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });
    };

    const applyCompareImages = () => {
        if (!compareBefore || !compareAfter) return;

        const [beforeSrc, afterSrc] = compareData[activeFilter] || compareData.betons;

        compareBefore.src = beforeSrc;
        compareAfter.src = afterSrc;
        setCompareState(false);
    };

    if (compareCard) {
        compareStateButtons.forEach((button) => {
            button.addEventListener('click', () => {
                setCompareState(button.dataset.compareState === 'after');
            });
        });
    }

    compareFilters.forEach((button) => {
        button.addEventListener('click', () => {
            compareFilters.forEach((item) => item.classList.toggle('is-active', item === button));
            activeFilter = button.dataset.filter;
            applyCompareImages();
        });
    });

    applyCompareImages();
});
