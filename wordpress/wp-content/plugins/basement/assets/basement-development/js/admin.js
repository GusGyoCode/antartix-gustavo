(function($, window, document){


    var $win = $(window),
        $doc = $(document);


    function Basement() {
        var that = this;

        $win.on('load', function(){

            // Shortcode tabs
            that.tabsWork();

            // Colorpicker HACK
            that.colorPickerWork();

            // RevSlider Settings
            that.revSlider();

            // Header Meta Box Settings
            that.header();

            // Page Title Meta Box Settings
            that.pagetitle();

            // Click to multitag text
            that.mutlitag();

            // Page Sidebar Meta Box Settings
            that.sidebar();

            // Page Footer Meta Box Settings
            that.footer();

            // Widget Logic
            that.widgetToggle();

            // Nav menu logic
            that.navMenuLogic();

            // Adds new Google Font
            that.addGoogleFont();

            // Help popup for icons and more..
            that.helpPopup();

            // Custom Template Styles
            that.templateChoose();

            // Export header/page title settings
            that.exportSettings();

        }).on('resize', function(){

        }).on('scroll', function(){

        })

    }


    Basement.prototype = {
        exportSettings : function () {
            var $headerExport = $('#basement_export_header_params'),
                header_params = {},
                value = '';


            $headerExport.on('click', function (e) {
                e.preventDefault();


                $(this).closest('.basement-meta-box').next('.basement-meta-box').find('input').each(function () {
                    var name = $(this).attr('name').replace('_basement_meta_header_','');


                    switch ($(this).attr('type')) {
                        case 'radio' :

                            if($(this).is(':checked')) {
                                value = $(this).val();
                            }
                            header_params[name] = value;
                            break;
                        case 'checkbox' :
                            //console.log($(this).is(':checked'), $(this), $(this).val());
                            break;
                        default :
                            value = $(this).val();
                            header_params[name] = value;
                    }

                });

                //console.log(header_params);

            });
        },
        templateChoose : function () {
            var instance = this;

            function fillTemplateSettings(params, prefix) {
                $.each(params, function(index, value) {

                    if(value instanceof Array) {
                        $('[name^="' + prefix + index+'"]:checked').removeAttr('checked');
                        if(value) {
                            for (var i = 0; i < value.length; i++) {
                                var $checkbox = $('[name="' + prefix + index + '[' + value[i] + ']"][value="' + value[i] + '"]');
                                if ($checkbox.size() > 0 && 'checkbox' === $checkbox.attr('type')) {
                                    $checkbox.prop('checked', true);
                                }
                            }
                        }
                    } else {
                        var inputSelector = '[name="'+prefix+index+'"]',
                            $input =  $(inputSelector);

                        if($input.size() > 0) {
                            switch ($input.attr('type')) {
                                case 'radio' :
                                    $(inputSelector + '[value="' + value + '"]').prop('checked', true);
                                    break;
                                default :
                                    $input.val(value).attr({'value': value}).css('background-color', 'transparent').trigger('change');
                            }
                        }
                    }

                });
            }

            function fillRSSettings(params) {
                $.each(params, function(index, value) {

                    var name = '';

                    switch (index) {
                        case 'shortcode' :
                            name = 'revlider_content_meta';
                            var $rsItem = $('[data-slideralias*="'+value+'"]');

                            if(value) {
                                value = '[rev_slider alias="' + value + '"][/rev_slider]';
                            }

                            if($rsItem.size() > 0) {
                                $rsItem.addClass('selected').siblings().removeClass('selected');
                            } else {
                                $('#basement-slider-list > li').removeClass('selected');
                            }
                            break;
                        case 'alias' :
                            name = 'basement_rev_alias';
                            break;
                        case 'rev_position' :
                            name = 'basement_rev_position';
                            break;
                        case 'hide_content' :
                            name = '_basement_meta_hide_content';
                            break;
                    }

                    if(value instanceof Array) {
                        $('[name="' + name +'"]:checked').removeAttr('checked');
                        if(value) {
                            for (var i = 0; i < value.length; i++) {
                                var $checkbox = $('[name="' + name + '"][value="' + value[i] + '"]');
                                if ($checkbox.size() > 0 && 'checkbox' === $checkbox.attr('type')) {
                                    $checkbox.prop('checked', true);
                                }
                            }
                        }
                    } else {
                        var inputSelector = '[name="'+name+'"]',
                            $input =  $(inputSelector);

                        if($input.size() > 0) {
                            switch ($input.attr('type')) {
                                case 'radio' :
                                    $(inputSelector + '[value="' + value + '"]').prop('checked', true);
                                    break;
                                default :
                                    $input.val(value).attr({'value': value}).css('background-color', 'transparent').trigger('change');
                            }
                        }
                    }

                });
            }

            function templateSet(select) {

                var template = select.val(),
                    params = basement_template_params[template],
                    $metaBoxHeader = $('#header_parameters_meta_box'),
                    $metaBoxPageTitle = $('#pagetitle_parameters_meta_box'),
                    $metaBoxRS = $('#basement-revslider-meta-box'),
	                $metaBoxSidebar = $('#sidebar_meta_box'),
	                $metaBoxFooter = $('#footer_meta_box'),
                    headerPrefix = '_basement_meta_header_',
                    pageTitlePrefix = '_basement_meta_pagetitle_',
                    sidebarPrefix = '_basement_meta_single_',
                    footerPrefix = '_basement_meta_single_';

                if(template === 'default') {
                    $metaBoxHeader.find('#basement-custom-header:checked').trigger('click');
                    $metaBoxPageTitle.find('#basement-custom-pagetitle:checked').trigger('click');
	                $metaBoxSidebar.find('#basement-custom-sidebar:checked').trigger('click');
	                $metaBoxFooter.find('#basement-custom-footer:checked').trigger('click');
                    return;
                }


                if(params) {
                    var header_params = params.header,
                        page_title_params = params.page_title,
                        rev_slider_params = params.rev_slider,
                        sidebar_params = params.sidebar,
                        footer_params = params.footer;

                    if(header_params && $metaBoxHeader.size() > 0) {
                        $metaBoxHeader.find('#basement-custom-header:not(:checked)').trigger('click');
                        fillTemplateSettings(header_params, headerPrefix);
                    }

                    if(page_title_params && $metaBoxPageTitle.size() > 0) {
                        $metaBoxPageTitle.find('#basement-custom-pagetitle:not(:checked)').trigger('click');
                        fillTemplateSettings(page_title_params, pageTitlePrefix);
                    }


                    if(rev_slider_params && $metaBoxRS.size() > 0) {
                        fillRSSettings(rev_slider_params);
                    }
	
                    
	                if(sidebar_params && $metaBoxSidebar.size() > 0) {
		                $metaBoxSidebar.find('#basement-custom-sidebar:not(:checked)').trigger('click');
		                fillTemplateSettings(sidebar_params, sidebarPrefix);
	                }
	
	                
	                if(footer_params && $metaBoxFooter.size() > 0) {
		                $metaBoxFooter.find('#basement-custom-footer:not(:checked)').trigger('click');
		                fillTemplateSettings(footer_params, footerPrefix);
	                }
                }


            }

            if (typeof basement_template_params !== 'undefined') {
                var $select_template = $('#page_template');

                //templateSet($select_template);

                $select_template.change(function () {
                    templateSet($(this));
                });
            }

        },
        helpPopup : function() {
            $(document).on('click','.basement-help-popup', function(e){
                e.preventDefault();
                var content = '<div class="popup-content">This is some amazing content!</div>';

                // Call the function to open the popup with the content from var = content
                openPopup(content);
            });

            function openPopup(content){
                var winpops = window.open(
                    'http://docs.aisconverse.com/conicowp/icons/',
                    'Popup Name',
                    //'fullscreen=no,toolbar=yes, status=yes, menubar=yes, scrollbars=yes, resizable=yes, directories=yes, location=yes, width=500, height=400, left=100, top=100, screenX=100, screenY=100'
                    'width=1440, height=800, left=100, top=100, screenX=100, screenY=100'
                );

                //winpops.document.write('<iframe sr>'+content+'</iframe>');
            }
        },
        addGoogleFont : function () {
            var instance = this,
                $btn = $('#basement_add_new_google_font'),
                $field = $('#basement_new_google_font'),
                $spinner = $btn.next('.spinner'),
                $response = $('#basement_new_google_font_response');



            $field.bind('input change paste keyup mouseup', function () {
                var fieldVal = $field.val(),
                    patt = /<link href="(.*?)"/g,
                    patt2 = /url\('(.*?)'\)/g,
                    n,s;
                if (fieldVal.trim()) {
                    while (match = patt.exec(fieldVal)) {
                        if(match[1]) {
                           n = match[1].indexOf('&');
                                s = match[1].substring(0, n != -1 ? n : match[1].length);
                            $field.val(s);
                        }
                    }
                    while (match2 = patt2.exec(fieldVal)) {
                        if(match2[1]) {
                             n = match2[1].indexOf('&');
                                s = match2[1].substring(0, n != -1 ? n : match2[1].length);
                            $field.val(s);
                        }
                    }
                }
            });


            $(document).on('mouseenter mouseleave', '#basement_list_google_fonts label', function (event) {
                var type = event.type,
                    $this = $(this),
                    close = '<span class="basement-remove-google-fonts">&times;</span>';

                if(!$this.find('input[type="checkbox"]').is(':checked') && $this.find('input[type="checkbox"]').val() !== 'Poppins') {
                    if (type == 'mouseenter') {
                        $this.append(close);
                    } else {
                        $this.find('.basement-remove-google-fonts').remove();
                    }
                }
            });

            $(document).on('change', '#basement_list_google_fonts input[type="checkbox"]', function (event) {
                var $this = $(this),
                    close = '<span class="basement-remove-google-fonts">&times;</span>';
                if($this.val() !== 'Poppins') {
                    if (this.checked) {
                        $this.parent('label').find('.basement-remove-google-fonts').remove();
                    } else {
                        $this.parent('label').append(close);
                    }
                }
            });


            $(document).on('click','.basement-remove-google-fonts', function(){
                var $label = $(this).parent('label'),
                    $input = $label.find('input[type="checkbox"]');


                if(!$input.is(':checked') && $input.val() !== 'Poppins') {
                    $input.prop('checked',false);
                    $input.removeAttr('checked');
                    var value = $input.val(),
                        data = {
                            action: 'remove_google_font',
                            font: value
                        };
                    $label.css({
                        'opacity' : '0.5'
                    });

                    $.post( ajaxurl, data, function(response) {
                        if(response) {
                            response = JSON.parse(response);
                            var list = response.list,
                                textareaVal = response.textarea,
                                $list = $(document).find('#basement_list_google_fonts'),
                                $textFonts = $(document).find('#basement_framework_google_fonts');

                            if(textareaVal) {
                                $textFonts.attr('value',textareaVal).val(textareaVal);
                            }

                            if(list) {
                                $list.parent('.basement_settings_panel_block_inputs').html(list);
                            }
                        }
                    });

                }
            });


            $btn.on('click', function(e){
                e.preventDefault();
                var fieldVal = $field.val();
                if (fieldVal.trim()) {
                    var data = {
                        action: 'add_new_google_font',
                        url: fieldVal
                    };
                    $spinner.addClass('is-active');
                    $btn.prop('disabled', true);
                    $field.prop('disabled', true);
                    $response.html('');
                    $.post( ajaxurl, data, function(response) {

                        if(response) {
                            response = JSON.parse(response);
                            var message = response.message,
                                list = response.list,
                                textareaVal = response.textarea,
                                $list = $(document).find('#basement_list_google_fonts'),
                                $textFonts = $(document).find('#basement_framework_google_fonts');

                            $spinner.removeClass('is-active');
                            $btn.prop('disabled', false);
                            $field.prop('disabled', false).val('');

                            $response.html(message);

                            setTimeout(function(){
                                $response.html('');
                            }, 4000);


                            if(textareaVal) {
                                $textFonts.attr('value',textareaVal).val(textareaVal);
                            }

                            if(list) {
                                $list.parent('.basement_settings_panel_block_inputs').html(list);
                            }
                        }
                    });
                }
            });


        },
        navMenuLogic : function() {
            var $megaCheck = $('.menu-item-depth-0 .basement-megamenu-check');
            if($megaCheck.size() > 0) {
                $megaCheck.each(function () {
                   var $this = $(this);

                   if($this.is(':checked')) {
                       $(this).closest('li.menu-item').addClass('basement-item-active-megamenu');
                   } else {
                       $(this).closest('li.menu-item').addClass('basement-item-disable-megamenu');
                   }
                });
            }
        },
        widgetToggle : function () {
            var instance = this,
                toggle = '.wtt';

            $doc.on('click', toggle, function (e) {
                e.preventDefault();
                var $this = $(this),
                    $checkbox = $this.next('input[type="checkbox"]'),
                    $panel = $checkbox.next('.wtp');

                $this.toggleClass('active');
                $panel.toggleClass('active');
                $checkbox.prop("checked",!$checkbox.prop("checked"));

                /*$.ajax({
                    type: 'POST',
                    url: ajaxurl ? ajaxurl : '/wp-admin/admin-ajax.php',
                    data: {
                        'action': 'ajax-generate-widget-settings',
                        'param' : { id: 'calendar-6' }
                    },
                    success: function(response, status){
                        if(response) {
                            console.log(response);
                        }
                    }
                });*/

            });
        },

        footer : function () {
            var $radio = $('#basement-custom-footer:checkbox'),
                checked = $('#basement-custom-footer:checkbox:checked').not(':disabled').val();
            if(checked) {
                $(checked).addClass('active');
            }

            $radio.on('click', function(){
                $($(this).val()).toggleClass('active');
            });
        },
        sidebar : function () {
            var $radio = $('#basement-custom-sidebar:checkbox'),
                checked = $('#basement-custom-sidebar:checkbox:checked').not(':disabled').val();
            if(checked) {
                $(checked).addClass('active');
            }

            $radio.on('click', function(){
                $($(this).val()).toggleClass('active');
            });
        },

        mutlitag : function () {
            $(document).on('click', '.multitext', function () {
                var range, selection;

                if (window.getSelection) {
                    selection = window.getSelection();
                    range = document.createRange();
                    range.selectNodeContents(this);
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else if (document.body.createTextRange) {
                    range = document.body.createTextRange();
                    range.moveToElementText(this);
                    range.select();
                }
            });
        },

        pagetitle : function () {
            var $radio = $('#basement-custom-pagetitle:checkbox'),
                checked = $('#basement-custom-pagetitle:checkbox:checked').not(':disabled').val();
            if(checked) {
                $(checked).addClass('active');
            }

            $radio.on('click', function(){
                $($(this).val()).toggleClass('active');
            });
        },

        header : function () {
            var $radio = $('#basement-custom-header:checkbox'),
                checked = $('#basement-custom-header:checkbox:checked').not(':disabled').val();
            if(checked) {
                $(checked).addClass('active');
            }

            $radio.on('click', function(){
                $($(this).val()).toggleClass('active');
            });

        },

        revSlider : function () {
            var $revMetabox = $('#basement-revslider-meta-box');

            if($revMetabox.size() > 0) {
                $revMetabox.each(function () {
                   var $this = $(this),
                       $listSliders = $this.find('#basement-slider-list'),
                       $itemSlide = $listSliders.children('.rs-slider-modify-li'),
                       $input = $this.find('#basement-revslider'),
                       $alias = $this.find('#basement-alias');

                    $itemSlide.on('click', function (e) {
                        e.preventDefault();

                        var $thisSlide = $(this);

                        $thisSlide.toggleClass('selected').siblings().removeClass('selected');
                        if($thisSlide.hasClass('selected')) {
                            $input.val('[rev_slider alias="'+$thisSlide.data('slideralias')+'"][/rev_slider]').trigger('change');
                            $alias.val($thisSlide.data('slideralias')).trigger('change');
                        } else {
                            $input.val('').trigger('change');
                            $alias.val('').trigger('change');
                        }
                    });

                });
            }

        },
        tabsWork: function(){
            $('.basement_tabs_header a').on('click', function(e){
                e.preventDefault();
                var $thisLink = $(this),
                    $thisLi = $thisLink.parent('li'),
                    thisHref = $thisLink.attr('href');

                $thisLi.addClass('active').siblings().removeClass('active');
                $(thisHref).addClass('active').siblings().removeClass('active');

            });
        },
        colorPickerWork: function() {
            $('#basement_shortcodes_panel_overlay').on('scroll', function(){


                if($('#basement_color_picker_ID').size()>0) {
                    $(".basement_color_picker").blur();
                    $('#basement_color_picker_ID').remove();
                }

            });
        }
    };

    new Basement;

})(jQuery, window, document);