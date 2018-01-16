(function() {
    'use strict';




    // Letter Spacing
    function letterSpacing() {

        var letterSpacing = [];
        for(var i = 1; i<=100; i++ ) {
            letterSpacing.push({
                text: i + 'px',
                data : i,
                onclick: function(params) {
                    var i = params.control.settings.data;
                    tinymce.activeEditor.formatter.register('ls_px' + i, {
                        inline : 'span',
                        styles : {'letter-spacing' : i+'px'}
                    });
                    tinymce.activeEditor.formatter.apply('ls_px' + i);
                }
            });
        }

        return letterSpacing;
    }




    tinymce.PluginManager.add( 'my_ls_button', function( editor, url ) {
        editor.addButton( 'my_ls_button', {
            type: 'menubutton',
            text: 'Letter Spacing',
            tooltip: 'Set letter spacing',
            menu: letterSpacing()
        });

    });

})();