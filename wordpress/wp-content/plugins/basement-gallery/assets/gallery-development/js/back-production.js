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


    function Basement_Gallery() {
        var that = this;


        $win.on('load', function(){

            // Detect row type
            that.typeRowGallery();

            // Load more settings
            that.loadMoreGallery();

            // Title Gallery
            that.titleGallery();

            // Type tiles
            that.tilesTypeGallery();

            // Type grid
            that.gridTypeGallery();

            // Margin  value
            that.marginValue();

            // Build Preview Arrow&Dots
            that.builderArrowDots();


            // Check position in Top Bar
            that.positionTopElements();

        }).on('resize', function(){


        }).on('scroll', function(){

        })

    }

    Basement_Gallery.prototype = {
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

            $doc.find('.basement_position-compare:checked').map(function(i,o){
                var value = $(this).val(),
                    allValues = [],
                    values = ['left','center','right'],
                    $siblings = $(this).closest('.basement_html_param-setting-root').siblings();

                allValues.push(value);


                if(i>0) {
                    if (value == allValues[--i]) {
                        removeArray(values, value);
                        if(values) {
                            $siblings.find('.basement_position-compare[value="'+value+'"]:checked').closest('.basement_form_radios').find('.basement_position-compare[value="'+values[0]+'"]').prop({
                                checked : true
                            });
                        }
                    }
                }
            });




            $radiosNorm.on('change',function(){
                var $this = $(this),
                    value = $this.val(),
                    values = ['left','center','right'],
                    $siblings = $this.closest('.basement_html_param-setting-root').siblings();

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

        gridTypeGallery : function() {
            var scope = this,
                $control = $('#js-grid-type-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val(),
                current = 'masonry',
                current2 = 'mixed';

            if(checked === current || checked === current2) {
                $('#_basement_meta_grid_tiles_height').prop({
                    'readonly' : true,
                    'disabled' : true
                });
                $('#layer_mode_show_yes').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current || $(this).val() === current2) {
                    $('#_basement_meta_grid_tiles_height').prop({
                        'readonly' : true,
                        'disabled' : true
                    });
                    $('#layer_mode_show_yes').addClass('active');
                } else {
                    $('#_basement_meta_grid_tiles_height').prop({
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
                $('#_basement_meta_grid_margins_yes').prop( 'checked', true);
                $('#_basement_meta_grid_margins_no').prop( 'disabled', true);
                $('#header_position').addClass('active');
            }

            $radio.on('click', function(){
                if($(this).val() === current) {
                    $('#_basement_meta_grid_margins_yes').prop( 'checked', true);
                    $('#_basement_meta_grid_margins_no').prop( 'disabled', true);
                    $('#header_position').addClass('active');
                } else {
                    $('#_basement_meta_grid_margins_no').prop( 'disabled', false);
                    $('#header_position').removeClass('active');
                }
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

        typeRowGallery : function() {
            var scope = this,
                $control = $('#js-row-choose'),
                $radio = $control.find('input:radio'),
                checked = $control.find('input:radio:checked').not(':disabled').val();
            $('#' + checked).addClass('active');
            if(checked === 'singlerow') {
                $('#_basement_meta_grid_grid_type_grid').prop('checked',true).trigger('click');
            }
            $radio.on('click', function(){
                if($(this).val() === 'singlerow') {
                    $('#_basement_meta_grid_grid_type_grid').prop('checked',true).trigger('click');
                }
                $('#' + $(this).val()).addClass('active').siblings($(this).val() === 'multirow' ? '#singlerow' : '#multirow').removeClass('active');
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


    new Basement_Gallery;

})(jQuery, window, document);