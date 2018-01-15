(function($, window, document){
    'use strict';

    var pluginName = "BasementMain",
        defaults = {},
        $win = $(window),
        $doc = $(document),
        init,
        $html = $('html'),
        $body = $('body'),
        $htmlbody = $('html, body');

    function Basement(element, options) {
        var that = this;
        that.element = $(element);
        that.options = $.extend({}, defaults, options);

        $win.on('load', function(){

            // Instagram Widget
            that.instagramEmbed();

            // Twitter Widget
            that.twitterEmbed();

            // Flickr Widget
            that.flickrEmbed();

            // Video popup after click link
            that.linkVideo();
        }).on('resize', function(){

        }).on('scroll', function(){

        });

    }

    Basement.prototype = {
        
        twitterEmbed : function () {
            var $widget = $('.twitter-widget');

            if($widget.size() > 0) {
                $widget.each(function () {
                    var $this = $(this),
                        id = $this.data('id'),
                        cid = $this.data('cid'),
                        cidText = cid.toString(),
                        tweet = $this.data('tweet');

                    if(cid) {
                        /*var tweetConfig = {
                            "id": cid,
                            "domId": id,
                            "maxTweets": tweet ? tweet : 3,
                            "showUser": false,
                            "showImages": false,
                            "showRetweet": false,
                            "showInteraction": false,
                            "showTime": false,
                            "enableLinks": true
                        };
                        twitterFetcher.fetch(tweetConfig);*/
                        
	                    var config2 = {
		                    "id": cidText,
		                    "domId": id,
		                    "maxTweets": tweet ? tweet : 3,
		                    "showUser": false,
		                    "showImages": false,
		                    "showRetweet": false,
		                    "showInteraction": false,
		                    "showTime": false,
		                    "enableLinks": true
	                    };
	                    twitterFetcher.fetch(config2);
                    }
                });
            }

        },
        flickrEmbed : function () {
            var $widget = $('.flickr-widget');

            if($widget.size() > 0) {
                $widget.each(function () {
                    var $this = $(this),
                        id = $this.data('id'),
                        cid = $this.data('cid'),
                        flick = $this.data('flick');


                    var imgBTemplate = "{{image_b}}",
                        imgSTemplate = "{{image_s}}";

                    $('#'+id).jflickrfeed({
                        limit: flick ? flick : 6,
                        qstrings: {
                            id: cid
                        },
                        itemTemplate: '<a href='+ imgBTemplate +' target="_blank"><img alt="{{title}}" class="img-responsive" src='+ imgSTemplate +' /></a>'
                    });

                });
            }
        },
        instagramEmbed : function () {
            var $widget = $('.instagram-widget');

            if($widget.size() > 0) {
                $widget.each(function () {
                    var $this = $(this),
                        id = $this.data('id'),
                        cid = $this.data('cid'),
                        token = $this.data('token'),
                        insta = $this.data('insta');


                    var linkTemplate = "{{link}}",
                        imgTemplate = "{{image}}",
                        userFeed = new Instafeed({
                            get: 'user',
                            userId: cid,
                            limit: insta ? insta : 6,
                            accessToken: token,
                            target : id,
                            template: '<a href='+ linkTemplate +' target="_blank"><img alt="" class="img-responsive" src='+ imgTemplate +' /></a>'
                        });
                    userFeed.run();
                });
            }
        },
        linkVideo : function () {
            $(document).find('[href^="#basement-popup-"]').each(function() {
                var $this = $(this),
                    url = $.trim($this.attr('href').replace('#basement-popup-','')),
                    pattern = /^(http|https)?:\/\/[a-zA-Z0-9-\.]+\.[a-z]{2,4}/;

                if(pattern.test(url)) {
                    $this.addClass('mfp-iframe');
                    $this.attr('href',url).magnificPopup({
                        type: 'image',
                        tLoading: '',
                        gallery: {
                            enabled: true,
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
                }

            });
        }
    };


    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Basement(this, options));
            }
        });
    };

})(jQuery, window, document);

jQuery(document).ready(function($){
    $(document.body).BasementMain();
});