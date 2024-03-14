/**
 * Filters the categories based on the selected checkboxes.
 */
function filterCategories() {
    let filter = [];
    document.querySelectorAll('.wdlb-filter input:checked').forEach((el) => {
        filter.push(el.value);
    });
    document.querySelectorAll('#wdlb-content .wdlb-content-item').forEach((el) => {
        if (filter.length === 0 || filter.some((cat) => el.dataset.category.split(',').includes(cat))) {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    });
}

document.querySelectorAll('.wdlb-filter input').forEach((el) => {
    el.addEventListener('change', filterCategories);
});


/**
 * Filter content by name based on the search query
 */
document.getElementById('wdlb-search').addEventListener('input', function() {
    let search = this.value.toLowerCase();
    document.querySelectorAll('#wdlb-content .wdlb-content-item').forEach((el) => {
        if (el.querySelector('.wdlb-content-item-description').textContent.toLowerCase().includes(search)) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });
});

document.querySelectorAll('.wdlb-request-button').forEach((el) => {
    el.addEventListener('click', function() {
        let checked = this.querySelector('.wdlb-checked-item');
        let count = document.getElementById('wdlb-count-item');
        if (checked.classList.contains('wdlb-checked')) {
            checked.classList.remove('wdlb-checked');
            count.textContent = parseInt(count.textContent) - 1;
            if (limitations[0] !== '0'){
                document.getElementById('wdlb-limitation-max-msg').style.display = 'none';
            }
        } else {
            if (parseInt(count.textContent) < limitations[0] || limitations[0] === '0'){
                checked.classList.add('wdlb-checked');
                count.textContent = parseInt(count.textContent) + 1;
            } else if(limitations[0] !== '0') {
                document.getElementById('wdlb-limitation-max-msg').style.display = 'block';
            }
        }
    });
});