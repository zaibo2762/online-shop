// JS for RTL star rating hover effect

document.addEventListener('DOMContentLoaded', function () {
    const starsContainers = document.querySelectorAll('.rating-stars');
    starsContainers.forEach(container => {
        const labels = Array.from(container.querySelectorAll('label'));
        labels.forEach((label, idx) => {
            label.addEventListener('mouseenter', function () {
                labels.forEach((l, i) => {
                    if (i <= idx) {
                        l.querySelector('.fa-star').style.color = '#ffc107';
                    } else {
                        l.querySelector('.fa-star').style.color = '#ddd';
                    }
                });
            });
            label.addEventListener('mouseleave', function () {
                labels.forEach((l, i) => {
                    l.querySelector('.fa-star').style.color = '#ddd';
                });
                // Restore checked state
                const checked = container.querySelector('input[type="radio"]:checked');
                if (checked) {
                    const checkedIdx = labels.findIndex(l => l.htmlFor === checked.id);
                    labels.forEach((l, i) => {
                        if (i <= checkedIdx) {
                            l.querySelector('.fa-star').style.color = '#ffc107';
                        }
                    });
                }
            });
            label.addEventListener('click', function () {
                labels.forEach((l, i) => {
                    if (i <= idx) {
                        l.querySelector('.fa-star').style.color = '#ffc107';
                    } else {
                        l.querySelector('.fa-star').style.color = '#ddd';
                    }
                });
            });
        });
    });
});
