(function($, window, document){
    'use strict';

    var $win = $(window),
        $doc = $(document),
        carouselParams = {
            dots : {
                inside : {
                    top : '-26px',
                    bottom: '-26px',
                    left : '0px',
                    center : {
                        left : '50%',
                        marginLeft: '-24px'
                    },
                    right : '0px'
                },
                outside : {
                    top : '-72px',
                    bottom: '-72px',
                    left : '0px',
                    center : {
                        left : '50%',
                        marginLeft: '-24px'
                    },
                    right : '0px'
                }
            },
            arrow : {
                inside : {
                    top : '-27px',
                    bottom: '-27px',
                    left : '0px',
                    center : {
                        left : '50%',
                        marginLeft: '-17px'
                    },
                    right : '0px',
                    side : {
                        marginLeft : '-35px',
                        marginRight : '-35px',
                        left : '0px',
                        right : '0px',
                        marginTop : '-6px',
                        top : '50%'
                    }
                },
                outside : {
                    top : '-72px',
                    bottom: '-72px',
                    left : '0px',
                    center : {
                        left : '50%',
                        marginLeft: '-17px'
                    },
                    right : '0px',
                    side : {
                        marginLeft : '-60px',
                        marginRight : '-60px',
                        left : '0px',
                        right : '0px',
                        marginTop : '-6px',
                        top : '50%'
                    }
                },
                inrow : {
                    marginTop : '-6px',
                    top : '50%',
                    width : '100%'
                }
            }
        },
        flagInrow = false;


    function Basement_Portfolio() {
        var that = this;


        $win.on('load', function(){

            // Title Portfolio
            that.titleGallery();

            // Check position in Top Bar
            that.positionTopElements();

            // Type projects
            that.tilesTypeGallery();

            // Type grid
            that.gridTypeGallery();

            // Load more settings
            that.loadMoreGallery();

            // Media uploader for Basement buttons
            that.metaboxGallery();

            // Build Preview Arrow&Dots
            that.builderArrowDots();

            // Build Custom Fields
            that.customFields();

            // Gallery type
            that.typeGallery();

            // Featured works
            that.featuredWorks();

            // Pagination
            that.pagination();

            // Margin  value
            that.marginValue();

        }).on('resize', function(){


        }).on('scroll', function(){

        })

    }

    Basement_Portfolio.prototype = {

        pagination : function () {
            var scope = this,
                $pagination = $('#basement_pagination_shoose'),
                $radio = $pagination.find('input:radio'),
                checked = $pagination.find('input:radio:checked').not(':disabled').val(),
                current = 'yes';
            
            if(checked === current) {
                $('.basement_pagination_settings').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current) {
                    $('.basement_pagination_settings').addClass('active');
                } else {
                    $('.basement_pagination_settings').removeClass('active');
                }
            });

        },

        featuredWorks : function () {
            var $featuredBlock = $('.basement-featured-works');

            $featuredBlock.each(function () {
                var $thisBlock = $(this),
                    $sourceSortable = $thisBlock.find('.basement-featured-source-sortable'),
                    $featureSortable = $thisBlock.find('.basement-featured-feature-sortable'),
                    $inputProjects = $thisBlock.find('.projects-ids'),
                    projects = [];

                /*$sourceSortable.sortable({
                    cursor: "move",
                    placeholder: 'ui-state-highlight',
                    handle: '.basement-source-remove',
                    update: function(event, ui) {
                        console.log('Update');
                    }
                }).disableSelection();*/

                if($inputProjects.val().length > 0) {
                    projects = $inputProjects.val().split(',');
                }

                function updateFeatures() {
                    projects = [];
                    $inputProjects.val('');

                    $featureSortable.children('div').each(function () {
                        projects.push($(this).data('post'));
                    });

                    $inputProjects.val(projects.join()).trigger('change');
                }

                $doc.on('click', '.basement-source-remove', function(e){
                    e.preventDefault();

                    var $this = $(this),
                        $line = $this.closest('.basement-featured-item'),
                        id = $line.data('post');

                    var $copied = $line.clone().appendTo($featureSortable);
                    $line.remove();

                    projects.push(id);

                    $inputProjects.val(projects.join()).trigger('change');
                });

                $doc.on('click', '.basement-featured-remove', function(e){
                    e.preventDefault();
                    
                    var $this = $(this),
                        $line = $this.closest('.basement-featured-item');

                    $sourceSortable.children('p').remove();

                    var $copied = $line.clone().appendTo($sourceSortable);
                    $line.remove();

                    updateFeatures();
                });



                $featureSortable.sortable({
                    axis: 'y',
                    cursor: "move",
                    placeholder: 'ui-state-highlight',
                    handle: '.basement-featured-move',
                    update: function(event, ui) {
                        updateFeatures();
                    }
                }).disableSelection();

            });

        },

        typeGallery : function() {
            var scope = this,
                $control = $('#js-gallery-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val();
            $('#' + checked).addClass('active');

            $radio.on('click', function(){
                $('#' + $(this).val()).addClass('active').siblings($(this).val() === 'video_gallery_type' ? '#image_gallery_type' : '#video_gallery_type').removeClass('active');
            });
        },


        customFields : function() {
            var $cfBlock = $('.basement_custom-fields-block'),
                $cfSelect = $('.basement_custom-fields-select'),
                cfiedls = [];


            function updateIndexes($sortable) {
                $sortable.children('div').each(function () {
                    var $this = $(this),
                        id = $this.index()+1,
                        name = $this.find('.basement-snap-field').data('name');
                    $this.attr('data-id',id);
                    $this.find('.basement-snap-field').attr('name', name + '['+id+']').trigger('change');
                });
            }


            function updateAfterSortable($sortable) {
                var $wrapBlock = $sortable.closest('.basement_custom-fields-block'),
                    $mainInput = $wrapBlock.find('.basement_custom-fields-ids');

                cfiedls = [];
                $mainInput.val('');

                $sortable.children('div').each(function () {
                    cfiedls.push($(this).data('field'));
                });
                updateIndexes($sortable);
                $mainInput.val(cfiedls.join()).trigger('change');
            }


            function generateField($field, id, type, slug, textValue) {
                var today = new Date(),
                    second = today.getSeconds(),
                    value = 'field-'+second+ Math.random().toString(36).substr(2, 9);

                $field.find('.basement-snap-field').val(value).attr('value',value).trigger('change');

                $field.find('.fast-edit-custom-fields input, .fast-edit-custom-fields select, .fast-edit-custom-fields textarea').each(function () {
                   $(this).attr('name','_basement_meta_project_'+value+'[]');
                });

                updateIndexes($field.closest('.basement_custom-fields-sortable'));
            }


            $cfBlock.each(function () {
                var $cfThis = $(this);
                $cfThis.find('.basement_custom-fields-sortable').each(function(){
                    $(this).sortable({
                        axis: 'y',
                        cursor: "move",
                        placeholder: 'ui-state-highlight',
                        handle: '.custom-field-handle',
                        update: function(event, ui) {
                            updateAfterSortable($(this));
                            //console.log(ui.item.index());
                        }
                    }).disableSelection();
                });


                if($cfThis.find('.basement_custom-fields-ids').val().length > 0) {
                    cfiedls = $cfThis.find('.basement_custom-fields-ids').val().split(',');
                }

            });

            $doc.on('click','.custom-field-remove', function(e){
                e.preventDefault();
                var answer = window.confirm("Are you sure?"),
                    $sortable = $(this).closest('.basement_custom-fields-sortable'),
                    $line = $(this).closest('.ui-state-default');

                if(answer) {
                    if (ajaxurl) {
                        $line.addClass('load');
                        $line.removeClass('load').delay(500).remove();
                        updateAfterSortable($sortable);

                        if($line.hasClass('is-static')) {
                            $sortable.addClass('load');
                            $.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    'action': 'remove-custom-field',
                                    'data': {
                                        'post_id': $line.data('post'),
                                        'index': $line.data('id'),
                                        'field': $line.data('field'),
                                        'ids': $sortable.closest('.basement_custom-fields-block').find('.basement_custom-fields-ids').val(),
                                        'value': $line.find('.basement-snap-field').val()
                                    }
                                },
                                success: function (response, status) {
                                    $sortable.removeClass('load');
                                }
                            });
                        }

                    }
                }
                
            });


            $doc.on('click','.custom-field-edit', function(e){
                e.preventDefault();
                var $this = $(this),
                    $line = $this.closest('.ui-state-default'),
                    $handle = $line.find('.custom-field-handle'),
                    $remove = $line.find('.custom-field-remove'),
                    $fastEdit = $line.find('.fast-edit-custom-fields');


                if($this.hasClass('edited')) {
                    $this.removeClass('edited');
                    $this.text('Edit');
                    $remove.removeClass('disable-block');
                    $handle.removeClass('disable-block');
                    $fastEdit.slideUp('slow');
                } else {
                    $this.addClass('edited');
                    $this.text('Close');
                    $remove.addClass('disable-block');
                    $handle.addClass('disable-block');
                    $fastEdit.slideDown('slow');
                    
                }
                
                
            });


            $cfSelect.on('change',function () {
                var $this = $(this),
                    $option = $this.find("option:selected"),
                    value = $option.val(),
                    field = $option.data('type'),
                    slug = $option.data('slug'),
                    textValue = $option.text(),
                    $sortFields = $($this.data('fields')),
                    $templateFields = $('div[data-templates="'+$this.data('fields').slice(1)+'"]'),
                    $select = $templateFields.find('.field-'+field),
                    $wrapBlock = $this.closest('.basement_custom-fields-block'),
                    $mainInput = $wrapBlock.find('.basement_custom-fields-ids');

                if(value && $select.size() > 0) {
                    var $copied = $select.clone().appendTo($sortFields);

                    $copied.attr('data-field',value);
                    $copied.find('.custom-field-title').text(textValue);

                    generateField($copied, value, field, slug, textValue);

                    cfiedls.push(value);

                    $mainInput.val(cfiedls.join()).trigger('change');

                    $this.prop('selectedIndex',0);
                }

            });


        },

        metaboxGallery : function() {
            var $btnUpload = $('.basement_gallery-add-slide'),
                $galleryBlock = $('.basement_gallery-block'),
                slides = [];


            function updateSlides($sortable) {
                var $input = $sortable.closest('.basement_gallery-block').find('.basement_gallery_ids');
                slides = [];
                $input.val('');

                $sortable.children('li').each(function () {
                    slides.push($(this).data('slide'));
                });

                $input.val(slides.join()).trigger('change');
            }

            $galleryBlock.each(function(){
                var $this = $(this),
                    $sortable = $this.find('.basement_gallery-slides'),
                    $input = $this.find('.basement_gallery_ids');

                if($input.size() > 0 && $input.val()) {
                    slides = $input.val().split(',');
                }

                $sortable.sortable({
                    placeholder: 'ui-state-highlight',
                    cursor: "move",
                    update: function() {
                        updateSlides($sortable);
                    }
                });
                $sortable.disableSelection();
            });

            function addSlide(params) {
                var $gallery = params.gallery,
                    url = params.url,
                    id = params.id,
                    $button = params.button,
                    $galleryBlockInput = $button.closest('.basement_gallery-block').find('.basement_gallery_ids');

                slides.push(id);

                $galleryBlockInput.val(slides.join()).trigger('change');

                $gallery.append('<li data-slide="'+id+'" class="ui-state-default"><span class="basement_gallery-helper"></span><a href="#" class="basement_gallery-slide-del" title="">&times;</a><img src="'+url+'" alt=""></li>');
            }

            $doc.on('click','.basement_gallery-slide-del', function(e){
                e.preventDefault();
                var $sortable = $(this).closest('.basement_gallery-slides');
                $(this).closest('li').remove();
                updateSlides($sortable);
            });

            $btnUpload.on('click', function( event ){
                var $that = $(this),
                    libraryType = $(this).data( 'library-type' ),
                    frameTitle = $(this).data( 'frame-title' ),
                    frameButtonText = $(this).data( 'button-text' );

                if (!libraryType) {
                    libraryType = 'image';
                }

                if (!frameTitle) {
                    frameTitle = 'Choose';
                }

                if (!frameButtonText) {
                    frameButtonText = 'Update';
                }

                event.preventDefault();

                var media_uploader_frame = null;


                media_uploader_frame = wp.media.frames.customHeader = wp.media( {

                    title: frameTitle,
                    button: {
                        text: frameButtonText
                    },
                    multiple: 'add'
                });

                media_uploader_frame.on( "select", function() {
                   /* var id = media_uploader_frame.state().get("selection").first().attributes.id,
                        url = media_uploader_frame.state().get("selection").first().attributes.url;

                    addSlide({
                        id : id,
                        url : url,
                        gallery : $($that.attr('href')),
                        button : $that
                    });*/
                    var selection = media_uploader_frame.state().get('selection');
                    selection.map( function( attachment ) {
                        var selection = attachment.toJSON(),
                            id = selection.id,
                            url = selection.url;
                        addSlide({
                            id : id,
                            url : url,
                            gallery : $($that.attr('href')),
                            button : $that
                        });
                    });
                });

                media_uploader_frame.open();
                return false;
            });

        },

        loadMoreGallery : function(){
            var scope = this,
                $control = $('#js-load-more-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val(),
                current = 'yes';

            if(checked === current) {
                $('#load_more_yes').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current) {
                    $('#load_more_yes').addClass('active');
                } else {
                    $('#load_more_yes').removeClass('active');
                }
            });
        },

        gridTypeGallery : function() {
            var scope = this,
                $control = $('#js-grid-type-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val(),
                current = 'masonry',
                current2 = 'mixed';

            if(checked === current || checked === current2 ) {
                $('#_basement_meta_portfolio_grid_tiles_height').prop({
                    'readonly' : true,
                    'disabled' : true
                });
                $('#layer_mode_show_yes').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current || $(this).val() === current2) {
                    $('#_basement_meta_portfolio_grid_tiles_height').prop({
                        'readonly' : true,
                        'disabled' : true
                    });
                    $('#layer_mode_show_yes').addClass('active');
                } else {
                    $('#_basement_meta_portfolio_grid_tiles_height').prop({
                        'readonly' : false,
                        'disabled' : false
                    });
                    $('#layer_mode_show_yes').removeClass('active');
                }
            });
        },

        tilesTypeGallery : function() {
            var scope = this,
                $control = $('#js-tiles-type-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val(),
                current = 'classic';

            if(checked === current) {
                $('#_basement_meta_portfolio_grid_margins_yes').prop( 'checked', true);
                $('#_basement_meta_portfolio_grid_margins_no').prop( 'disabled', true);

                $('#header_position').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current) {
                    $('#_basement_meta_portfolio_grid_margins_yes').prop( 'checked', true);
                    $('#_basement_meta_portfolio_grid_margins_no').prop( 'disabled', true);
                    $('#header_position').addClass('active');
                } else {
                    $('#_basement_meta_portfolio_grid_margins_no').prop( 'disabled', false);
                    $('#header_position').removeClass('active');
                }
            });
        },
        marginValue : function () {
            var scope = this,
                $control = $('#js-margin-value-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val(),
                current = 'yes';

            if(checked === current) {
                $('#margin_value_mode_show_yes').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current) {
                    $('#margin_value_mode_show_yes').addClass('active');
                } else {
                    $('#margin_value_mode_show_yes').removeClass('active');
                }
            });
        },
        positionTopElements : function() {
            var $radiosNorm = $('.basement_position-compare');

            function removeArray(arr) {
                var what, a = arguments, L = a.length, ax;
                while (L > 1 && arr.length) {
                    what = a[--L];
                    while ((ax= arr.indexOf(what)) !== -1) {
                        arr.splice(ax, 1);
                    }
                }
                return arr;
            }


            $radiosNorm.on('change',function(){
                var $this = $(this),
                    value = $this.val(),
                    values = ['left','center','right'],
                    $siblings = $this.closest('.basement_html_param-setting').siblings();

                removeArray(values, value);

                $siblings.find('.basement_position-compare:checked').each(function(){
                    for(var i = 0; i<=values.length; i++) {
                        if(values[i] === $(this).val()) {
                            removeArray(values, $(this).val());
                        }
                    }
                });

                if(values) {
                    $siblings.find('.basement_position-compare[value="'+value+'"]:checked').closest('.basement_form_radios').find('.basement_position-compare[value="'+values[0]+'"]').prop({
                       checked : true
                    });
                }

            });



        },

        titleGallery : function () {
            var scope = this,
                $control = $('#js-title-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val(),
                current = 'yes';

            if(checked === current) {
                $('#title_show_yes, #titlegrid').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current) {
                    $('#title_show_yes, #titlegrid').addClass('active');
                } else {
                    $('#title_show_yes, #titlegrid').removeClass('active');
                }
            });
        },


        dotsMove : function(dotsSettings, dotsBuilder, arrowDotsBuilder, arrowBuilder) {
            var instance = this,
                animate = {},
                settings = {
                    position : 'inside',
                    y : 'bottom',
                    x : 'center'
                },
                hide = false;

            $.map(dotsSettings.find('input:radio:checked').not(':disabled'), function(element) {
                var param = $(element).data('control'),
                    value = $(element).val();
                if(param === 'type' && value === 'nope') {
                    hide = true;
                }
                if(param === 'position' || param === 'y' || param === 'x') {
                    settings[param] = value;
                }
            });

            for(var param in settings) {
                var value = settings[param],
                    key = param;

                switch (key) {
                    case 'y' :
                    case 'x' :
                        if(value === 'center') {
                            for (var xyParam in carouselParams.dots[settings.position][value]) {
                                animate[xyParam] = carouselParams.dots[settings.position][value][xyParam];
                            }
                        } else {
                            animate[value] = carouselParams.dots[settings.position][value];
                        }
                        break;
                }
            }
            dotsBuilder.hide().attr('style','').css(animate).fadeIn();

            if(hide) {
                $('.layout-nav-builder').hide();
            } else {
                $('.layout-nav-builder').show();
            }

            var compareRes = instance.checkPosition(settings, '#js-arrowSettings');

            if(compareRes) {
                dotsBuilder.hide();
                arrowBuilder.hide();

                if(settings.x === 'center') {
                    animate.marginLeft = '-41px';
                }

                arrowDotsBuilder.css(animate).show();
            } else {
                arrowDotsBuilder.attr('style','').hide();
                arrowBuilder.show();
            }

        },

        radioDisabled : function(arrowSettings) {
            $.map(arrowSettings.find('input:radio:checked'), function(element) {
                var param = $(element).data('control'),
                    value = $(element).val();

                switch (param) {
                    case 'position' :
                        if(value === 'inrow') {
                            arrowSettings.find('input:radio[data-control="y"]').prop( 'disabled', true);
                            arrowSettings.find('input:radio[data-control="x"]').prop( 'disabled', true);
                            flagInrow = true;
                        } else {
                            arrowSettings.find('input:radio[data-control="y"]').prop( 'disabled', false);
                            flagInrow = false;
                        }
                        break;
                    case 'y' :
                        if(value === 'side') {
                            arrowSettings.find('input:radio[data-control="x"]').prop( 'disabled', true);
                        } else {
                            if(!flagInrow) {
                                arrowSettings.find('input:radio[data-control="x"]').prop( 'disabled', false);
                            }
                        }
                        break;
                }
            });
        },

        checkPosition : function(settingsChoose, otherEl) {
            var $element = $(otherEl),
                otherSettings = {};

            $.map($element.find('input:radio:checked').not(':disabled'), function(element) {
                var param = $(element).data('control'),
                    value = $(element).val();
                if(param === 'position' || param === 'y' || param === 'x') {
                    otherSettings[param] = value;
                }
            });

            var chooseParam = JSON.stringify(settingsChoose),
                otherParam = JSON.stringify(otherSettings);

            return chooseParam === otherParam;

        },

        arrowMove : function(arrowSettings, arrowBuilder, arrowDotsBuilder, dotsBuilder) {
            var instance = this,
                animate = {},
                settings = {},
                hide = false;

            instance.radioDisabled(arrowSettings);


            $.map(arrowSettings.find('input:radio:checked').not(':disabled'), function(element) {
                var param = $(element).data('control'),
                    value = $(element).val();

                if(param === 'type' && value === 'nope') {
                    hide = true;
                }

                if(param === 'position' || param === 'y' || param === 'x') {
                    settings[param] = value;
                }
            });


            for(var param in settings) {
                var value = settings[param],
                    key = param;

                switch (key) {
                    case 'position' :
                        if(value === 'inrow') {
                            for (var positionParam in carouselParams.arrow[value]) {
                                animate[positionParam] = carouselParams.arrow[value][positionParam];
                            }
                        }
                        break;
                    case 'x' :
                    case 'y' :
                        if(value === 'side' || value === 'center') {
                            for (var xyParam in carouselParams.arrow[settings.position][value]) {
                                animate[xyParam] = carouselParams.arrow[settings.position][value][xyParam];
                            }
                        } else {
                            animate[value] = carouselParams.arrow[settings.position][value];
                        }
                        break;

                }
            }

            arrowBuilder.hide().attr('style','').css(animate).fadeIn();

            if(hide) {
                $('.layout-arrow-left, .layout-arrow-right').hide();
            } else {
                $('.layout-arrow-left, .layout-arrow-right').show();
            }

            var compareRes = instance.checkPosition(settings, '#js-dotsSettings');

            if(compareRes) {

                arrowBuilder.hide();
                dotsBuilder.hide();

                if(settings.x === 'center') {
                    animate.marginLeft = '-41px';
                }

                arrowDotsBuilder.css(animate).show();
            } else {
                arrowDotsBuilder.attr('style','').hide();
                dotsBuilder.show();
            }
        },

        builderArrowDots : function() {
            var instance = this,
                $arrowSettings = $('#js-arrowSettings'),
                $arrowBuilder = $('.layout-arrows-builder'),
                $dotsSettings = $('#js-dotsSettings'),
                $builderDotsArrow = $('.layout-dots-arrows-builder'),
                $dotsBuilder = $('.layout-dots-builder');

            // Builder Arrow
            instance.arrowMove($arrowSettings, $arrowBuilder, $builderDotsArrow, $dotsBuilder);

            $arrowSettings.find('input:radio').on( 'click', function() {
                instance.arrowMove($arrowSettings, $arrowBuilder, $builderDotsArrow, $dotsBuilder);
            });


            // Builder Dots
            instance.dotsMove($dotsSettings, $dotsBuilder, $builderDotsArrow, $arrowBuilder);

            $dotsSettings.find('input:radio').on( 'click', function() {
                instance.dotsMove($dotsSettings, $dotsBuilder, $builderDotsArrow, $arrowBuilder);
            });

        }

    };


    new Basement_Portfolio;

})(jQuery, window, document);