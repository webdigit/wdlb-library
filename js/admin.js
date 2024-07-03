document.addEventListener("DOMContentLoaded", function() {
    getImages();
    getDocuments();
    toggleFields();
});

const getImages = () => {
    const imgSelector = document.getElementById('select_image')

    if (!imgSelector) return;
    imgSelector.addEventListener('click', function(e) {
        e.preventDefault();
        /**
         * Custom media uploader for selecting an image.
         * @type {object}
         * @property {string} title - The title of the media uploader.
         * @property {object} library - The library settings for the media uploader.
         * @property {string} library.type - The type of media to be selected (e.g., 'image').
         * @property {object} button - The button settings for the media uploader.
         * @property {string} button.text - The text to be displayed on the button.
         * @property {boolean} multiple - Whether multiple files can be selected.
         */
        const customUploader = wp.media({
            title: 'Choisir une image',
            library: { type: 'image' },
            button: { text: 'Sélectionner' },
            multiple: false
        });

        customUploader.on('select', function() {
            const attachment = customUploader.state().get('selection').first().toJSON();
            document.getElementById('image_url').value = attachment.url;
            const imageIdContainer = document.getElementById('image_id')
            if (imageIdContainer) {
                imageIdContainer.value = attachment.id;
            }
            const imagePreview = document.getElementById('image_preview');
            imagePreview.innerHTML = '<img src="' + attachment.url + '" width="50" height="50" alt="">';
        });

        customUploader.open();
    });
}

const getDocuments = () => {
    const docSelector = document.getElementById('select_document_url')

    if (!docSelector) return;
    docSelector.addEventListener('click', function(e) {
        e.preventDefault();
        /**
         * Custom media uploader for selecting a document.
         * @type {object}
         * @property {string} title - The title of the media uploader.
         * @property {object} library - The library settings for the media uploader.
         * @property {string} library.type - The type of media to be selected (e.g., 'file').
         * @property {object} button - The button settings for the media uploader.
         * @property {string} button.text - The text to be displayed on the button.
         * @property {boolean} multiple - Whether multiple files can be selected.
         */
        const customUploader = wp.media({
            title: 'Choisir un document',
            library: { type: ['application/pdf', 'image'] }, // Utiliser le type 'file' pour les documents
            button: { text: 'Sélectionner' },
            multiple: false
        });
    
        customUploader.on('select', function() {
            const attachment = customUploader.state().get('selection').first().toJSON();
            const thumbnail = attachment.url.replace('.pdf', '-pdf.jpg');
            const post_idContainer = document.getElementById('document_id');

            if (post_idContainer) {
                post_idContainer.value = attachment.id;
            }

            document.getElementById('document_url').value = attachment.url;
            const documentPreview = document.getElementById('document_url_preview');
            documentPreview.innerHTML = '<a href="' + attachment.url + '" target="_blank"><img src="' + thumbnail + '" width="50" height="50" alt=""></a>';
        });

        customUploader.open();
    });
}

const  toggleFields = () => {
    var linkField = document.getElementById('toggleLinkField');

    if (!linkField) return;

    var documentUrlField = document.getElementById('toggleDocField');

    var toggleButton = document.getElementById('toggleFields');
    linkField.classList.toggle('hidden');

    toggleButton.addEventListener('click', function() {
        if (linkField.classList.contains('hidden')) {
            linkField.classList.toggle('hidden');
            documentUrlField.classList.toggle('hidden');
            document.getElementById('document_url').value = '';
            toggleButton.innerText = 'Encoder une ressource';
        } else {
            const linkinput = document.getElementById('link');
            documentUrlField.classList.toggle('hidden');
            linkField.classList.toggle('hidden');
            // ajoute required sur #link

            linkinput.value = '';
            toggleButton.innerText = 'Encoder un lien';
        }
    });
}