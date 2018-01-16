(function() {
    'use strict';
    //Line Height
    function lineHeight() {
        var lineHeight = [];
        for(var i = 1; i<=100; i++ ) {
            lineHeight.push({
                text: (i/10) + 'em',
                data : (i/10),
                onclick: function(params) {
                    var i = params.control.settings.data;
                    tinymce.activeEditor.formatter.register('lh_px' + i, {
                        inline : 'span',
                        styles : {'line-height' : i+'em','display':'inline-block'}
                    });
                    tinymce.activeEditor.formatter.apply('lh_px' + i);
                }
            });
        }

        return lineHeight;
    }

    tinymce.PluginManager.add( 'my_lh_button', function( editor, url ) {
        editor.addButton( 'my_lh_button', {
            type: 'menubutton',
            text: 'Line Height',
            tooltip: 'Set line height',
            menu: lineHeight()
        });

    });
})();
