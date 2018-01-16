var $basement_shortcodes = jQuery.noConflict();(function($) {


    var $basement_shortcodes_panel_overlay = $( '#basement_shortcodes_panel_overlay' ),
        $basement_shortcodes_panel = $( '#basement_shortcodes_panel' ),
        $basement_shortcode_panel_button = $( '.basement_shortcode_panel_button' ),
        $basement_shortcode_panel_shortcode_wrapper = $('.basement_shortcode_panel_shortcode_wrapper'),
        basement_active_editor,
        $countdown = $('.input-datepicker'),
        $timedown = $('.input-timepicker'),
        titles = [];



    $( '.basement_shortcodes_back_button' ).click( function() {
        var parent = $( this ).parents( '.basement_settings_panel_section' );
        section = parent.data( 'section' );
        $( '.basement_shortcode_panel_shortcode_wrapper' ).hide();
        $( '.basement_settings_panel_section.active[data-section="' + section + '"]' ).find( '.basement_shortcode_panel_button' ).show();
        return false;
    });

    $.each( $( '.wp-editor-wrap' ), function( index, wrap ) {
        if ( $( wrap ).hasClass( 'html-active' ) ) {
            $( wrap ).find( '.basement_shortcodes_panel_open_button' ).hide();
        }
    } );

    $( '.wp-switch-editor.switch-tmce' ).click( function() {
        $( this ).parents( '.wp-editor-tools' ).find( '.basement_shortcodes_panel_open_button' ).show();
    });

    $( '.wp-switch-editor.switch-html' ).click( function() {
        $( this ).parents( '.wp-editor-tools' ).find( '.basement_shortcodes_panel_open_button' ).hide();
    });

    $( 'body' ).on( 'click', '.basement_shortcodes_panel_open_button', function() {
        $( 'body' ).addClass( 'modal-open' );
        $basement_shortcodes_panel_overlay.show();
        $('.basement_settings_page').trigger('basement_content_changed');
        basement_active_editor = tinyMCE.activeEditor;
        return false;
    });

    $basement_shortcodes_panel_overlay.click( function( e ) {
        if ( $( e.target ).attr( 'id' ) !== 'basement_shortcodes_panel_overlay' ) {
            return true;
        }
        $( 'body' ).removeClass( 'modal-open' );
        $basement_shortcodes_panel_overlay.hide();
    });

    $basement_shortcode_panel_button.click(function() {
        titles = [];
        $basement_shortcode_panel_button.hide();

        var $basement_shortcode_panel_shortcode_form = $basement_shortcode_panel_shortcode_wrapper.filter('[data-name="' + $(this).data('name') + '"]'),
            typeSection = $(this).closest('.basement_settings_panel_section').data('section'),
            dataName = $(this).data('name');

        if(typeSection === 'basement_icons' && $('#ajax-'+dataName+'').hasClass('process-loading')) {


            $.ajax({
                type: 'POST',
                url: ajaxurl ? ajaxurl : '/wp-admin/admin-ajax.php',
                data: {
                    'action': 'ajax-generate-icons',
                    'param' : { type: $(this).data('name'), id : $(this).next('.basement_shortcode_panel_shortcode_wrapper').data('name') }
                },
                success: function(response, status){
                    if(response) {
                        $('#ajax-'+dataName+'').html(response);
                    }
                    $('#ajax-'+dataName+'').removeClass('process-loading');
                }
            });

        }

        $basement_shortcode_panel_shortcode_form.show();
        basement_shortcodes_build_preview( $basement_shortcode_panel_shortcode_form );
    });

    $basement_shortcodes_panel.find('.basement_settings_panel_menu_item a').click(function() {
        $basement_shortcode_panel_shortcode_wrapper.hide();
        $basement_shortcode_panel_button.show();
    });

    var $wrapperAllRows = $('.basement_row-dynamic-wrapper'),
        classWrapRow = '.basement_row-dynamic-wrap',
        $wrapRow = $wrapperAllRows.find(classWrapRow),
        $inpRow = $wrapRow.find('[data-type=text]'),
        dataAddRow = '[data-dyn-field=add]',
        $addRow = $wrapRow.find(dataAddRow),
        dataDelRow = '[data-dyn-field=remove]',
        $delRow = $wrapRow.find(dataDelRow),
        $panel = $( '.basement_shortcode_panel_button'),
        attributes = '',
        htmlRow = '<div class="cf basement_row-dynamic-wrap"><div class="basement_column basement_half_column basement_bootstrap_column_params_wrapper" style="width:36%;margin-right:15px;margin-bottom:5px;"><input type="text" data-type="dynamic-tab"></div><div class="basement_column basement_half_column basement_bootstrap_column_params_wrapper" style="width:auto;margin-right:6px;margin-bottom:5px;"><a class="button-secondary button" href="#" data-dyn-field="add">Add</a></div><div class="basement_column basement_half_column basement_bootstrap_column_params_wrapper" style="width:auto;margin-right:15px;margin-bottom:5px;"><a class="basement_remove-btn" href="#" data-dyn-field="remove">Delete</a></div></div>';





    $(document).on('click', dataAddRow, function(e){
        e.preventDefault();

        var $btn = $(this);

        $dumpBodyRow = $($btn.closest('.basement_row-dynamic-wrap').wrap('<div>').parent().html());

        $btn.closest('.basement_row-dynamic-wrap').unwrap();

        $dumpBodyRow.find('input[type=text]').val('');
        $dumpBodyRow.find('input[type=checkbox]').prop('checked', false);


        $dumpBodyRow.appendTo($btn.closest($wrapperAllRows)).html(function(){
            $(this).closest($wrapperAllRows).children(classWrapRow+':visible').each(function(index, el){
                $(el).find('input[type=checkbox]').val(index+1);
            });
        });

        $btn.parent().hide().closest(classWrapRow).find(dataDelRow).css('display', 'inline-block');

    });


    $(document).on('click', dataDelRow, function(e){
        e.preventDefault();

        var $btn = $(this);

        $btn.closest('.basement_row-dynamic-wrap').find('input').val('').trigger('change');
        $btn.closest('.basement_row-dynamic-wrap').find('input[type=checkbox]').prop('checked', false).trigger('change');



        //$btn.closest('.basement_row-dynamic-wrap').find('input').attr('value','').trigger('change');
        $btn.closest(classWrapRow).hide();
        $btn.closest($wrapperAllRows).children(classWrapRow+':visible').each(function(index, el){
            $(el).find('input[type=checkbox]').val(index+1).trigger('change');
        });

    });


    function basement_build_shortcode( builder, preview ) {


        builder.data('content', '');
        builder.data('params', '');

        if (builder.find('input, select, textarea').length) {
            var collective_params = {};
            $.each(builder.find( 'input, select, textarea' ), function(index, input) {
                var value = '',
                    key = $( input ).data('key'),
                    checked = false,
                    type = $( input ).attr( 'type' ),
                    param_type = $( input ).data( 'type' ),
                    name = $( input ).attr( 'name' ),
                    may_by_empty = $( input ).data('may-be-empty') ;
                if ( param_type === 'content' ) {
                    if ( type == 'radio' ) {
                        builder.data(
                            'content',
                            $( input ).parents( '.basement_shortcodes_panel_block' )
                                .find('input[name="' + $( input ).attr( 'name' ) + '"]:checked')
                                .val()
                        );
                    } else {
                        builder.data('content', $( input ).val() );
                    }
                } else if ( param_type === 'text' || 'color' === param_type || 'select' === param_type ) {
                    if ( ( $( input ).data('key') && $( input ).val() ) || !may_by_empty ) {
                        builder.data('params', builder.data('params') + ' ' + $( input ).data('key') + '="' + $( input ).val() + '"');
                    }
                } else if ( param_type === 'radio') {
                    if ( $( input ).is( ':checked' ) ) {
                        value = $( input ).val();
                        if ( ( key && value ) && ( !may_by_empty || value !== "0" ) ) {
                            builder.data('params', builder.data('params') + ' ' + key + '="' + value + '"');
                        }
                    }
                } else if ( 'check' === param_type ) {
                    if ( $( input ).is( ':checked' ) ) {
                        if ( collective_params[ key ] === undefined ) {
                            collective_params[ key ] = [];
                        }
                        collective_params[ key ].push( $( input ).val() );
                    }

                } else if ( 'toggler' === param_type ) {
                    checked = $( input ).parents( '.basement_shortcodes_panel_block' )
                        .find('input[name="' + $( input ).attr( 'name' ) + '"]:checked')
                        .first();
                    if (checked.length) {
                        builder.data( 'params', builder.data('params') + ' ' + key );
                    }
                } else if (param_type === 'dynamic-tab' ) {
                    var thisVal = $( input ).val(),
                        indexElement = $( input ).closest('.basement_row-dynamic-wrap').index();

                    if(thisVal === '') {
                        if (indexElement > -1) {
                            titles.splice(indexElement, 1);
                        }
                        titles[indexElement] = '';
                    }
                    if(thisVal != '') {
                        titles[indexElement] = '[tab title="'+thisVal+'"][/tab]';
                    }
                } else if (param_type === 'dynamic-accordion' ) {
                    var thisVal = $( input ).val(),
                        indexElement = $( input ).closest('.basement_row-dynamic-wrap').index();

                    if(thisVal === '') {
                        if (indexElement > -1) {
                            titles.splice(indexElement, 1);
                        }
                        titles[indexElement] = '';
                    }
                    if(thisVal != '') {
                        titles[indexElement] = '[accordion_section title="'+thisVal+'"][/accordion_section]';
                    }
                } else if (param_type === 'toggle-accordion' ) {
                    var thisCheckbox = $( input ),
                        indexElement = thisCheckbox.closest('.basement_row-dynamic-wrap').index();

                    if ( thisCheckbox.is( ':checked' ) ) {
                        if ( collective_params[ key ] === undefined ) {
                            collective_params[ key ] = [];
                        }
                        collective_params[ key ].push( thisCheckbox.val() );
                    }

                }
            });

            $.each( collective_params, function( param_key, param_values ) {
                if ( param_values.length ) {
                    builder.data('params', builder.data('params') + ' ' + param_key + '="' + param_values.join() + '"');
                }
            });

        }



        var output = '[' + builder.data( 'tag' );
        if (builder.data('params').length) {
            output += builder.data('params');
        }
        var editor_selection = '';
        if ( builder.data( 'shortcode-enclosing' ) === 1 ) {
            if ( preview ) {
                editor_selection = basement_active_editor.selection.getContent({ 'format' : 'text' });
            } else {
                editor_selection = basement_active_editor.selection.getContent({ 'format' : 'html' });
            }
            if ( preview && editor_selection.length ) {
                editor_selection = '...';
            }
        }
        var content = builder.data('content');
        if ( ( content_wrap = builder.data( 'wrap' ) ) ) {
            content = '<' + content_wrap + '>' + content + '</' + content_wrap + '>';
        }
        output += ']' + titles.join('') + content + editor_selection;
        if ( builder.data( 'shortcode-enclosing' ) === 1) {
            output += '[/' + builder.data( 'tag' ) + ']';
        }
        return output;


    }

    function basement_shortcodes_build_preview( builder ) {
        builder.find('.basement_shortcode_builder_button').find( 'span' ).text( basement_build_shortcode( builder, true ) );

    }

    $(document).on('change keyup', '.basement_shortcodes_panel_block input, .basement_shortcodes_panel_block textarea', function() {
        var builder = $(this).parents('.basement_shortcode_panel_shortcode_wrapper');
        basement_shortcodes_build_preview(builder);
    });

    $('.basement_shortcodes_panel_block').on('change', 'select', function() {
        var builder = $(this).parents('.basement_shortcode_panel_shortcode_wrapper');
        basement_shortcodes_build_preview(builder);
    });

    $('.basement_shortcodes_panel_block').on( 'basement_colorpicker_changed', function() {
        var builder = $(this).parents('.basement_shortcode_panel_shortcode_wrapper');
        basement_shortcodes_build_preview(builder);
    });

    $('.menu a').first().click();

    $('.default-icons input').change(function() {
        $('#' + $(this).parents('.default-icons').data('target')).val($(this).val()).change();
    });

    if ( $( ".sortable-contaner" ).length && $.fn.sortable ) {
        $( ".sortable-contaner" ).sortable({
            placeholder: "basement_sortable_drop_area",
            update: function (e, ui) {
                basement_shortcodes_build_preview($(ui.item).parents( '.basement_shortcode_panel_shortcode_wrapper' ) );
            }
        });
    }

    $('.basement_shortcode_builder_button').click(function() {

        basement_active_editor.execCommand('mceInsertContent', false, basement_build_shortcode($(this).parent('.basement_shortcode_panel_shortcode_wrapper')));

        $( 'body' ).removeClass( 'modal-open' );
        $basement_shortcodes_panel_overlay.hide();
    });


    $('[data-select-action]').on('change', function() {
        var $select = $(this),
            thisData = $select.data('select-action');

        if($select.val()) {
            var $tab = $select.closest('.basement_shortcodes_panel_block_inputs').find('.basement-tabs-icons [data-tab-area*='+$select.val()+']');

            if($tab.hasClass('process-loading')) {
                $tab.addClass('active').siblings().removeClass('active');
                $.ajax({
                    type: 'POST',
                    url: ajaxurl ? ajaxurl : '/wp-admin/admin-ajax.php',
                    data: {
                        'action': 'ajax-generate-icons',
                        'param' : { type: $select.val(), id :  ($select.data('uniq-id') ?  $select.data('uniq-id') : $select.closest('.basement_shortcode_panel_shortcode_wrapper').data('name')), key : $select.data('key-icons') }
                    },
                    success: function(response, status){
                        if(response) {
                            $tab.html(response);
                        }
                        $tab.removeClass('process-loading');
                    }
                });
            } else {
                $tab.addClass('active').siblings().removeClass('active');
            }
            $('.'+thisData+' input').prop('checked', false);
            $('.'+thisData+' select').prop('selectedIndex', false);
        } else {
            $('.'+thisData+' > div').removeClass('active');
            $('.'+thisData+' input').prop('checked', false);
            $('.'+thisData+' select').prop('selectedIndex', false);
        }
    });





if($countdown.size() > 0) {
    $countdown.each(function () {
        var $this = $(this),
            mask = $this.data('mask');

        $this.datepicker({
            dateFormat : mask
        });

    });
}

if($timedown.size() > 0) {
    $timedown.each(function () {
        var $this = $(this),
            mask = $this.data('mask');

        $this.timepicker({
            timeFormat : mask
        });

    });
}



})($basement_shortcodes);


// @koala-prepend "sortable.js"