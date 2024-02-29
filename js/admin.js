document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('select_image').addEventListener('click', function(e) {
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
        var customUploader = wp.media({
            title: 'Choisir une image',
            library: { type: 'image' },
            button: { text: 'Sélectionner' },
            multiple: false
        });

        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            document.getElementById('image_url').value = attachment.url;
            document.getElementById('image_id').value = attachment.id;
            var imagePreview = document.getElementById('image_preview');
            imagePreview.innerHTML = '<img src="' + attachment.url + '" width="50" height="50" alt="">';
        });

        customUploader.open();
    });
});
