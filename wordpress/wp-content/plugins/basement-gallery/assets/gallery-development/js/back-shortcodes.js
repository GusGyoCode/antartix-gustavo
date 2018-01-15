(function($, window, document){
    'use strict';

    var $win = $(window),
        $doc = $(document),
        $body = $('body'),
        shCarouselSection = '[data-section="basement_gallery"]',
        shAddSlide = '.galleries_tile_add',
        shSortableBlock = '.galleries_tile_sortable',
        shRemoveSortSlide = '.galleries_tile_remove',
        shSortSlide = '.ui-state-default';


    function Basement_Shortcode_Gallery() {
        var that = this;

        that.fillSmartTiles();
        that.smartTiles();
    }


    Basement_Shortcode_Gallery.prototype = {
        fillSmartTiles : function () {
            var scope = this,
                $slidesInput = $doc.find('.galleries_tile_insert'),
                valueSlides = $slidesInput.val(),
                $sortable = $doc.find(shSortableBlock);

            if(valueSlides) {
                var slides = valueSlides.split(',');

                for(var i = 0; i<slides.length; i++) {
                    if($sortable.size() > 0) {
                        var value = slides[i];
                        if(slides[i]) {
                            var $option = $doc.find(shAddSlide + ' option[value="' + value + '"]'),
                                text = $option.text(),
                                htmlImg = $option.data('img') ? '<div class="galleries_thumb" style="background-image: url('+$option.data('img')+')" data-src="'+$option.data('img')+'"></div>' : '',
                                htmlTitle = '<strong class="galleries_tile_title">'+text+'</strong>',
                                htmlEdit = '<a href="'+$option.data('edit')+'" target="_blank" class="button-secondary button galleries_tile_edit" title="">'+$option.data('edit-title')+'</a>',
                                htmlClose = '<a href="#" class="'+shRemoveSortSlide.slice(1)+'" title=""><i class="fa fa-remove"></i></a>',
                                htmlMove = '<i class="fa fa-arrows galleries_tile_handle"></i>';

                            $option.remove();

                            if (scope.isEmpty($sortable)) {
                                $sortable.addClass('activate');
                            }
                            $sortable.append('<div class="'+shSortSlide.slice(1)+' cf" data-id="'+value+'">' + htmlImg + htmlTitle + htmlMove + htmlClose + htmlEdit + '</div>');

                            scope.updateSlides($sortable);
                        }
                    }
                }
            }

        },
        updateSlides : function ($sortable) {

            var scope = this,
                $slides = $doc.find('.galleries_tile_insert'),
                slidesList = [];
            if(!scope.isEmpty($sortable) && $slides.size() > 0) {
                $sortable.find(shSortSlide).each(function(){
                    slidesList.push($(this).data('id'));
                });
                $slides.val(slidesList.join()).trigger('change');
            } else {
                $slides.val('').trigger('change');
            }
        },
        smartTiles : function() {
            var scope = this,
                $addSlide = $(shAddSlide),
                $sortBlock = $(shSortableBlock);



            $doc.on('change', shAddSlide, function(){
                var $this = $(this),
                    value = $this.val(),
                    $option = $this.find('option[value="'+value+'"]'),
                    text = $option.text(),
                    $sortable = $doc.find(shSortableBlock);

                if($sortable.size() > 0 && value) {
                    var htmlImg = $option.data('img') ? '<div class="galleries_thumb" style="background-image: url('+$option.data('img')+')" data-src="'+$option.data('img')+'"></div>' : '',
                        htmlTitle = '<strong class="galleries_tile_title">'+text+'</strong>',
                        htmlEdit = '<a href="'+$option.data('edit')+'" target="_blank" class="button-secondary button galleries_tile_edit" title="">'+$option.data('edit-title')+'</a>',
                        htmlClose = '<a href="#" class="'+shRemoveSortSlide.slice(1)+'" title=""><i class="fa fa-remove"></i></a>',
                        htmlMove = '<i class="fa fa-arrows galleries_tile_handle"></i>';
                    if(scope.isEmpty($sortable)) {
                        $sortable.addClass('activate');
                    }
                    $sortable.append('<div class="'+shSortSlide.slice(1)+' cf" data-id="'+value+'">' + htmlImg + htmlTitle + htmlMove + htmlClose + htmlEdit + '</div>');
                    $option.remove();
                    scope.updateSlides($sortable);
                }
            });

            $doc.on('click', shRemoveSortSlide, function(){
                var $this = $(this),
                    $slide = $this.closest(shSortSlide),
                    selectTitle = $slide.find('.galleries_tile_title').text(),
                    selectValue = $slide.data('id'),
                    selectImg = $slide.find('.galleries_thumb').data('src'),
                    selectEdit = $slide.find('.galleries_tile_edit').attr('href'),
                    selectEditTitle  = $slide.find('.galleries_tile_edit').text(),
                    $select = $doc.find(shAddSlide),
                    $sortable = $doc.find(shSortableBlock);

                if(!$select.find('option[value="'+selectValue+'"]').size() > 0) {
                    $select.append(
                        $('<option></option>')
                            .attr('value', selectValue)
                            .attr('data-img', selectImg)
                            .attr('data-edit', selectEdit)
                            .attr('data-edit-title', selectEditTitle)
                            .text(selectTitle)
                    );
                }

                $slide.remove();

                if(scope.isEmpty($sortable)) {
                    $sortable.removeClass('activate');
                }

                scope.updateSlides($sortable);
            });


            $sortBlock.each(function(){
                $(this).sortable({
                    axis: 'y',
                    placeholder: 'ui-state-highlight',
                    handle: '.galleries_tile_handle',
                    update: function(event, ui) {
                        scope.updateSlides($(this));
                    },
                    stop: function(event, ui) {
                        scope.updateSlides($(this));
                    }
                });
                $(this).disableSelection();
            });

        },
        isEmpty : function(el){
            return !$.trim(el.html())
        }
    };


    new Basement_Shortcode_Gallery;
})(jQuery, window, document);