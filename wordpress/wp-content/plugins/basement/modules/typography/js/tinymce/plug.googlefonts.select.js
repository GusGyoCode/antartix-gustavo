(function() {
    tinymce.init({
        extended_valid_elements : "span[!class]"
    });

    tinymce.PluginManager.add('my_mce_button', function( editor, url ) {

        var onlyText, data;

        var menu_construct = [];

        for (var key in objGF) {
            var menu_second = [];

            for (var weidht in objGF[key]) {

                var titleTypeFont = objGF[key][weidht],
                    weightFont = weidht,
                    parentTitleFont = objGF[key].title_font;

                if(weightFont !== 'title_font') {

                    menu_second.push({
                        text: titleTypeFont,
                        style: weightFont,
                        data: parentTitleFont,
                        onclick: function(params) {

                            var titleParentMenu = params.control.settings.data,
                                nativeWeight = params.control.settings.style,
                                titleMenu = params.control.text(),
                                regExp = /\d+/g,
                                numberWeight,
                                newWeight,
                                style = '';

                            if ((numberWeight = regExp.exec(nativeWeight)) != null) {
                                newWeight = numberWeight[0];
                                var styleParam = nativeWeight.substring(newWeight.length);

                                if(styleParam) {
                                    var inner_style = nativeWeight.substring(newWeight.length);
                                    if(inner_style == 'i') {
                                        inner_style = 'italic';
                                    }
                                    style = 'font-style: ' + inner_style + ';';
                                }
                            } else {
                                newWeight = 'normal';
                            }

                            editor.focus();

                            //console.log(titleParentMenu, nativeWeight, titleMenu);

                            editor.selection.setContent('<span style="font-family: \''+titleParentMenu+'\', sans-serif; font-weight: '+newWeight+'; '+style+'" >' + editor.selection.getContent({ 'format' : 'text' }) + '</span>');
                        }
                    });
                    //console.log(obj[key][typez]);   //obj[key][typez] Normal
                }
            }


            menu_construct.push({
                text : objGF[key].title_font,
                menu : menu_second
            });

        }


        //console.log(menu_construct);




        editor.addButton( 'my_mce_button', {
            text: 'Google Fonts',
            icon: 'basement_google_icon',
            plugins: "directionality",
            type: 'menubutton',
            menu: menu_construct
        });

    });

})();