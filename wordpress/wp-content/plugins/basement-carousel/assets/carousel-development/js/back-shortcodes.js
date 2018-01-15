(function($, window, document){
    'use strict';

    var $doc = $(document),
        shAddSlide = '.carousel_slide_add',
        shSortableBlock = '.carousel_slide_sortable',
        shRemoveSortSlide = '.carousel_slide_remove',
        shSortSlide = '.ui-state-default';


    function Basement_Shortcode_Carousel() {
        var that = this;
        that.fillSmartSlides();
        that.smartSlides();
    }


    Basement_Shortcode_Carousel.prototype = {
        fillSmartSlides : function () {
            var scope = this,
                $slidesInput = $doc.find('.carousel_slide_insert'),
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
                                htmlTitle = '<strong class="carousel_slide_title">' + text + '</strong>',
                                htmlEdit = '<a href="' + $option.data('edit') + '" target="_blank" class="button-secondary button carousel_slide_edit" title="">' + $option.data('edit-title') + '</a>',
                                htmlClose = '<a href="#" class="' + shRemoveSortSlide.slice(1) + '" title=""><i class="fa fa-remove"></i></a>',
                                htmlMove = '<i class="fa fa-arrows carousel_slide_handle"></i>';

                            $option.remove();

                            if (scope.isEmpty($sortable)) {
                                $sortable.addClass('activate');
                            }
                            $sortable.append('<div class="' + shSortSlide.slice(1) + ' cf" data-id="' + value + '">' + htmlTitle + htmlMove + htmlClose + htmlEdit + '</div>');

                            scope.updateSlides($sortable);
                        }
                    }
                }
            }

        },
        updateSlides : function ($sortable) {
            var scope = this,
                $slides = $doc.find('.carousel_slide_insert'),
                slidesList = [];

            if(!scope.isEmpty($sortable) && $slides.size() > 0) {
                $sortable.find(shSortSlide).each(function(){
                    slidesList.push($(this).data('id'));
                });
                $slides.val(slidesList.join()).attr('value',slidesList.join()).trigger('change');
            } else {
                $slides.val('').attr('value','').trigger('change');
            }
        },
        smartSlides : function() {
            var scope = this,
                $sortBlock = $(shSortableBlock);


            $doc.on('change', shAddSlide, function(){
                var $this = $(this),
                    value = $this.val(),
                    $option = $this.find('option[value="'+value+'"]'),
                    text = $option.text(),
                    edit = $option.data('edit'),
                    title = $option.data('edit-title'),
                    $sortable = $doc.find(shSortableBlock);

                if($sortable.size() > 0 && value) {
                    var htmlTitle = '<strong class="carousel_slide_title">'+text+'</strong>',
                        htmlEdit = '<a href="'+edit+'" target="_blank" class="button-secondary button carousel_slide_edit" title="">'+title+'</a>',
                        htmlClose = '<a href="#" class="'+shRemoveSortSlide.slice(1)+'" title=""><i class="fa fa-remove"></i></a>',
                        htmlMove = '<i class="fa fa-arrows carousel_slide_handle"></i>';
                    if(scope.isEmpty($sortable)) {
                        $sortable.addClass('activate');
                    }
                    $sortable.append('<div class="'+shSortSlide.slice(1)+' cf" data-id="'+value+'">' + htmlTitle + htmlMove + htmlClose + htmlEdit + '</div>');
                    $option.remove();
                    scope.updateSlides($sortable);
                }
            });

            $doc.on('click', shRemoveSortSlide, function(e){
                e.preventDefault();
                var $this = $(this),
                    $slide = $this.closest(shSortSlide),
                    selectTitle = $slide.find('.carousel_slide_title').text(),
                    selectValue = $slide.data('id'),
                    selectEdit = $slide.find('.carousel_slide_edit').attr('href'),
                    selectEditTitle  = $slide.find('.carousel_slide_edit').text(),
                    $select = $doc.find(shAddSlide),
                    $sortable = $doc.find(shSortableBlock);

                if(!$select.find('option[value="'+selectValue+'"]').size() > 0) {
                    $select.append(
                        $('<option></option>')
                            .attr('value', selectValue)
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
                    handle: '.carousel_slide_handle',
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


    new Basement_Shortcode_Carousel;
})(jQuery, window, document);