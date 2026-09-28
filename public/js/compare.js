document.addEventListener('DOMContentLoaded', () => {
    const projectSelect = document.querySelector('#compare-project');
    const compareRange = document.querySelector('#compare-range');
    const compareCard = document.querySelector('.compare-card');
    const compareAfterLayer = document.querySelector('#compare-after-layer');
    const compareDivider = document.querySelector('#compare-divider');
    const compareBefore = document.querySelector('#compare-before');
    const compareAfter = document.querySelector('#compare-after');
    const compareFilters = document.querySelectorAll('.compare-filter');

    const compareData = {
        aizkraukle: {
            betons: ['/storage/portfolio/brugis1.jpg', '/storage/portfolio/brugis2.jpg'],
            klinkers: ['/storage/portfolio/brugis3.jpg', '/storage/portfolio/brugis4.jpg'],
            granits: ['/storage/portfolio/brugis5.jpg', '/storage/portfolio/brugis6.jpg'],
        },
        cesu: {
            betons: ['/storage/portfolio/brugis7.jpg', '/storage/portfolio/brugis8.jpg'],
            klinkers: ['/storage/portfolio/brugis9.jpg', '/storage/portfolio/brugis1.jpg'],
            granits: ['/storage/portfolio/brugis2.jpg', '/storage/portfolio/brugis3.jpg'],
        },
        valmiera: {
            betons: ['/storage/portfolio/brugis4.jpg', '/storage/portfolio/brugis5.jpg'],
            klinkers: ['/storage/portfolio/brugis6.jpg', '/storage/portfolio/brugis7.jpg'],
            granits: ['/storage/portfolio/brugis8.jpg', '/storage/portfolio/brugis9.jpg'],
        },
    };

    let activeFilter = 'all';
    let isDragging = false;

    const setComparePosition = (value) => {
        if (!compareCard || !compareAfterLayer || !compareDivider) return;

        const position = Math.min(100, Math.max(0, Number(value) || 0));
        compareCard.style.setProperty('--compare-position', `${position}%`);
        compareAfterLayer.style.width = `${position}%`;
        compareDivider.style.left = `${position}%`;

        if (compareRange) {
            compareRange.value = String(position);
        }
    };

    const applyCompareImages = () => {
        if (!projectSelect || !compareBefore || !compareAfter) return;

        const projectKey = projectSelect.value;
        const projectSet = compareData[projectKey] || compareData.aizkraukle;
        const filterKey = activeFilter === 'all' ? 'betons' : activeFilter;
        const beforeSrc = projectSet[filterKey]?.[0] || projectSet.betons[0];
        const afterSrc = projectSet[filterKey]?.[1] || projectSet.betons[1];

        compareBefore.src = beforeSrc;
        compareAfter.src = afterSrc;
    };

    const updateFromPointer = (event) => {
        if (!compareCard) return;

        const rect = compareCard.getBoundingClientRect();
        const x = Math.min(Math.max(event.clientX - rect.left, 0), rect.width);
        const position = (x / rect.width) * 100;
        setComparePosition(position);
    };

    const stopDragging = () => {
        isDragging = false;
    };

    const beginCompareDrag = (event) => {
        if (event.target && event.target.closest('img')) {
            return;
        }

        if (event.button !== undefined && event.button !== 0) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        isDragging = true;
        updateFromPointer(event);
    };

    document.addEventListener('dragstart', (event) => {
        if (event.target && event.target.closest('img')) {
            event.preventDefault();
        }
    });

    if (projectSelect) {
        projectSelect.addEventListener('change', () => {
            applyCompareImages();
            setComparePosition(48);
        });
    }

    if (compareRange) {
        compareRange.addEventListener('input', (event) => setComparePosition(event.target.value));
    }

    if (compareDivider) {
        compareDivider.addEventListener('pointerdown', beginCompareDrag);
    }

    if (compareCard) {
        compareCard.addEventListener('pointerdown', (event) => {
            if (event.target && event.target.closest('img')) {
                return;
            }

            beginCompareDrag(event);
        });

        compareCard.addEventListener('pointermove', (event) => {
            if (!isDragging) return;
            event.preventDefault();
            updateFromPointer(event);
        });

        document.addEventListener('pointermove', (event) => {
            if (!isDragging) return;
            event.preventDefault();
            updateFromPointer(event);
        });

        document.addEventListener('pointerup', stopDragging);
        document.addEventListener('pointercancel', stopDragging);
        document.addEventListener('pointerleave', stopDragging);
    }

    compareFilters.forEach((button) => {
        button.addEventListener('click', () => {
            compareFilters.forEach((item) => item.classList.toggle('is-active', item === button));
            activeFilter = button.dataset.filter;
            applyCompareImages();
            setComparePosition(48);
        });
    });

    setComparePosition(48);
    applyCompareImages();
});
