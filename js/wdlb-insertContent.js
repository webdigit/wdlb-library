(function() {
    tinymce.create('tinymce.plugins.WDLB', {
        init : function(ed, url) {
            const parentUrl = url.replace('/js/dist', '')
            ed.addButton('wdlb_button', {
                title : 'WDLB button',
                cmd : 'wdlb_insert_shortcode',
                image : parentUrl + '/assets/img/icon.png'
            });

            ed.addCommand('wdlb_insert_shortcode', function() {
                const return_text = '[wdlb_content_mail_user]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('wdlb', tinymce.plugins.WDLB);
})();