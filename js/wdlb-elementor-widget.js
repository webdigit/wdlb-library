console.log('script loaded 🤣')

document.addEventListener('DOMContentLoaded', (event) => {
    console.log('DOM fully loaded and parsed');

    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
        console.log('Elementor editor is open');
        console.log('Widget type: ' + model.get('widgetType'));

        if ('wdlb-library' === model.get('widgetType')) {
            const settings = model.get('settings');
            console.log(settings.attributes);

            settings.on('change:wd_lib_limit_dl', function() {
                console.log('change triggered limit');
                sendAjaxRequest(settings);
            });

            settings.on('change:wd_lib_active_search', function() {
                console.log('change triggered search');
                sendAjaxRequest(settings);
            });
        }
    });
});

function sendAjaxRequest(settings) {
    console.log('change triggered')
    const data = new FormData();
    data.append('action', 'update_wdlb_options');
    data.append('nonce', wdlb_ajax_nonce);
    data.append('wd_lib_limit_dl', settings.get('wd_lib_limit_dl'));
    data.append('wd_lib_active_search', settings.get('wd_lib_active_search'));

    const xhr = new XMLHttpRequest();
    xhr.open('POST', ajaxurl, true);
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            console.log(this.responseText);
        }
    };
    xhr.send(data);
}