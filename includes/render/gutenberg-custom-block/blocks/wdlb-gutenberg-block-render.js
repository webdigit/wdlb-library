(function (blocks, editor, i18n, element) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var __ = i18n.__;

    registerBlockType('mon-plugin/wdlb-library', {
        title: __('WDLB Library'),
        icon: 'book-alt',
        category: 'common',
        edit: function (props) {
            return el(
                'div',
                { className: props.className },
                '[wdlb_library]'
            );
        },
        save: function () {
            return el('div', {}, '[wdlb_library]');
        },
    });
})(
    window.wp.blocks,
    window.wp.editor,
    window.wp.i18n,
    window.wp.element
);
