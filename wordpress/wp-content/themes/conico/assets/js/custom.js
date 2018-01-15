/**
 * Theme functions file.
 *
 * Contains handlers for navigation, footer and much, much more.
 */

(function ($, window, document) {
    'use strict';
    var pluginName = 'Aisconverse',
        $doc = $(document),
        defaults = {},
        $body = $('body'),
        $html = $('html'),
        $win = $(window);
	// The plugin constructor
    function Plugin (element, options) {
        var that = this;
        that.element = $(element);
        that.options = $.extend({}, defaults, options);
        that.init();
        that.activate();
        that.ieDetect();
        that.mobileDetect();
        that.osDetect();
        that.browserDetect();
        that.iePlaceholder();
        that.stickyStyle();
        that.stretch('[data-stretch]');
        that.scrollTop();
        that.stickFooter('load');
        that.typography();
        that.forms();
        that.vc();
        that.headerHelper('load');
        that.menu();
        that.blog();
        that.megaMenuCreator();
        that.simpleMenu();
        that.floatPageTitle('load');
        that.categoryStretch();
        that.widgetSettings();
        $doc.ajaxComplete(function () {
            var $cf7 = $('form.wpcf7-form');
            if ($cf7.size() > 0) {
                $cf7.find('.wpcf7-form-control').each(function () {
                    if ($(this).hasClass('wpcf7-not-valid')) {
                        $(this).closest('label').addClass('wpcf7-not-valid-label');
                    } else {
                        $(this).closest('label').removeClass('wpcf7-not-valid-label');
                    }
                });
            }
        });
        $win.load(function () {
            that.preloader.delay(200).fadeOut(200);
        }).scroll(function () {
            that.scrollTop();
            that.stickyStyle();
            that.floatPageTitle('scroll');
        }).resize(function () {
            that.stickFooter('resize');
            that.stretch('[data-stretch]');
            that.headerHelper('resize');
            that.floatPageTitle('resize');
            that.categoryStretch();
        }).afterResize(function () {
            that.stickFooter();
            that.categoryStretch();
        }, true, 320);
        $(document).on('theme_custom_bind', function () {
            that.forms();
            that.vc();
        });
    }

    Plugin.prototype = {
        init: function () {
            this.body = $(document.body);
            this.preloader = $('.preloader');
            this.scrTop = $('.scrolltop');
        },
        widgetSettings: function () {
            var widgets = ['.widget_calendar', '.widget_archive', '.widget_categories', '.widget_contact_form'],
                widgetClass, $widget, $li, $chosenSingle, clearNode, $formElement, i;

            $('.widget').find('select:not(.wpcf7-select) option').each(function () {
                var $html = $(this).html();
                $html.replace(/\(([^*]+)\)/g, '<span>$1</span>');
            });

            for (i = 0; i <= widgets.length; i++) {
                widgetClass = widgets[i];
                $widget = $(widgetClass);

                if ($widget.size() > 0) {
                    $widget.each(function () {
                        var $this = $(this);

                        switch (widgetClass) {
                            case '.widget_archive' :
                            case '.widget_categories' :
                                $li = $this.find('.widget-body-inner li');
                                $chosenSingle = $this.find('.chosen-single');
                                $li.each(function () {
                                    var $html = $(this).html();

                                    clearNode = $html.replace(/\((.*?)\)/g, '<span class="counter-list">$1</span>');
                                    $(this).html(clearNode);
                                });

                                if ($chosenSingle.size() > 0) {
                                    $chosenSingle.html($chosenSingle.html().replace(/&nbsp;/g, ''));
                                }

                                break;
                            case '.widget_contact_form' :
                                $formElement = $this.find('.wpcf7-form-control-wrap');

                                if ($formElement.size() > 0) {
                                    $formElement.addClass('form-group');
                                }
                                break;
                        }
                    });
                }
            }
        },
        blog: function () {
            var $figure = $doc.find('figure.gallery-item'),
                $commentForm = $('#commentform');

            $('.comment-text-body table').addClass('table');

            if ($body.hasClass('custom-background')) {
                $html.css('background-color', 'transparent');
            }

            $('blockquote p').each(function () {
                var $this = $(this);
                if ($this.html().replace(/\s|&nbsp;/g, '').length === 0) {
                    $this.remove();
                }
            });

            if ($commentForm.size() > 0) {
                $commentForm.validate({
                    errorClass: 'wpcf7-not-valid',
                    errorPlacement: function () {
                        return true;
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass(errorClass).removeClass(validClass);
                    },
                    focusInvalid: false,
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass(errorClass).addClass(validClass);
                    }
                });
            }

            if ($figure.size() > 0) {
                $figure.each(function () {
                    var $a = $(this).find('a'),
                        href = $a.attr('href').slice(-3);

                    if (href === 'jpg' || href === 'png' || href === 'jpeg' || href === 'bmp' || href === 'gif') {
                        $a.addClass('wp-gallery');
                    }
                });
            }
        },
        categoryStretch: function () {
            var $grid = $('.blog-posts-grid');

            if ($grid.size() > 0 && !$grid.hasClass('classic')) {
                $grid.children('article.post').each(function () {
                    var $this = $(this),
					  $rotateInner = $this.find('.entry-categories-rotate'),
					  $rotate = $this.find('.entry-categories.is-rotate'),
					  postHeight = $this.height() + 15,
					  rotateWidth;

                    rotateWidth = $rotate.height();
                    if (rotateWidth > 28) {
                        $rotate.css('left', '-' + (rotateWidth + 9) + 'px');
                    }

                    $rotateInner.css('max-width', postHeight);
                });
            }
        },
        floatPageTitle: function () {
            var $pageTitle = $('.page-title-float'),
			  $content = $('.content'),
			  offsetTop, scrollTop, $header, $adminBar, heightHeight, adminBarHeight;
            if ($pageTitle.size() > 0 && $content.size() > 0) {
                offsetTop = $content.offset().top;
                scrollTop = $win.scrollTop();
                $header = $('header.header');
                $adminBar = $('#wpadminbar');
                heightHeight = $header.size() > 0 ? $header.outerHeight(true) : 0;
                adminBarHeight = $adminBar.size() > 0 ? $adminBar.outerHeight(true) : 0;

                if ($body.hasClass('admin-bar')) {
                    offsetTop -= adminBarHeight;
                }
                if ($body.hasClass('basement-enable-sticky')) {
                    offsetTop -= heightHeight;
                }
                if (scrollTop >= offsetTop) {
                    $pageTitle.addClass('vis');
                } else {
                    $pageTitle.removeClass('vis');
                }
            }
        },
        vc: function () {
            var $accordion = $doc.find('.vc_general.vc_tta.vc_tta-accordion'),
			  xH;

            if ($accordion.size() > 0) {
                $accordion.each(function () {
                    var $this = $(this),
					  $h4 = $this.find('.vc_tta-panel-title');
                    if ($h4.hasClass('vc_tta-controls-icon-position-left')) {
                        $this.addClass('vc_accordion-icon-left');
                    } else {
                        $this.addClass('vc_accordion-icon-right');
                    }
                });
            }
            $(document).on('mouseenter mouseleave', '.vc_single_image_img_scroll figure', function (e) {
                var height = $(this).outerHeight(true);
                if (e.type === 'mouseenter') {
                    xH = $(this).find('img').css('height');
                    xH = parseInt(xH);
                    xH = xH - height;
                    xH = '-' + xH + 'px';
                    $(this).find('img').css('marginTop', xH);
                } else {
                    $(this).find('img').css('marginTop', '0px');
                }
            });
        },
        stretch: function (selector) {
            if ($(selector).size() > 0) {
                var $rowElement = $(selector);
                $rowElement.each(function () {
                    var $thisRow = $(this),
					  clearDataName = selector.substring(6, selector.length - 1),
					  valueElement = $thisRow.data(clearDataName),
					  $fullWidthElement = $thisRow.next('.full-width-basement'),
					  rowMarginLeft = parseInt($thisRow.css('margin-left'), 10),
					  rowMarginRight = parseInt($thisRow.css('margin-right'), 10),
					  offsetLeft = -1 - $fullWidthElement.offset().left - rowMarginLeft,
					  width = $win.width(),
					  $lastLayer, paddingRight, padding;
                    $thisRow.css({
                        'width': $(window).width(),
                        'position': 'relative',
                        'left': offsetLeft,
                        'box-sizing': 'border-box'
                    });
                    if ($thisRow.hasClass('deep-layers-basement')) {
                        $lastLayer = $thisRow.find('.deep-layer-basement:not(:has(.deep-layer-basement))');
                        $lastLayer.addClass('last-layer-basement');
                        funStrow($lastLayer);
                        funStrowContPad($lastLayer);
                    } else {
                        funStrow($thisRow);
                        funStrowContPad($thisRow);
                    }
                    function funStrow ($layer) {
                        if (valueElement === 'strow') {
                            padding = (-1 * offsetLeft);
                            if (padding < 0) {
                                padding = 0;
                            }
                            paddingRight = width - padding - $fullWidthElement.width() + rowMarginLeft + rowMarginRight;
                            if (paddingRight < 0) {
                                paddingRight = 0;
                            }
                            $layer.css({
                                'padding-left': padding,
                                'padding-right': paddingRight
                            });
                        }
                    }

                    function funStrowContPad ($layer) {
                        if (valueElement === 'strow_cont_pad') {
                            $layer.children('[class*=col-]').css({
                                'padding': '0px'
                            });
                        }
                    }
                });
            }
        },
        activate: function () {
            var instance = this;

			// scrollTop function
            instance.scrTop.on('click', function (e) {
                e.preventDefault();
                $('html, body').stop(true, true).animate({
                    scrollTop: 0
                }, 500);
            });

            $('.modal').on('shown.bs.modal', function () {
                $(this).find('[autofocus]').focus();
            });
        },
        forms: function () {
            $('.wpcf7-form-control-wrap + br').remove();
            $('.wpcf7-submit').parent().addClass('wpcf7-wrap-submit');
            $('select:not(.basement-portfolio-cat-select)').uniform();
            $(document).ajaxComplete(function (t) {
                var $select = $('.wpcf7-select');
                if ($select.size() > 0) {
                    $select.each(function () {
                        if ($(this).hasClass('wpcf7-not-valid')) {
                            $(this).parent().addClass('wpcf7-not-valid');
                        } else {
                            $(this).parent().removeClass('wpcf7-not-valid');
                        }
                    });
                }
            });
        },
        getElementCss: function (selector, param, parse) {
            return parse ? parseInt($(selector).css(param)) : $(selector).css(param);
        },
        menu: function () {
            var $menu = $('.navbar-lang, .wrapper-navbar-nav');
            $menu.superfish({
                delay: 0,
                animation: {
                    opacity: 'show',
                    marginTop: 0,
                    marginBottom: 0
                },
                animationOut: {
                    opacity: 'hide',
                    marginTop: '14px',
                    marginBottom: '14px'
                },
                cssArrows: false,
                speed: 'fast',
                speedOut: 'fast',
                onBeforeShow: function () {
                    var $this = $(this),
					  $navMenu = $this.closest('div.navbar-menu'),
					  winWidth = $win.width(),
					  $rootLi = $this.parents('li').last(),
					  thisWidth = $this.outerWidth(true),
					  menuPosition;
                    if ($navMenu.size() > 0 && !$rootLi.hasClass('mega-menu') && !$this.parents().eq(1).hasClass('navbar-nav-menu')) {
                        menuPosition = $this.parent().offset().left + (thisWidth * 2);
                        if (menuPosition > winWidth) {
                            $this.addClass('sf-onleft');
                        } else {
                            $this.removeClass('sf-onleft');
                        }
                    }
                }
            });
        },
        megaMenuCreator: function () {
            var $megaMenu = $('.is-mega-menu');
            if ($megaMenu.size() > 0) {
                $megaMenu.each(function () {
                    var $this = $(this),
					  $item = $this.find('.mega-menu-col');
                    if ($item.size() > 0) {
                        $item.each(function () {
                            var $thisItem = $(this),
							  id = $thisItem.data('id'),
							  $insertItem = $this.find('.basement-col-' + id + ' ul'),
							  $insertedItem;
                            if ($thisItem.hasClass('this-item-is-title')) {
                                $insertedItem = $thisItem.children('h6');
                                $insertedItem.clone().appendTo($insertItem).wrap('<li/>').parent('li').addClass('title-mega-col');
                                $thisItem.remove();
                            } else {
                                $insertedItem = $thisItem.children('a');
                                $insertedItem.removeClass().removeAttr('data-toggle');
                                $insertedItem.clone().appendTo($insertItem).wrap('<li/>');
                                $thisItem.remove();
                            }
                        });
                    }
                });
            }
        },
        headerHelper: function (type) {
            var $helper = $('.header-helper'),
			  $header = $('.header'),
			  counter = 0,
			  headerHeight,
			  tt;
            if ($helper.size() > 0) {
                if (type === 'load') {
                    tt = setInterval(function () {
                        if (counter === 10) {
                            clearInterval(tt);
                        } else {
                            counter++;
                        }
                        headerHeight = $header.outerHeight(true);
                        $helper.css({
                            'height': headerHeight
                        });
                    }, 100);
                    $(window).trigger('resize');
                } else {
                    headerHeight = $header.outerHeight(true);
                    $helper.css({
                        'height': headerHeight
                    });
                }
            } else {
                if (type === 'load') {
                    $(window).trigger('resize');
                }
            }
        },
        stickyStyle: function () {
            var $header = $('.header'),
			  headerStyle = $header.data('bgparams') ? $header.data('bgparams').split(',') : [],
			  r, g, b, opacity;
            if ($header.hasClass('header_sticky_enable')) {
                if (headerStyle.length > 0) {
                    r = headerStyle[0];
                    g = headerStyle[1];
                    b = headerStyle[2];
                    opacity = headerStyle[3];
                    if (opacity !== '1') {
                        if ($win.scrollTop() > 10) {
                            $header.css('background-color', 'rgba(' + r + ',' + g + ',' + b + ',1)');
                        } else {
                            $header.css('background-color', 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')');
                        }
                    }
                }
            }
        },
        simpleMenu: function () {
            var $menuWrapper = $('.simple-menu-pages'),
			  $link = $menuWrapper.find('.menu-item-has-children > a'),
			  $back = $('.simple-menu-back');

            $link.on('click', function (e) {
                var $this = $(this),
				  id = $this.parent().data('id'),
				  $simpleElement = $this.closest('.simple-menu-element'),
				  $openElement = $menuWrapper.children('div[data-id="' + id + '"]'),
				  depth;
                if ($openElement.size() > 0 && !$this.parent().hasClass('simple-mega-link')) {
                    e.preventDefault();
                    $simpleElement.addClass('fade').removeClass('in').delay(150).queue('fx', function () {
                        $(this).addClass('out').dequeue();
                        $('.menu-simple-controls').css('opacity', '0');
                    });
                    setTimeout(function () {
                        $back.addClass('in').attr('href', '#menu-item-' + id);
                    }, 200);
                    $openElement.delay(330).queue('fx', function () {
                        setTimeout(function () {
                            $('.menu-simple-controls').css('opacity', '1');
                        }, 100);
                        $(this).removeClass('out').addClass('in').dequeue();

                        if ($(this).hasClass('simple-menu-mega')) {
                            $(this).closest('.modal-dialog').addClass('modal-dialog-mega');
                        } else {
                            $(this).closest('.modal-dialog').removeClass('modal-dialog-mega');
                        }

                        depth = parseInt($(this).data('depth'));
                        $('.current-lvl').text('0' + depth);
                        $('.prev-lvl').text('0' + (depth - 1));
                    });
                }
            });

            $back.on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
				  goto = $this.attr('href'),
				  id = goto.replace(/\D/g, ''),
				  $goto, $simpleElement, parentId, $openElement, depth;
                if (goto && $(goto).size() > 0) {
                    $goto = $(goto);
                    $simpleElement = $goto.closest('.simple-menu-element');
                    parentId = $simpleElement.data('id');
                    $openElement = $menuWrapper.children('div[data-id="' + id + '"]');
                    if ($simpleElement.size() > 0) {
                        $('.modal-dialog').removeClass('modal-dialog-mega');
                        $openElement.addClass('fade').removeClass('in').delay(150).queue('fx', function () {
                            $(this).addClass('out').dequeue();
                            $('.menu-simple-controls').css('opacity', '0');

                            depth = parseInt($simpleElement.data('depth'));
                            if (!depth) {
                                depth = '1';
                            }
                            $('.current-lvl').text('');
                            $('.prev-lvl').text('');
                            setTimeout(function () {
                                $('.current-lvl').text('0' + depth);
                                $('.prev-lvl').text('0' + (depth - 1));
                            }, 100);
                        });
                        $simpleElement.delay(330).queue('fx', function () {
                            setTimeout(function () {
                                $('.menu-simple-controls').css('opacity', '1');
                            }, 100);
                            $(this).removeClass('out').addClass('in').dequeue();
                        });

                        if (parentId === undefined || parentId === null) {
                            $back.removeClass('in').attr('href', '');
                            $('.prev-lvl').text('');
                            $('.current-lvl').text('');
                        } else {
                            $back.addClass('in').attr('href', '#menu-item-' + parentId);
                        }
                    }
                }
            });
        },
        typography: function () {
            $('img').parent('a').addClass('reset-link');
            $('.table-responsive > p, .reset-list > p, dl > br').remove();
        },
        scrollTop: function () {
            var instance = this;
            if ($win.scrollTop() > 300) {
                instance.scrTop.addClass('vis');
            } else {
                instance.scrTop.removeClass('vis').removeAttr('style');
            }
        },
        iePlaceholder: function () {
			// *** IE9 placeholder *** //
            if (document.all && !window.atob) {
                $('[placeholder]').focus(function () {
                    var input = $(this);
                    if (input.val() === input.attr('placeholder')) {
                        input.val('');
                        input.removeClass('placeholder');
                    }
                }).blur(function () {
                    var input = $(this);
                    if (input.val() === '' || input.val() === input.attr('placeholder')) {
                        input.addClass('placeholder');
                        input.val(input.attr('placeholder'));
                    }
                }).blur().parents('form').submit(function () {
                    $(this).find('[placeholder]').each(function () {
                        var input = $(this);
                        if (input.val() === input.attr('placeholder')) {
                            input.val('');
                        }
                    });
                });
            }
        },
        browserDetect: function () {
            var browserType = '',
			  isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0,
			  isFirefox = typeof InstallTrigger !== 'undefined',
			  isSafari = /constructor/i.test(window.HTMLElement) || (function (p) {
      return p.toString() === '[object SafariRemoteNotification]';
  })(!window['safari'] || safari.pushNotification),
			  isIE = /* @cc_on!@ */false || !!document.documentMode,
			  isEdge = !isIE && !!window.StyleMedia,
			  isChrome = !!window.chrome && !!window.chrome.webstore,
			  isBlink = (isChrome || isOpera) && !!window.CSS;
            if (isOpera) {
                browserType = 'is-opera';
            } else if (isFirefox) {
                browserType = 'is-firefox';
            } else if (isSafari) {
                browserType = 'is-safari';
            } else if (isIE) {
                browserType = 'is-ie';
            } else if (isEdge) {
                browserType = 'is-edge';
            } else if (isChrome) {
                browserType = 'is-chrome';
            } else if (isBlink) {
                browserType = 'is-blink';
            }
            $html.addClass(browserType);
        },
        osDetect: function () {
            var OSName = 'unknown-os';
            if (navigator.appVersion.indexOf('Win') !== -1) OSName = 'windows-os';
            if (navigator.appVersion.indexOf('Mac') !== -1) OSName = 'mac-os';
            if (navigator.appVersion.indexOf('X11') !== -1) OSName = 'unix-os';
            if (navigator.appVersion.indexOf('Linux') !== -1) OSName = 'linux-os';
            $html.addClass(OSName);
        },
        mobileDetect: function () {
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                $body.addClass('is-mobile');
            } else {
                $body.addClass('no-mobile');
            }
        },
        ieDetect: function () {
            var ua = window.navigator.userAgent,
			  msie = ua.indexOf('MSIE '),
			  trident = ua.indexOf('Trident/'),
			  edge = ua.indexOf('Edge/');

            if (msie > 0) {
                $html.addClass('ie-10');
            }			else if (trident > 0) {
                $html.addClass('ie-11');
            }			else if (edge > 0) {
                $html.addClass('ie-edge');
            }			else				{ return false; }
        },
        isEmpty: function (el) {
            return !$.trim(el.html());
        },
        stickFooter: function (type) {
            var instance = this,
			  $help = $('.h-footer'),
			  $footer = $('footer[role=contentinfo]'),
			  $footerRow = $footer.find('.footer-row'),
			  fHeight = $footer.height(),
			  fHeightOuter;
            if ($footerRow.size() > 0 && instance.isEmpty($footerRow)) {
                $footerRow.remove();
            } else {
                if (!$body.hasClass('is-fix-footer')) {
                    $footer.css({
                        marginTop: -fHeight
                    });
                    $help.css({
                        height: fHeight
                    });
                    if (type === 'load' && $('.footer .widget_revslider').size() > 0) {
                        setTimeout(function () {
                            $(window).trigger('resize');
                        }, 1000);
                    }
                } else {
                    if (type === 'load' && $help.size() > 0) {
                        $help.remove();
                        $('html').css({
                            'height': 'auto'
                        });
                    }
                    fHeightOuter = $footer.outerHeight(true);
                    if (type === 'load') {
                        setTimeout(function () {
                            fHeightOuter = $footer.outerHeight(true);
                            $body.css({
                                'margin-bottom': fHeightOuter
                            });
                        }, 600);
                    } else {
                        $body.css({
                            'margin-bottom': fHeightOuter
                        });
                    }
                }
            }
        }
    };
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
				  new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);
jQuery(document).ready(function ($) {
    $(document.body).Aisconverse();
});
