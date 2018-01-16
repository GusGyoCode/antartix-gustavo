(function($, window, document){
    'use strict';

    var $win = $(window),
        $doc = $(document),
        $body = $('body'),
        shCarouselSection = '[data-section="basement_gallery"]',
        shAddSlide = '.portfolio_project_add',
        shSortableBlock = '.portfolio_project_sortable',
        shRemoveSortSlide = '.portfolio_project_remove',
        shSortSlide = '.ui-state-default';


    function Basement_Shortcode_Portfolio() {
        var that = this;


        that.fillSmartTiles();
        //Add/Drag/Sort/Remove projects
        that.smartprojects();
    }


    Basement_Shortcode_Portfolio.prototype = {
        fillSmartTiles : function () {
            var scope = this,
                $slidesInput = $doc.find('.portfolio_project_insert'),
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
                                htmlImg = $option.data('img') ? '<div class="portfolio_thumb" style="background-image: url('+$option.data('img')+')" data-src="'+$option.data('img')+'"></div>' : '',
                                htmlTitle = '<strong class="portfolio_project_title">'+text+'</strong>',
                                htmlEdit = '<a href="'+$option.data('edit')+'" target="_blank" class="button-secondary button portfolio_project_edit" title="">'+$option.data('edit-title')+'</a>',
                                htmlClose = '<a href="#" class="'+shRemoveSortSlide.slice(1)+'" title=""><i class="fa fa-remove"></i></a>',
                                htmlMove = '<i class="fa fa-arrows portfolio_project_handle"></i>';

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
                $slides = $doc.find('.portfolio_project_insert'),
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
        smartprojects : function() {
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

                    var htmlIndex = '<span>'+$this.data('index')+'</span>',
                        htmlImg = $option.data('img') ? '<div class="portfolio_thumb" style="background-image: url('+$option.data('img')+')" data-src="'+$option.data('img')+'"></div>' : '',
                        htmlTitle = '<strong class="portfolio_project_title">'+text+'</strong>',
                        htmlEdit = '<a href="'+$option.data('edit')+'" target="_blank" class="button-secondary button portfolio_project_edit" title="">'+$option.data('edit-title')+'</a>',
                        htmlClose = '<a href="#" class="'+shRemoveSortSlide.slice(1)+'" title=""><i class="fa fa-remove"></i></a>',
                        htmlMove = '<i class="fa fa-arrows portfolio_project_handle"></i>';
                    if(scope.isEmpty($sortable)) {
                        $sortable.addClass('activate');
                    }
                    $sortable.append('<div class="'+shSortSlide.slice(1)+' cf" data-id="'+value+'">' + htmlImg + htmlTitle + htmlMove + htmlClose + htmlEdit + '</div>');

                    $option.remove();

                    scope.updateSlides($sortable);
                }
            });

            $doc.on('click', shRemoveSortSlide, function(e){
                e.preventDefault();
                var $this = $(this),
                    $slide = $this.closest(shSortSlide),
                    selectTitle = $slide.find('.portfolio_project_title').text(),
                    selectValue = $slide.data('id'),
                    selectImg = $slide.find('.portfolio_thumb').data('src'),
                    selectEdit = $slide.find('.portfolio_project_edit').attr('href'),
                    selectEditTitle  = $slide.find('.portfolio_project_edit').text(),
                    $select = $doc.find(shAddSlide),
                    $sortable = $doc.find(shSortableBlock);

                $select.append(
                    $('<option></option>')
                        .attr('value',selectValue)
                        .attr('data-img',selectImg)
                        .attr('data-edit',selectEdit)
                        .attr('data-edit-title',selectEditTitle)
                        .text(selectTitle)
                );

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
                    handle: '.portfolio_project_handle',
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


    new Basement_Shortcode_Portfolio;
})(jQuery, window, document);