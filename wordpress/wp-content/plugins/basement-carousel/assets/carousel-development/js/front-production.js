

(function($, window, document){
    'use strict';
    var pluginName = "BasementCarousel",
        defaults = {},
        $win = $(window),
        $doc = $(document),
        initCarousel = $('.basement-carousel'),
        initParams = {};


    function Basement_Carousel(element, options) {
        var that = this;
        that.element = $(element);
        that.options = $.extend({}, defaults, options);


        $win.on('load', function(){
            that.initBasementCarousel(initCarousel, initParams);

        }).on('resize', function(){

        }).on('scroll', function(){

        });

    }

    Basement_Carousel.prototype = {

        stretchControls : function($carousel){
          var scope = this,
              $row = $carousel.closest('.basement-carousel-row'),
              stretch = $row.data('stretch'),
              $fullRowOuter = $row.next('.full-width-basement'),
              $fullRowInner = $row.find('.full-width-basement-help-row'),
              $container = $row.closest('.basement-carousel-container'),
              controls = [
                  '.basement-carousel-inline-controls',
                  '.basement-carousel-merge-controls',
                  '.basement-carousel-dots-controls',
                  '.basement-carousel-arrows-controls'
              ];


            for(var i=0; i < controls.length; i++) {
                var $control = $container.children(controls[i]),
                    controlInner = false;

                if(!$control.size()>0) {
                    $control = $container.find(controls[i]);
                    controlInner = true;
                }


                if($control.size() > 0 && (stretch === 'strow' || stretch === 'strow_cont')) {

                    //scope.stretch(stretch,$inlineControls,$fullRow);
                    var marginLeft = parseInt( $control.css('margin-left'), 10),
                        offsetLeft;


                    if(controlInner && stretch === 'strow') {
                        offsetLeft = -1 - $fullRowInner.offset().left - marginLeft;
                        $control.css({
                            'width' : $(window).width(),
                            'left': offsetLeft,
                            'position' : 'relative',
                            'box-sizing': 'border-box'
                        });
                    } else if(!controlInner && (stretch === 'strow' || stretch === 'strow_cont')) {
                        offsetLeft = -1 - $fullRowOuter.offset().left - marginLeft;
                        $control.css({
                            'width' : $(window).width(),
                            'left': offsetLeft,
                            'position' : 'relative',
                            'box-sizing': 'border-box'
                        });
                    }
                }

            }
        },
        findMinControls : function() {
            var scope = this,
                $rowControls = $('.basement-carousel-inline-controls'),
                minHeight;

            if($rowControls.size() > 0) {
                $.each($rowControls, function(){
                    var $this = $(this),
                        $elements = $this.children('div');
                    if($elements.size() >= 2) {
                        minHeight = scope.findMinElement($this);
                        $.each($elements, function (){
                            if ($(this).outerHeight(true) === minHeight) {
                                $(this).addClass('basement-vertical-controls');
                                return false;
                            }
                        });
                    }
                });
            }
        },
        outsideSideCarouselArrows : function() {
            var $arrowLine = $('.basement-carousel-arrow-outside.basement-carousel-arrow-side');

            if($arrowLine.size()>0) {
                $.each($arrowLine, function(){
                   var $this = $(this),
                        $fullWidthElement = $this.closest('.basement-carousel-help-row').next('.full-width-basement-help-row'),
                        rowMarginLeft = parseInt( $this.css('margin-left'), 10),
                        rowMarginRight = parseInt( $this.css( 'margin-right' ), 10),
                        offsetLeft = -1 - $fullWidthElement.offset().left - rowMarginLeft,
                        width = $win.width();
                    $this.css({
                        'width' : $(window).width(),
                        'left': offsetLeft,
                        'box-sizing': 'border-box'
                    });

                });
            }

        },
        insideSideCarouselArrows : function() {

            var $arrowLine = $('.basement-carousel-arrow-inside.basement-carousel-arrow-side');

            if($arrowLine.size()>0) {
                $.each($arrowLine, function(){
                    var $this = $(this),
                        $fullWidthElement = $this.closest('.basement-carousel-help-row').next('.full-width-basement-help-row'),
                        rowMarginLeft = parseInt( $this.css('margin-left'), 10),
                        rowMarginRight = parseInt( $this.css( 'margin-right' ), 10),
                        offsetLeft = -1 - $fullWidthElement.offset().left - rowMarginLeft,
                        width = $win.width(),
                        caroRowWidth = $this.closest('.basement-carousel-help-row').outerWidth(true),
                        arrowWidth = $this.children('a').outerWidth(true),
                        halfs,
                        half;


                    halfs = Math.floor(($win.width()-caroRowWidth) / 2);
                    //halfs-arrowWidth press arrows to border
                    half = Math.floor((halfs-arrowWidth) / 2);

                    $this.css({
                        'width' : $(window).width(),
                        'left': offsetLeft,
                        'box-sizing': 'border-box'
                    });


                    if(caroRowWidth <= $win.width()) {
                        setTimeout(function(){

                            $this.css({
                                'paddingLeft' : half <=0 ? 0 : half + 'px',
                                'paddingRight' : half <=0 ? 0 : half + 'px'
                            });

                        }, 100);
                    }


                });
            }

        },
        numberCheck : function(value) {
            return !isNaN(value) &&
                parseInt(Number(value)) == value &&
                !isNaN(parseInt(value, 10));
        },
        findMinElement : function(carousel) {
            return Math.min.apply(Math, carousel.children('div').map(function() {
                return $(this).outerHeight(true);
            }));
        },
        findMaxElement : function(carousel) {
            return Math.max.apply(Math, carousel.children('div').map(function() {
                return $(this).outerHeight(true);
            }));
        },
        initBasementCarousel : function( $carousel, getParams ) {
            var scope = this;

                try {
                    if($carousel.size() > 0) {
                        // this animate VC bug remove if you not use VC
                        if(typeof $.fn.viewportChecker == 'function') {
                            $carousel.viewportChecker({
                                offset: '50%',
                                callbackFunction: function(element, action){
                                    setTimeout(function(){
                                        $(element).find('.animated').removeClass('animated');
                                    }, 1100);

                                }
                            });
                        }
                        $.each($carousel, function () {
                            var $this = $(this),
                                dataParams = $this.data('basement-params'),
                                dataTotal = $this.data('basement-total'),
                                $total = $(dataTotal),
                                pagContainer = dataParams.hasOwnProperty('pagination') && dataParams.pagination.hasOwnProperty('container') ? dataParams.pagination.container : null,
                                setTotal = function(number) {
                                    $total.find('.basement-carousel-paginate-all').text(number);
                                },
                                setCurrent = function(current) {
                                    $total.find('.basement-carousel-paginate-current').text(current);
                                },
                                customParams = {
                                    pagination : {
                                        anchorBuilder : function (nr, item) {
                                            return '<a href="#" class="basement-item-pag"><span>'+nr+'</span></a>';
                                        },
                                        container : pagContainer
                                    },
                                    onCreate : function (data) {

                                        $win.on('resize', function(){
                                            setTimeout(function(){
                                                var paramHeight = dataParams.height,
                                                    height = scope.numberCheck(paramHeight),
                                                    maxHeight = scope.findMaxElement($this);
                                                if(height) {
                                                    if(maxHeight >= paramHeight) {
                                                        $this.trigger("configuration", {
                                                            height : 'auto'
                                                        });
                                                    }
                                                } else {


                                                    if(paramHeight === 'auto') {
                                                        $this.trigger("configuration", {
                                                            height: maxHeight
                                                        });
                                                    }
                                                }
                                                scope.insideSideCarouselArrows();
                                                scope.outsideSideCarouselArrows();
                                                scope.findMinControls();
                                                scope.stretchControls($this);
                                            }, 100);

                                        }).trigger('resize');


                                        if($total.size() > 0) {
                                            var total = data.items.prevObject.length;
                                            if(total > 1) {
                                                setTotal(total);
                                                setCurrent( $this.triggerHandler('currentPosition') + 1 );
                                            } else {
                                                $total.hide();
                                            }
                                        }
                                        scope.insideSideCarouselArrows();
                                        scope.outsideSideCarouselArrows();
                                        scope.findMinControls();
                                        scope.stretchControls($this);

                                        /*setTimeout(function () {
                                            $this.find('.wpb_start_animation').removeClass('animated');
                                        }, 1100);*/


                                    }
                                };


                            if($total.size() > 0) {
                                dataParams.scroll.onBefore = function () {
                                    setCurrent($this.triggerHandler('currentPosition') + 1);
                                };
                            }


                            $.extend(dataParams, customParams);

                            $this.carouFredSel(dataParams);
                        });
                    }
                } catch (e) {
                    console.log(e + "\n" + 'Basement Carousel detect error. Please Turn off plugin "Basement Carousel" and please write a message to our support team. Thx!')
                }


        },

        getCookie: function (name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }
    };


    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Basement_Carousel(this, options));
            }
        });
    };

})(jQuery, window, document);


jQuery(document).ready(function($){
    $(document.body).BasementCarousel();
});