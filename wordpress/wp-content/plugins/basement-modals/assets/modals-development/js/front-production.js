(function($, window, document){
    'use strict';
    var pluginName = "BasementModals",
        defaults = {},
        $win = $(window),
        $doc = $(document);


    function Basement_Modals(element, options) {
        var that = this;
        that.element = $(element);
        that.options = $.extend({}, defaults, options);


        $win.on('load', function(){
            that.callModal();
        }).on('resize', function(){

        }).on('scroll', function(){

        });

    }

    Basement_Modals.prototype = {
        callModal : function () {
            var instance = this;
            $doc.on('click','[href^="#basement-modal-"]', function (e) {
                e.preventDefault();
                var $this = $(this),
                    postId = parseInt($this.attr('href').replace( /^\D+/g, '')),
                    $modal = $doc.find('#basement-modal-window'),
                    $close = $modal.find('.basement-modal-close, .basement-modal-close i'),
                    $maincontent = $modal.find('.maincontent'),
                    $preloader = $('#basement-modal-preloader'),
                    $style = $('#basement-modal-style');

                if($('.woo-modals').size() > 0) {
                    $('.woo-modals').children('div').removeClass('show');
                }

                $modal.attr('class','init');
                
                if(postId == $modal.attr('data-init')) {
                    //setTimeout(function () {
                    $modal.addClass('show');
                    //}, 100);
                } else {
                    var data = {
                        action: 'basement-modal-call',
                        post_id: postId
                    };
                    $modal.removeClass('show');

                    if($preloader.size() > 0) {
                        $preloader.addClass('show');
                    }
                    $.post(basement_modals_ajax.url, data, function (response) {

                        if (response) {
                            response = JSON.parse(response);

                            var bg_color = response.bg_color ? response.bg_color : '',
                                close_color = response.close_color ? response.close_color : '',
                                close_hover_color = response.close_hover_color ? response.close_hover_color : '',
                                close_icon_color = response.close_icon_color ? response.close_icon_color : '',
                                close_hover_icon_color = response.close_hover_icon_color ? response.close_hover_icon_color : '',
                                content = response.content ? response.content : '',
                                css = response.css ? response.css : '';


                            $modal.removeAttr('style');
                            $close.removeAttr('style');
                            $close.find('i').removeAttr('style');
                            $close.unbind("mouseenter");
                            $close.unbind("mouseleave");

                            if (bg_color) {
                                $modal.css('background-color', bg_color);
                            }

                            if (close_color) {
                                $close.css('background-color', close_color);
                            }

                           if (close_hover_color || close_color) {
                                $close.mouseenter(function () {
                                    if (close_hover_color) {
                                        $close.css('background-color', close_hover_color);
                                    } else {
                                        //$close.css('background-color', '#d64f32');
                                    }
                                }).mouseleave(function () {
                                    if (close_color) {
                                        $close.css('background-color', close_color);
                                    } else {
                                        //$close.css('background-color', '#121212');
                                    }
                                });
                            }


                            if (close_icon_color) {
                                $close.find('i').css('color', close_icon_color);
                            }
                            if (close_hover_icon_color || close_icon_color) {
                                $close.mouseenter(function () {
                                    if (close_hover_icon_color) {
                                        $close.find('i').css('color', close_hover_icon_color);
                                        //$close.find('i').css('color', close_hover_color);
                                    } else {
                                        //$close.css('background-color', '#d64f32');
                                    }
                                }).mouseleave(function () {
                                    if (close_icon_color) {
                                        $close.find('i').css('color', close_icon_color);
                                       // $close.find('i').css('color', close_color);
                                    } else {
                                        //$close.css('background-color', '#121212');
                                    }
                                });
                            }


                            if($style.size() > 0) {
                                $style.text(css);
                            }

                            if(content) {
                                $maincontent.html(content);
                            }

                            $(document).trigger('basement_shortcodes_bind');
                            $(document).trigger('theme_custom_bind');
	
                            if(getscript) {
	                            $.getScript ("/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=4.8", function ( response, status ) {
		                            if ( status !== 'success' ) {
			                            console.log (status + ' loaded contact form basement ajax js)');
		                            }
	                            });
                            }
                            
	                        
	                        
                            setTimeout(function () {
                                $modal.addClass('show');
                                $modal.attr('data-init',postId);
                            }, 310);

                            $preloader.removeClass('show');

                        } else {
                            $preloader.removeClass('show');
                        }
                    });
                }

            });
            $doc.on('click','.basement-modal-close', function(e){
                e.preventDefault();

                $(this).closest('#basement-modal-window').removeClass('show');
            });
        }
    };


    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Basement_Modals(this, options));
            }
        });
    };

})(jQuery, window, document);


jQuery(document).ready(function($){
    $(document.body).BasementModals();
});

