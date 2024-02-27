setTimeout(() => {
    const successElement = document.getElementById('succes_saved')

    if (successElement) {
        displayHideElement(successElement, 'none')
    }
}, 5000)

const displayForm = () => {
    const form = document.getElementsByClassName('add-form-display')

    form[0].style.display === 'none'
        ? displayHideElement(form[0], 'block')
        : displayHideElement(form[0], 'none')
}

const handleAddFileForm = (checkItem, editId = false) => {
    if (editId) {
        checkItem.checked
            ? displayUrlField(true, editId)
            : displayUrlField(false, editId)
    } else {
        checkItem.checked ? displayUrlField(true) : displayUrlField(false)
    }
}

const closeOtherEditForm = () => {
    const editForms = document.getElementsByClassName('wd-edit-form')

    for (const editForm of editForms) {
        displayHideElement(editForm, 'none')
    }
}

const closeEditForm = (id) => {
    const editForm = document.getElementsByClassName('wd-edit-form-' + id)
    displayHideElement(editForm[0], 'none')
}

const openEditForm = (id) => {
    const editForm = document.getElementsByClassName('wd-edit-form-' + id)

    closeOtherEditForm()

    editForm[0].style.display === 'none'
        ? displayHideElement(editForm[0], 'block')
        : displayHideElement(editForm[0], 'none')
}

const displayUrlField = (display, editId = false) => {
    if (editId) {
        var addUrl = document.getElementById('wd_link_' + editId)
        var addDocument = document.getElementById('wd_upload_image_' + editId)
    } else {
        var addUrl = document.getElementById('wd_link')
        var addDocument = document.getElementById('wd_upload_image')
    }

    if (display) {
        displayHideElement(addUrl, 'inline-block')
        displayHideElement(addDocument, 'none')
    } else {
        displayHideElement(addUrl, 'none')
        displayHideElement(addDocument, 'inline-block')
    }
}

const displayHideElement = (element, visibility) => {
    element.style.display = visibility
}

const deleteCategory = (catId) => {
    const dataTable = document.getElementById('categoryDataTable')
    const currentRow = document.getElementsByClassName('row-for-' + catId)[0]

    jQuery
        .ajax({
            type: 'POST',
            url: ajax_object.ajaxurl,
            data: {
                action: 'deleteCategory',
                id: catId
            }
        })
        .success((response) => {
            dataTable.removeChild(currentRow)
        })
}

const deleteFile = (id) => {
    const dataTable = document.getElementById('fileDataTable')
    const currentRow = document.getElementsByClassName('row-for-' + id)[0]

    jQuery
        .ajax({
            type: 'POST',
            url: ajax_object.ajaxurl,
            data: {
                action: 'deleteFile',
                id: id
            }
        })
        .success((response) => {
            dataTable.removeChild(currentRow)
        })
}

jQuery(document).ready(function () {
    jQuery('input[id*="wd_upload_image"]').click(function (e) {
        e.preventDefault()
        const currentId = this.id
        var image_frame
        if (image_frame) {
            image_frame.open()
        }

        image_frame = wp.media({
            title: 'Choisissez une ressource',
            multiple: false
        })

        image_frame.on('close', function () {
            var selection = image_frame.state().get('selection')
            var gallery_ids = new Array()
            var my_index = 0
            selection.each(function (attachment) {
                gallery_ids[my_index] = attachment['id']
                my_index++
            })
            var ids = gallery_ids.join(',')
            if (ids.length === 0) return true

            jQuery('input#' + currentId).val(ids)
            refreshImage(ids)
        })

        image_frame.on('open', function () {
            var selection = image_frame.state().get('selection')
            var ids = jQuery('input#' + currentId)
                .val()
                .split(',')
            ids.forEach(function (id) {
                var attachment = wp.media.attachment(id)
                attachment.fetch()
                selection.add(attachment ? [attachment] : [])
            })
        })

        image_frame.open()
    })

    jQuery('input[id*="wd_cover_image"]').click(function (e) {
        e.preventDefault()
        const currentId = this.id
        var image_frame
        if (image_frame) {
            image_frame.open()
        }

        image_frame = wp.media({
            title: 'Choisissez une image de couverture',
            multiple: false
        })

        image_frame.on('close', function () {
            var selection = image_frame.state().get('selection')
            var gallery_ids = new Array()
            var my_index = 0
            selection.each(function (attachment) {
                gallery_ids[my_index] = attachment['id']
                my_index++
            })
            var ids = gallery_ids.join(',')
            if (ids.length === 0) return true
            jQuery('input#' + currentId).val(ids)
            refreshImageCouv(ids, currentId)
        })

        image_frame.on('open', function () {
            var selection = image_frame.state().get('selection')
            var ids = jQuery('input#' + currentId)
                .val()
                .split(',')
            ids.forEach(function (id) {
                var attachment = wp.media.attachment(id)
                attachment.fetch()
                selection.add(attachment ? [attachment] : [])
            })
        })

        image_frame.open()
    })
})

// Ajax request to refresh the image preview
const refreshImage = (the_id) => {
    var data = {
        action: 'wd_upload_image',
        id: the_id
    }

    jQuery.get(ajaxurl, data, function (response) {
        if (response.success === true) {
            jQuery('#displayImg').html(response.data.image)
        }
    })
}
const refreshImageCouv = (the_id, currentElementId) => {
    var data = {
        action: 'wd_cover_image',
        id: the_id
    }

    jQuery.get(ajaxurl, data, function (response) {
        if (response.success === true) {
            jQuery('#' + currentElementId).val(response.data.url)
            jQuery('#displayImgCouv').html(response.data.image)
        }
    })
}
