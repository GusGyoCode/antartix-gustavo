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


    function Basement_Carousel(element, options) {
        var that = this;


        $win.on('load', function(){

            // Set fixed height for Carousel
            that.selectChooseInput();

            // Stretch Row Magic. Pass the data name element. Values (strow, strow_cont, strow_cont_pad)
            that.stretch('[data-stretch]');

            // Generate preview Carousel
            that.previewCarouselGenerate();

            // Settings Tabs
            that.metaboxTabs();

            // Build Preview Arrow&Dots
            that.builderArrowDots();

            // Inside Side Arrows
            that.arrowPreviewInsideSide();



        }).on('resize', function(){

            // Stretch Row Magic. Pass the data name element. Values (strow, strow_cont, strow_cont_pad)
            that.stretch('[data-stretch]');
            that.arrowPreviewInsideSide();

        }).on('scroll', function(){

        })

    }

    Basement_Carousel.prototype = {

        arrowPreviewInsideSide : function() {
            var instance = this,
                $rowPreviewArrows = $('#preview-arrows');

            if($rowPreviewArrows.size() >0 && $rowPreviewArrows.hasClass('preview-position-inside preview-vertical-side')) {
                var $fakeWindow = $('.carousel-area'),
                    widthWindow = $fakeWindow.width(),
                    $caroRow = $('.builder-work-row'),
                    caroRowWidth = $caroRow.outerWidth(),
                    arrowWidth = $rowPreviewArrows.children('a').width(),
                    halfs,
                    half;

                //console.log($('.builder-work-row').outerWidth(), caroRowWidth);

                halfs = Math.floor((widthWindow-caroRowWidth) / 2);
                //halfs-arrowWidth press arrows to border
                half = Math.floor((halfs-arrowWidth) / 2);

                if(caroRowWidth <= widthWindow) {
                    setTimeout(function(){

                        $rowPreviewArrows.css({
                            'paddingLeft' : half <=0 ? 0 : half + 'px',
                            'paddingRight' : half <=0 ? 0 : half + 'px'
                        });

                        //console.log(half);

                    }, 100);
                }
            }
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

        },

        metaboxTabs : function(){
            var instance = this,
                $nav = $('.z_tabs-nav'),
                $btn = $('#basement-generate-preview'),
                id = $btn.data('id'),
                status = localStorage.getItem("tabLocal"+id) ? localStorage.getItem("tabLocal"+id) : '#';

            if($nav.size() > 0) {

                if(status !== '#') {
                    setTimeout(function(){
                        $nav.find('a[href="'+status+'"]').trigger('click');
                    }, 30);
                }

                $nav.each(function(){
                    var $this = $(this);

                    $this.find('a').on('click', function(e){
                        e.preventDefault();

                        $(this).parent('li').addClass('active').siblings().removeClass('active');

                        $($(this).attr('href')).addClass('active').siblings().removeClass('active');


                        localStorage.setItem("tabLocal"+id, $(this).attr('href'));

                        /*if(history.pushState) {
                         history.pushState(null, $(this).attr('href'));
                         }
                         else {
                         location.hash = $(this).attr('href');
                         }*/
                    });

                    /*var hash = $.trim( window.location.hash );

                     if (hash)*/

                });
            }

        },

        stretch : function(selector){

            if($(selector).size() > 0) {

                var $rowElement = $(selector);

                $rowElement.each(function(){
                    var $thisRow = $(this),
                        clearDataName = selector.substring(6, selector.length-1),
                        valueElement = $thisRow.data(clearDataName),
                        $fullWidthElement = $thisRow.next('.full-width-basement'),
                        rowMarginLeft = parseInt( $thisRow.css('margin-left'), 10),
                        rowMarginRight = parseInt( $thisRow.css( 'margin-right' ), 10),
                        offsetLeft = 0 - $fullWidthElement.position().left - rowMarginLeft,
                        $fakeWindow = $('.carousel-area'),
                        width = $fakeWindow.outerWidth();

                    $thisRow.css({
                        'width' : width,
                        'position' : 'relative',
                        'left': offsetLeft,
                        'box-sizing': 'border-box'
                    });

                    if(valueElement === 'strow') {

                        var padding = (- 1 * offsetLeft);
                        if ( padding < 0 ) {
                            padding = 0;
                        }
                        var paddingRight = width - padding - $fullWidthElement.width() + rowMarginLeft + rowMarginRight;
                        if ( paddingRight < 0 ) {
                            paddingRight = 0;
                        }
                        $thisRow.css({
                            'padding-left': padding,
                            'padding-right': paddingRight
                        });
                    }

                });
            }
        },

        numberCheck : function(value) {
            return !isNaN(value) &&
                parseInt(Number(value)) == value &&
                !isNaN(parseInt(value, 10));
        },

        findMaxElement : function(carousel) {
            return Math.max.apply(Math, carousel.children('li').map(function() {
                return $(this).outerHeight(true);
            }));
        },

        previewCarouselGenerate : function() {
            var instance = this,
                $btn = $('#basement-generate-preview'),
                $btnClose = $('#basement-close-preview'),
                $carousel = $('#preview-basement-carousel'),
                params = $carousel.data('params-preview'),
                $preview = $('#carousel-preview-meta-box'),
                id = $btn.data('id'),
                $total = $($carousel.data('total')),
                setTotal = function( number ) {
                    $total.find('.preview-all').text(number);
                },
                setCurrent = function( current ) {
                    $total.find('.preview-current').text(current);
                },
                status = localStorage.getItem("statusLocal"+id) ? localStorage.getItem("statusLocal"+id) : 'false';

            if(status === 'true') {
                $preview.addClass('active-metabox');
                $btnClose.removeClass('hidden');
                $btn.addClass('hidden');
            } else {
                $preview.removeClass('active-metabox');
                $btn.removeClass('hidden');
                $btnClose.addClass('hidden');
            }


            $btn.on('click', function(e){
                e.preventDefault();
                var $thisBtn = $(this),
                    href = $thisBtn.attr('href');
                $(href).addClass('active-metabox');
                $btnClose.removeClass('hidden');
                $thisBtn.addClass('hidden');
                localStorage.setItem("statusLocal"+id, 'true');
            });


            $btnClose.on('click', function(e){
                e.preventDefault();
                var $thisBtn = $(this),
                    href = $thisBtn.attr('href');
                $(href).removeClass('active-metabox');
                $thisBtn.addClass('hidden');
                $btn.removeClass('hidden');
                localStorage.setItem("statusLocal"+id, 'false');
            });


            if($carousel.size() > 0) {
                $carousel.each(function(){
                    var $thisEl = $(this),
                        $numberPaginate = $('#preview-paginate');

                    var customObj = {
                        onCreate: function (data) {

                            $win.on('resize', function(){
                                setTimeout(function(){
                                    var paramHeight = params.height,
                                        height = instance.numberCheck(paramHeight),
                                        maxHeight = instance.findMaxElement($thisEl);

                                    if(height) {
                                        //console.log('Max - '+maxHeight + ' | '+ paramHeight);

                                        if(maxHeight >= paramHeight) {
                                            //console.log('Auto height');
                                            $thisEl.trigger("configuration", {
                                                height : 'auto'
                                            });
                                        }
                                    } else {

                                        if(paramHeight === 'auto') {
                                            //console.log('Max - ' + maxHeight + ' | ' + paramHeight);

                                            $thisEl.trigger("configuration", {
                                                height: maxHeight
                                            });
                                        }
                                    }

                                }, 100);


                            }).trigger('resize');


                            if($numberPaginate.hasClass('preview-type-number')) {

                                var total = data.items.prevObject.length;
                                if(total > 1) {
                                    setTotal(total);
                                    setCurrent( $thisEl.triggerHandler('currentPosition') + 1 );
                                } else {
                                    $total.hide();
                                }
                            }
                        }
                    };

                    if($numberPaginate.hasClass('preview-type-number')) {
                        params.scroll.onBefore = function () {
                            setCurrent($thisEl.triggerHandler('currentPosition') + 1);
                        };
                    }

                    $.extend( params, customObj );

                    $thisEl.carouFredSel(params);
                });
            }
        },

        getCookie: function (name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        },
        selectChooseInput : function() {
            var elements = {
                '#_basement_meta_carousel_height' : 'js_basement_fixed_height',
                '#_basement_meta_carousel_item_height' : 'js_basement_fixed_item_height'
            },
            selectLogic = function(value, target) {
                if(value === target) {
                    $('#'+target).show();
                } else {
                    $('#'+target).hide().find('input').val('');
                }
            };
            
            for(var element in elements) {
                var $element = $(element),
                    id = elements[element],
                    selected = $element.find('option:selected' ).val();

                if($element.size() > 0) {
                    selectLogic(selected, id);
                    $element.on('change', function() {
                        var $this = $(this),
                            currentValue = $this.val(),
                            id =  elements['#'+$this.attr('id')];
                        selectLogic(currentValue, id);
                    });
                }

            }

        }

    };


    new Basement_Carousel;

})(jQuery, window, document);