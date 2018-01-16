(function($, window, document){
    'use strict';

    var $win = $(window),
        $doc = $(document),
        $body = $('body'),
        shAddSlide = '.posts_post_add',
        shSortableBlock = '.posts_post_sortable',
        shRemoveSortSlide = '.posts_post_remove',
        shSortSlide = '.ui-state-default';


    function Basement_Sortable() {
        var that = this;


        $win.on('load', function(){
            that.smartTiles();
        }).on('resize', function(){

        }).on('scroll', function(){

        });
    }


    Basement_Sortable.prototype = {
        smartTiles : function() {
            var scope = this,
                $addSlide = $(shAddSlide),
                $sortBlock = $(shSortableBlock);

            scope.updateSlides = function ($sortable) {

                var $parent = $sortable.closest('.basement_shortcodes_panel_block_inputs'),
                    $slides = $parent.find('.posts_post_insert'),
                    slidesList = [];
                if(!scope.isEmpty($sortable) && $slides.size() > 0) {
                    $sortable.find(shSortSlide).each(function(){
                        slidesList.push($(this).data('id'));
                    });
                    $slides.val(slidesList.join()).trigger('change');
                } else {
                    $slides.val('').trigger('change');
                }
            };

            $doc.on('change', shAddSlide, function(){
                var $this = $(this),
                    value = $this.val(),
                    $option = $this.find('option[value="'+value+'"]'),
                    $parent = $this.closest('.basement_shortcodes_panel_block_inputs'),
                    text = $option.text(),
                    $sortable = $parent.find(shSortableBlock);

                if($sortable.size() > 0 && value) {
                    var htmlIndex = '<span>'+$this.data('index')+'</span>',
                        htmlImg = $option.data('img') ? '<div class="post_thumb" style="background-image: url('+$option.data('img')+')" data-src="'+$option.data('img')+'"></div>' : '',
                        htmlTitle = '<strong class="post_title">'+text+'</strong>',
                        htmlEdit = '<a href="'+$option.data('edit')+'" target="_blank" class="button-secondary button post_edit" title="">'+$option.data('edit-title')+'</a>',
                        htmlClose = '<a href="#" class="'+shRemoveSortSlide.slice(1)+'" title=""><i class="fa fa-remove"></i></a>',
                        htmlMove = '<i class="fa fa-arrows post_handle"></i>';
                    if(scope.isEmpty($sortable)) {
                        $sortable.addClass('activate');
                    }

                    if($sortable.children('div').size() <= 2 ) {
                        $sortable.append('<div class="' + shSortSlide.slice(1) + ' cf" data-id="' + value + '">' + htmlImg + htmlTitle + htmlMove + htmlClose + htmlEdit + '</div>');
                        $option.remove();
                        scope.updateSlides($sortable);
                    }
                }
            });

            $doc.on('click', shRemoveSortSlide, function(){
                var $this = $(this),
                    $slide = $this.closest(shSortSlide),
                    selectTitle = $slide.find('.post_title').text(),
                    selectValue = $slide.data('id'),
                    selectImg = $slide.find('.post_thumb').data('src'),
                    selectEdit = $slide.find('.post_edit').attr('href'),
                    selectEditTitle  = $slide.find('.post_edit').text(),
                    $parent = $this.closest('.basement_shortcodes_panel_block_inputs'),
                    $select = $parent.find(shAddSlide),
                    $sortable = $parent.find(shSortableBlock);

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
                    handle: '.post_handle',
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


    new Basement_Sortable;
})(jQuery, window, document);