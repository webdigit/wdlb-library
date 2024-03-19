/**
 * Filters the categories based on the selected checkboxes.
 */
(function() {
    if (!JSON.parse(libRoles).length) {
        return
    }
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

    const searchElement = document.getElementById('wdlb-search');
    if (searchElement) {
        searchElement.addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('#wdlb-content .wdlb-content-item').forEach((el) => {
                el.style.display = el.querySelector('.wdlb-content-item-description').textContent.toLowerCase().includes(search) ? 'flex' : 'none';
            });
        });
    }

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

    const btnConfirmation = document.getElementById('wdlb-confirmation-request');
    if (btnConfirmation) {
        btnConfirmation.addEventListener('click', function() {
            const checked = document.querySelectorAll('.wdlb-checked');
            const items = [];
            const categories = [];

            const requestedDatas = [];

            checked.forEach((el) => {
                requestedDatas.push({
                    files: el.dataset.file_id,
                    categories: el.dataset.categories_id
                });
            });
            if (requestedDatas.length > 0) {
                document.getElementById('wdlb_hidden_data_field').value = JSON.stringify(requestedDatas);
                document.getElementById('wdlb-form-popup').style.display = 'block';
            }
        });
    }


    const crossPopup =  document.getElementById('cross-popup');
    if (crossPopup) {
        document.getElementById('cross-popup').addEventListener('click', function () {
            document.getElementById('wdlb-form-popup').style.display = 'none';
        })
    }

    function wdlb_accept_gdpr (element) {
        document.getElementById('wdlb_requestFormBtn').disabled = !element.checked;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const wdlb_gdpr = document.getElementsByClassName('wd-rgpd-check-form')[0];
        wdlb_accept_gdpr(wdlb_gdpr);

    });
})();