(function($, window, document){
    'use strict';
    var pluginName = "BasementGallery",
        defaults = {},
        $win = $(window),
        $doc = $(document),
        initCarousel = $('.basement-gallery-carousel'),
        initParams = {};


    function Basement_Gallery(element, options) {
        var that = this;
        that.element = $(element);
        that.options = $.extend({}, defaults, options);


        $win.load(function () {
            that.stretchGrid();
            that.initBasementGalleryCarousel(initCarousel, initParams);
            that.loadMoreGallery();
            that.magnificPopup();

            $(document).on('click', '.basement-gallery-carousel-tiles_type-classic .figure, .basement-gallery-tiles_type-classic:not(.basement-portfolio-wrapper) .figure', function(){
                if(!$(this).children('a').size() > 0) {
                    $(this).parent('div').find('a').trigger('click');
                }
            });



            $(document).on('click', '.classic-helpers-icons a.icon-arr',function(){
                $(this).closest('h5').children('a').trigger('click');
            });

            that.initGallery();
            that.isotopeMix();



        }).resize(function(){
            that.stretchGrid();
        });

    }

    Basement_Gallery.prototype = {
        magnificPopup: function () {
            var instance = this,
                $magnific = $('.magnific'),
                $magnificWrap = $('.basement-gallery-magnific-wrap'),
                $magnificGallery = $('.magnific-gallery'),
                $magnificVideo = $('.magnific-video');

            $doc.find('.basement-gallery-magnific-wrap').each(function() {
                var $this = $(this);

                $this.find('.magnific').magnificPopup({
                    type: 'image',
                    tLoading: '',
                    gallery: {
                        enabled: true,
	                    tCounter: '<span class="mfp-counter">%curr% &mdash; %total%</span>',
                        navigateByImgClick: true
                    },
                    fixedContentPos: false,
                    callbacks: {
                        open: function() {
                            $('body').addClass('noscroll');
                        },
                        close: function() {
                            $('body').removeClass('noscroll');
                        }
                    },
	                image: {
		                markup: '<div class="mfp-figure">'+
		                '<div class="mfp-close"></div>'+
		                '<div class="mfp-top-bar"></div>'+
		                '<div class="mfp-img"></div>'+
		                '<div class="mfp-bottom-bar">'+
		                '<div class="mfp-title"></div><div class="mfp-counter"></div>'+
		                '</div>'+
		                '</div>',
		                titleSrc: function (item) {
			                return item.el.data('title');
		                }
	                }
                });
            });
        },
        isotopeMix: function () {
            var instance = this,
                $isotopeList = $('.basement-gallery-isotope-list'),
                $navCategory = $('.basement-gallery-nav-category'),
                type = 'masonry';

            if ($isotopeList.size() > 0) {
                var isohsh = window.location.hash !== '' ? window.location.hash : '#all';

                $navCategory.find('a').parent().removeClass('selected');
                $navCategory.find('a[href="' + isohsh + '"]').parent().addClass('selected');

                isohsh = isohsh == '#all' ? '*' : isohsh.replace('#', '.');

                if (isohsh === '*') {
                    $isotopeList.css('opacity', 1);
                }

                //setTimeout(function () {
                if (isohsh !== '*') {
                    $isotopeList.css('opacity', 1);
                }

                /*if($isotopeList.hasClass('cellsByRow-wrap')) {
                    type = 'cellsByRow';
                }*/
                /*else if ($isotopeList.hasClass('cellsByColumn-wrap')) {
                    type = 'cellsByColumn';
                }*/

                $isotopeList.isotope({
                    itemSelector: '.mix',
                    layoutMode: type,
                    percentPosition: true,
                    transitionDuration: '0.3s',
                    filter: isohsh
                });

                //}, 300);
            }
        },
        initGallery : function() {
            var $navCategory = $('.basement-gallery-nav-category'),
                mixList = $('.basement-gallery-mix-list'),
                $isotopeList = $('.basement-gallery-isotope-list');

            if ($navCategory.size() > 0) {

                if (mixList.size() > 0) {

                    var mixer = mixitup(mixList, {
                        callbacks: {
                            onMixClick: function(state, originalEvent) {
                                var $target = $(originalEvent.target);

                                if(!$target.hasClass('filter')) {
                                    return false;
                                }
                            }
                        }
                    });
                }

                $navCategory.find('a').on('click', function (e) {
                    e.preventDefault();
                    var self = $(this),
                        fltr = self.data('filter');

                    self.closest('li').addClass('selected').siblings().removeClass('selected');

                    if ($isotopeList.size() > 0) {
                        $isotopeList.isotope({
                            filter: fltr
                        });
                    }

                });

                $navCategory.find('a span').on('click', function (e) {
                    e.stopPropagation();
                });
            }
        },
        loadMoreGallery : function() {
            var scope = this;
            $(document).on('click', '.basement-load-more', function (e) {
                e.preventDefault();

                var $this = $(this),
                    all = $this.data('all'),
                    need = $this.data('need'),
                    tiles = $this.data('tiles'),
                    load =  $this.data('load'),
                    grid =  $this.data('grid'),
                    $mix = $($this.attr('href')),
                    type = $this.data('type'),
                    ajax_url = basement_gallery_ajax.url;

                var data = {
                    'need' : need,
                    'tiles' : tiles.toString(),
                    'load' : load,
                    'grid' : grid
                };

                $this.addClass('basement-loading').button('loading');

                if(ajax_url) {
                    $.ajax({
                        type: 'POST',
                        url: ajax_url,
                        data: {
                            'action': 'load-more-tiles',
                            'data': data
                        },
                        success: function(response, status){
                            $this.removeClass('basement-loading').button('reset').blur();
                            if(response.html) {
                                var html = $(response.html);
                                if(response.load) {
                                    $this.data('load', response.load);
                                }
                                if (type === 'masonry') {
                                    //$mix.isotope('insert', html);
                                    html.find('img').addClass('basement-img-hidden');
                                    $mix.append( html );
                                    $mix.imagesLoaded()
                                        .always( function(){
                                            html.find('img').removeClass('basement-img-hidden');
                                            $mix.isotope('insert', html);
                                        } );
                                } else {
                                    $mix.mixItUp('append', html );
                                }


                                scope.magnificPopup();

                                if (all === $mix.children('.mix').size()) {
                                    $this.fadeOut().slideUp();
                                }
                            } else {
                                $this.fadeOut().slideUp();
                            }
                        }
                    });
                }


            });

        },
        convertHTML : function ( html ) {
            var newHtml = $.trim( html ),
                $html = $(newHtml ),
                $empty = $();

            $html.each(function ( index, value ) {
                if ( value.nodeType === 1) {
                    $empty = $empty.add ( this );
                }
            });

            return $empty;
        },
        stretchGrid : function() {
            var scope = this,
                $row = $('.basement-gallery-carousel-row, .basement-gallery-wrap-block, .basement-gallery-top-bar, .basement-gallery-carousel-title ');

            $row.each(function(){
                var rowMarginLeft = parseInt( $(this).css('margin-left'), 10),
                    $fullRowOuter = $(this).next('.full-width-basement'),
                    offsetLeft = -1 - $fullRowOuter.offset().left - rowMarginLeft;

                if($(this).data('grid-size') === 'fullwidth') {
                    $(this).css({
                        'width' : $win.width() + 2,
                        'position' : 'relative',
                        'left': offsetLeft,
                        'box-sizing': 'border-box'
                    }).addClass('basement-stretched');
                }
            });


        },

        stretchControls : function($carousel){
          var scope = this,
              $row = $carousel.closest('.basement-gallery-carousel-row'),
              stretch = $row.data('grid-size'),
              $fullRowOuter = $row.next('.full-width-basement'),
              $fullRowInner = $row.find('.full-width-basement-help-row'),
              $container = $row.closest('.basement-gallery-carousel-container'),
              controls = [
                  '.basement-gallery-carousel-inline-controls',
                  '.basement-gallery-carousel-merge-controls',
                  '.basement-gallery-carousel-dots-controls',
                  '.basement-gallery-carousel-arrows-controls'
                  //'.basement-gallery-carousel-title'
              ];


            for(var i=0; i < controls.length; i++) {
                var $control = $container.children(controls[i]),
                    controlInner = false;

                if(!$control.size()>0) {
                    $control = $container.find(controls[i]);
                    controlInner = true;
                }


                if($control.size()>0 && stretch === 'fullwidth') {
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
                    } else if(!controlInner &&  stretch === 'fullwidth') {
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
                $rowControls = $('.basement-gallery-carousel-inline-controls'),
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
            var $arrowLine = $('.basement-gallery-carousel-arrow-outside.basement-gallery-carousel-arrow-side');

            if($arrowLine.size()>0) {
                $.each($arrowLine, function(){
                   var $this = $(this),
                        $fullWidthElement = $this.closest('.basement-gallery-carousel-help-row').next('.full-width-basement-help-row'),
                        rowMarginLeft = parseInt( $this.css('margin-left'), 10),
                        rowMarginRight = parseInt( $this.css( 'margin-right' ), 10),
                        offsetLeft = 0 - $fullWidthElement.offset().left - rowMarginLeft,
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

            var $arrowLine = $('.basement-gallery-carousel-arrow-inside.basement-gallery-carousel-arrow-side');

            if($arrowLine.size()>0) {
                $.each($arrowLine, function(){
                    var $this = $(this),
                        $fullWidthElement = $this.closest('.basement-gallery-carousel-help-row').next('.full-width-basement-help-row'),
                        rowMarginLeft = parseInt( $this.css('margin-left'), 10),
                        rowMarginRight = parseInt( $this.css( 'margin-right' ), 10),
                        offsetLeft = 0 - $fullWidthElement.offset().left - rowMarginLeft,
                        width = $win.width(),
                        caroRowWidth = $this.closest('.basement-gallery-carousel-help-row').outerWidth(true),
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
        initBasementGalleryCarousel : function( $carousel, getParams ) {
            var scope = this;

                try {
                    if($carousel.size() > 0) {
                        $.each($carousel, function () {
                            var $this = $(this),
                                dataParams = $this.data('basement-params'),
                                dataTotal = $this.data('basement-total'),
                                $total = $(dataTotal),
                                pagContainer = dataParams.hasOwnProperty('pagination') && dataParams.pagination.hasOwnProperty('container') ? dataParams.pagination.container : null,
                                setTotal = function(number) {
                                    $total.find('.basement-gallery-carousel-paginate-all').text(number);
                                },
                                setCurrent = function(current) {
                                    $total.find('.basement-gallery-carousel-paginate-current').text(current);
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
                    console.log(e + "\n" + 'Basement Gallery detect error. Please Turn off plugin "Basement Gallery" and please write a message to our support team. Thx!')
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
                    new Basement_Gallery(this, options));
            }
        });
    };

})(jQuery, window, document);

jQuery(document).ready(function($){
    $(document.body).BasementGallery();
});