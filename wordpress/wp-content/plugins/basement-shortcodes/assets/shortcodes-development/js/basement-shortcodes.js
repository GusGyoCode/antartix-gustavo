
// Main Google Map Function
function basementInitMap (selector) {
	selector = selector || '';
	(function ( $, window, document ) {
		function gmapScreenType() {
			var envs = ['xs', 'sm', 'md', 'lg'],
			  $el = $('<div>');
			
			$el.appendTo($('body'));
			
			for (var i = envs.length - 1; i >= 0; i--) {
				var env = envs[i];
				
				$el.addClass('hidden-'+env);
				if ($el.is(':hidden')) {
					$el.remove();
					return env
				}
			}
		}
		function map_move(googleMap, positions, screen) {
			var coordinates = positions[screen].split(',');
			
			if(coordinates[0] && coordinates[1]) {
				var x = parseInt(coordinates[0]),
				  y = parseInt(coordinates[1]);
				
				switch (screen) {
					case 'lg' :
						googleMap.panBy(x, y);
						break;
					case 'md' :
						googleMap.panBy(x, y);
						break;
					case 'sm' :
						googleMap.panBy(x, y);
						break;
					case 'xs' :
						googleMap.panBy(x, y);
						break;
				}
			}
		}
		
		if ( typeof google === 'object' && typeof google.maps === 'object' ) {
			var instance = this,
			  marker,
			  modalMapLink = $('.a-map'),
			  modalMapClose = $('.map-close'),
			  $map = $(selector + '.google-map'),
			  globalScreenType = gmapScreenType();
			
			if ($map.length > 0) {
				$.each($map, function (index, map) {
					var mapContainer = $(map).find('.google-map-container'),
					  mapDataCenter = mapContainer.data('center').split(','),
					  mapDataMarkers = mapContainer.data('markers').split('; '),
					  positions = mapContainer.data('position'),
					  latlng = new google.maps.LatLng(mapDataCenter[0], mapDataCenter[1]),
					  mapOptions = {
						  zoom: mapContainer.data('zoom'),
						  scrollwheel: false,
						  panControl: mapContainer.data('pan-control') ? mapContainer.data('pan-control') : false,
						  navigationControl: mapContainer.data('navigation-control') ? mapContainer.data('navigation-control') : false,
						  mapTypeControl: mapContainer.data('map-type-control') ? mapContainer.data('map-type-control') : false,
						  scaleControl: mapContainer.data('scale-control') ? mapContainer.data('scale-control') : false,
						  draggable: true,
						  zoomControl: true,
						  streetViewControl: false,
						  zoomControlOptions: {
							  style: google.maps.ZoomControlStyle.SMALL,
							  position: google.maps.ControlPosition.LEFT_CENTER
						  },
						  center: latlng,
						  styles: mapContainer.data('style') ? mapContainer.data('style') : []
					  },
					  styleName = mapContainer.data('style-name') ? mapContainer.data('style-name') : '',
					  googleMap = new google.maps.Map(mapContainer.get(0), mapOptions),
					  i = 0,
					  markerLength = mapContainer.data('markers').split('; ').length,
					  markerColor = ['#1f1f1f'];
					
					if(styleName === 'shades_gray') {
						markerColor = ['#ffffff'];
					}
					
					
					for (i; i < markerLength; i++) {
						marker = new google.maps.Marker({
							map: googleMap,
							position: new google.maps.LatLng(mapDataMarkers[i].split(',')[0], mapDataMarkers[i].split(',')[1]),
							icon: {
								path: "M13.567,26.156h0.862v28.958h-0.862V26.156z M13.567,0c7.532,0,13.562,6.03,13.562,13.562c0,7.427-6.029,13.567-13.562,13.567C6.029,27.128,0,20.988,0,13.561C-0.001,6.029,6.028,0,13.567,0z",
								fillColor: markerColor[i],
								fillOpacity: 1,
								strokeColor: markerColor[i],
								anchor: new google.maps.Point(18, 40)
							}
						});
					}
					
					var initCenter = googleMap.getCenter();
					
					if(positions) {
						map_move(googleMap,positions, globalScreenType);
					}
					
					google.maps.event.addDomListener(window, "resize", function() {
						if(positions) {
							var currentScreen = gmapScreenType();
							
							google.maps.event.trigger(googleMap, "resize");
							googleMap.setCenter(initCenter);
							map_move(googleMap,positions, currentScreen);
							
						}
					});
					
					
					
					mapContainer.parents('.map-wrap').find(modalMapLink).on('click', function (e) {
						e.preventDefault();
						var self = $(this),
						  popupContainer = $(self.attr('href')),
						  mapPopup = popupContainer.find('.google-map-popup');
						
						setTimeout(function () {
							var googlePopup = new google.maps.Map(mapPopup.get(0), mapOptions);
							for (i = 0; i < markerLength; i++) {
								marker = new google.maps.Marker({
									map: googlePopup,
									position: new google.maps.LatLng(mapDataMarkers[i].split(',')[0], mapDataMarkers[i].split(',')[1]),
									icon: {
										path: "M13.567,26.156h0.862v28.958h-0.862V26.156z M13.567,0c7.532,0,13.562,6.03,13.562,13.562c0,7.427-6.029,13.567-13.562,13.567C6.029,27.128,0,20.988,0,13.561C-0.001,6.029,6.028,0,13.567,0z",
										fillColor: markerColor[i],
										fillOpacity: 1,
										strokeColor: markerColor[i],
										anchor: new google.maps.Point(18, 40)
									}
								});
							}
						}, 50);
					});
				});
			}
			
			modalMapClose.on('click', function () {
				$('.google-map-popup').removeAttr('style').children().remove();
			});
		}
	}) (jQuery, window, document);
}


(function($, window, document){
	'use strict';
	
	var pluginName = "BasementShortcodes",
	  $win = $(window),
	  $doc = $(document),
	  init,
	  defaults = {},
	  $html = $('html'),
	  $body = $('body'),
	  $htmlbody = $('html, body');
	
	function Basement_Shortcodes(element, options) {
		var that = this;
		that.element = $(element);
		that.options = $.extend({}, defaults, options);
		
		
		that.countDown('');
		that.verticalTitle();
		
		$win.on('load', function(){
			that.share();
			that.animationSVG('');
			that.counterTo('');
		}).on('scroll',function(){
			that.share();
		}).on('resize', function () {
			that.share();
		});
		
		$(document).on('basement_shortcodes_bind', function(){
			//that.yaShare();
			that.share();
			basementInitMap('.modal-maincontent ');
			that.counterTo('.modal-maincontent ');
			that.countDown('.modal-maincontent ');
			that.animationSVG('.modal-maincontent ');
		});
		
	}
	
	Basement_Shortcodes.prototype = {
		verticalTitle : function () {
			var $vertRight = $('.vc_vertical_right');
			
			if($vertRight.size() > 0) {
				$vertRight.find('.rotated-text').each(function () {
					var $this = $(this),
					  width = $this.height();
					
					$this.css('right',width+'px');
				});
			}
		},
		share : function() {
			var $share = $('.theme-share-dropdown');
			
			$share.on('click', function(e){
				e.preventDefault();
			});
			if($share.size() > 0) {
				$share.each(function(){
					var $this = $(this),
					  btnOffset = $this.offset(),
					  btnOffsetTop = parseInt(btnOffset.top),
					  btnHeight = $this.outerHeight(true),
					  scrollTop = $win.scrollTop(),
					  docBottom = $doc.height(),
					  dropDownHeight = $this.find('.share-tooltip').outerHeight(true),
					  dropDownFullHeight = btnHeight + dropDownHeight - 25,
					  dropDownBottom = btnOffsetTop - dropDownFullHeight,
					  headerHeight = 0, adminBar = 0;
					
					
					if($('.header_sticky_enable').size() > 0) {
						headerHeight = $('.header_sticky_enable').outerHeight(true);
					}
					
					if($('#wpadminbar').size() > 0) {
						adminBar = $('#wpadminbar').outerHeight(true);
					}
					
					btnOffsetTop = dropDownBottom - (headerHeight + adminBar);
					
					if(scrollTop > btnOffsetTop) {
						$this.addClass('bottom-open')
					} else {
						$this.removeClass('bottom-open')
					}
				});
			}
			
		},
		screenType  : function () {
			var envs = ['xs', 'sm', 'md', 'lg'],
			  $el = $('<div>');
			
			$el.appendTo($('body'));
			
			for (var i = envs.length - 1; i >= 0; i--) {
				var env = envs[i];
				
				$el.addClass('hidden-'+env);
				if ($el.is(':hidden')) {
					$el.remove();
					return env
				}
			}
		},
		counterTo: function (selector) {
			var $counters = $(selector  + '.basement_counter'),
			  $progressBar = $(selector  + '.progress-bar');
			
			$progressBar.viewportChecker({
				offset: 20,
				callbackFunction: function (element, action) {
					var timeout = 50,
					  $preloader = $('.preloader');
					
					if($preloader.size() > 0 && $preloader.is(':hidden')) {
						timeout  = 50;
					} else if ($preloader.size() > 0 && $preloader.is(':visible')) {
						timeout  = 1000;
					}
					
					
					//setTimeout(function () {
						element.css('width', element.data('end') + '%');
					//},400);
				}
			});
			$counters.viewportChecker({
				offset: 20,
				callbackFunction: function (elem, action) {
					var timeout = 50,
					  $preloader = $('.preloader');
					
					if($preloader.size() > 0 && $preloader.is(':hidden')) {
						timeout  = 50;
					} else if ($preloader.size() > 0 && $preloader.is(':visible')) {
						timeout  = 1000;
					}
					//setTimeout(function () {
						
						$('#' + elem.attr('id')).countTo({
							formatter: function (value, options) {
								var valEnd = value.toFixed(options.decimals).toString();
								return valEnd.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
							}
						});
					//},400);
				}
			});
		},
		countDown: function (selector) {
			var $countdown = $(selector + '.vc_countdown');
			if ($countdown.size() > 0) {
				$countdown.each(function () {
					var $timer = $(this),
					  dataDate = $timer.data('date') ? $timer.data('date') : '2017/06/01',
					  dataTime = $timer.data('time') ? $timer.data('time') : '00:00:00',
					  finalDate = dataDate + ' ' + dataTime,
					  dir = 'right';
					
					$timer.countdown(finalDate, function (event) {
						  var self = $(this);
						  
						  if(dir === 'right') {
						  	dir = 'left'
						  } else {
							  dir = 'right'
						  }
						  
						  self.html(event.strftime(
							'<div><span>%D</span><ins>Day%!D</ins></div><div class="vc-icon-timer '+dir+'"></div>' + '<div><span>%H</span><ins class="cd1">Hours</ins></div><i class="dot-timer"></i>' + '<div><span>%M</span><ins class="cd2">Minutes</ins></div><i class="dot-timer"></i>' + '<div><span>%S</span><ins class="cd3">Seconds</ins></div>'))
					});
				});
			}
		},
		animationSVG : function(selector){
			var $iconBlock = $(selector + '.vc_is_animate_icon'),
			  ids = [];
			
			if($iconBlock.size() > 0) {
				$iconBlock.each(function(){
					ids.push($(this).find('svg').attr('id'));
				});
				
				for(var i = 0; i<ids.length; i++) {
					var id = ids[i];
					if(id) {
						var obt1 = new Vivus(id, {type: 'oneByOne', duration: 150, animTimingFunction: Vivus.EASE });
					}
				}
				
			}
		}
	};
	
	
	
	
	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName,
				  new Basement_Shortcodes(this, options));
			}
		});
	};
	
})(jQuery, window, document);

jQuery(document).ready(function($){
	$(document.body).BasementShortcodes();
});