// Hide pop-up
const popupForm = document.getElementById('form-popup')
if (popupForm) {
    popupForm.style.display = 'none'
}
document.getElementById('cross-popup').onclick = function () {
    popupForm.style.display = 'none'
}

document.getElementById('form-popup').onclick = function () {
    popupForm.style.display = 'none'
}
document.getElementById('formNotClickable').onclick = function (e) {
    e.stopPropagation()
}

const disableDlButton = (alter) => {
    const downloadButtons = document.getElementsByClassName(
        'downolad-btn-element'
    )

    for (box of downloadButtons) {
        alter
            ? box.classList.add('wd-disable-button')
            : box.classList.remove('wd-disable-button')
    }
}

// Count checked element on checkbox click
localStorage.setItem('library-request-list', 0)

const alterCheckBoxes = (alter = false) => {
    const checkBoxes = document.getElementsByClassName(
        'library-element-checkbox'
    )

    disableDlButton(alter)

    for (box of checkBoxes) {
        if (!box.checked && limitations !== '0') {
            box.disabled = alter
        }
    }
}

const checkIfLimitationOverload = () => {
    const countElement = localStorage.getItem('library-request-list')

    if (limitations !== '0') {
        countElement >= limitations ? alterCheckBoxes(true) : alterCheckBoxes()
    }
}

const displayCounter = () => {
    const countElement = document.getElementById('wd-lib-count-item')
    checkIfLimitationOverload()
    if (countElement) {
        countElement.innerHTML = localStorage.getItem('library-request-list')
    }
}

displayCounter()

const addToList = (item) => {
    let actualCountRequested = localStorage.getItem('library-request-list')
    item.checked ? actualCountRequested++ : actualCountRequested--

    localStorage.setItem('library-request-list', actualCountRequested)
    displayCounter()
}

const checkedbox = (box) => {
    const elementDownload = box.getElementsByClassName(
        'library-element-checkbox'
    )
    const textElement = box.getElementsByClassName('text-checkbox')

    if (
        box.classList.contains('wd-disable-button') &&
        !elementDownload[0].checked
    ) {
        return
    }

    if (elementDownload[0].checked) {
        elementDownload[0].removeAttribute('checked')
        textElement[0].classList.remove('checkedElement')
        box.classList.remove('downolad-btn-element-checked')
    } else {
        elementDownload[0].setAttribute('checked', 'checked')
        textElement[0].classList.add('checkedElement')
        box.classList.add('downolad-btn-element-checked')
    }

    addToList(elementDownload[0])
}

// Hide or display pop up on click 'validate' button
const validateButton = document.getElementById('displayConfirmation')

if (validateButton) {
    validateButton.addEventListener('click', (e) => {
        popupForm.style.display === 'none'
            ? updateHiddenFields(popupForm)
            : (popupForm.style.display = 'none')
    })
}

const updateHiddenFields = (popupForm) => {
    popupForm.style.display = 'flex'
    const filesId = []
    const hiddenField = document.getElementById('hidden_data_field')
    const catsId = []
    const hiddenCatField = document.getElementById('hidden_data_cat_field')
    hiddenField.value = ''
    hiddenCatField.value = ''

    const checkedElements = document.querySelectorAll(
        '.library-element-checkbox:checked'
    )

    checkedElements.forEach((element) => {
        filesId.push(element.value)
        const selectedCats = element.dataset.categories.split(',')
        for (const selectedCat of selectedCats) {
            if (!catsId.includes(selectedCat)) {
                catsId.push(selectedCat)
            }
        }
    })

    hiddenField.value = JSON.stringify(filesId)
    hiddenCatField.value = JSON.stringify(catsId)
}

var filterList = []
jQuery('.wd-category-container option').each(function () {
    var img = jQuery(this).attr('data-thumbnail')
    var text = this.innerText
    var value = jQuery(this).val()
    var item =
        '<li class="item-dropdown-list" data-filter ="' +
        value +
        '" onclick="filterLinks(this, ' +
        value +
        ')">'
    if (img) {
        item += '<img src="' + img + '" alt="" value="' + value + '"/>'
    }
    item += '<span>' + text + '</span></li>'
    filterList.push(item)
})

jQuery('#wd-filter-list').html(filterList)

//Set the button value to the first el of the array
jQuery('.wd-btn-select').html(filterList[0])
jQuery('.wd-btn-select').attr('value', 1)

//change button stuff on click
jQuery('#wd-filter-list li').click(function () {
    var img = jQuery(this).find('img').attr('src')
    var value = jQuery(this)[0].dataset.filter
    var text = this.innerText
    var item = '<li>'
    if (img) {
        item += '<img src="' + img + '" alt="" />'
    }
    item += '<span>' + text + '</span></li>'

    jQuery('.wd-btn-select').html(item)
    jQuery('.wd-btn-select').attr('value', value)
    jQuery('.wd-filter-list-contain').toggle()
})

jQuery('.wd-btn-select').click(function () {
    jQuery('.wd-filter-list-contain').toggle()
})

/*  Javascript filter
---------------------------------*/

const displayAllFilter = () => {
    const allFiles = document.getElementsByClassName('library-element')
    const selectButton = document.getElementsByClassName('wd-btn-select')
    const currentSelect = document.querySelector('.wd-btn-select li')
    const allCategorieElement = document.querySelectorAll('#wd-filter-list li')
    const cloneElement = allCategorieElement[0].cloneNode(true)

    selectButton[0].removeChild(currentSelect)
    selectButton[0].appendChild(cloneElement)

    for (const file of allFiles) {
        file.style.display = 'grid'
    }
}

// filter links functions
const filterLinks = (element, filterValue) => {
    const allFiles = document.getElementsByClassName('library-element')
    for (const file of allFiles) {
        const fileCat = file.dataset.filtercat.split(',')
        if (fileCat.includes(filterValue.toString())) {
            file.style.display = 'grid'
        } else {
            file.style.display = 'none'
        }
    }
}

const searchLibrary = (searchElement) => {
    const allFiles = document.getElementsByClassName('library-element')
    const searchTerms = searchElement.value.toLowerCase()

    for (const file of allFiles) {
        const fileTerm = file.dataset.content.toLowerCase()
        if (fileTerm.includes(searchTerms)) {
            file.style.display = 'grid'
        } else {
            file.style.display = 'none'
        }
    }
}

const authorizedValidation = (validationBox) => {
    const submitFormButton = document.getElementById('requestFormBtn')
    validationBox.checked
        ? (submitFormButton.disabled = false)
        : (submitFormButton.disabled = true)
}

//Script Rendre le bouton non cliquable si le compteur = 0 by Alex @webdigit.

// Je get l'ID du boutton et je le rend inactif par défault

const btnToDisabled = document.getElementById("displayConfirmation");
btnToDisabled.style.backgroundColor = "grey";
btnToDisabled.disabled = true;

// Je récupère l'ensemble de mes boutons contenant mes inputs pour appliquer ma fonction sur ces derniers au clic et pouvoir faire ma comparaison.

var thelist = document.querySelectorAll(".downolad-btn-element");

// Pour l'ensemble des entrées dans la liste du querySelectorAll j'ajoute un écouteur d'évenement au clic pour pouvoir lancer ma fonction et enlever le disable du bouton en fonction de mes quantités.

for (let i = 0; i < thelist.length; i++) {
  thelist[i].addEventListener("click", checkSelectedDocs);
}

// Ma fonction récupére la valeur de documents cochés dans la boite à outils et disable le bouton si ça revient à 0 ou au contraire enable le bouton si jamais la valeur du compteur est supérieur à 0.

function checkSelectedDocs() {
  let selectedDocsNumber = document.getElementById("wd-lib-count-item").textContent
  parseInt(selectedDocsNumber);
  if (selectedDocsNumber > 0 ){
    btnToDisabled.disabled = false;
    btnToDisabled.style.backgroundColor = "#E11769";
    }
    else{
    btnToDisabled.disabled = true;
    btnToDisabled.style.backgroundColor = "grey";
    alert("Veuillez cocher au moins un élément dans la liste pour pouvoir valider votre demande");
  }
}
