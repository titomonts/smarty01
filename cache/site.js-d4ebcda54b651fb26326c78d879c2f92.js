/*
sacy javascript cache dump 

This dump has been created from the following files:
    - <root>/resources/theylangylang/js/site.js
*/


/* <root>/resources/theylangylang/js/site.js */

var $=jQuery.noConflict();$(window).bind('load',function(e){if(window.location.hash){setTimeout(function(){window.scrollTo(0,$('.header').outerHeight());},1);setTimeout(function(){$('.spy a[href^="'+window.location.hash+'"]').trigger('click');var scrollV,scrollH,loc=window.location;if("replaceState"in history)
history.replaceState("",document.title,loc.pathname+loc.search);return false;},1000);}});$(document).ready(function(){var monitor=setInterval(function(){var elem=document.activeElement;if(elem&&elem.tagName=='IFRAME'){$('iframe').addClass('active');clearInterval(monitor);}},100);$('.nav li').bind('mouseover mousemove',function(e){var li=$(this);li.addClass('hover');});$('.nav li').bind('mouseleave',function(e){var li=$(this);setTimeout(function(){li.removeClass('hover')},500);});$('.nav li a').bind('click',function(e){if(Modernizr.touchevents){$('.subnav').removeClass('current');if($(this).siblings('.subnav').size()){e.preventDefault();$(this).siblings('.subnav').addClass('current');$('.subnav').not('.current').removeClass('slide');$(this).siblings('.subnav').toggleClass('slide');}}});setTimeout(function(){$('.header-container .header').wrap('<div class="sticky-wrapper" />').parent().css({height:$('.header-container .header').outerHeight()}).find('.header').addClass('wrapped');$('.footer-container .footer').wrap('<div class="sticky-wrapper" />').parent().css({height:$('.footer-container .footer').outerHeight()});$('.spy').wrap('<div class="spy-wrapper" />').parent().css({height:$('.spy').outerHeight()});},1000);var lastScrollTop=0;var initialScrollEvent=true;setTimeout(function(){$(window).bind('scroll',function(e){if(!initialScrollEvent){var st=$(this).scrollTop();if(st>lastScrollTop){if($(window).scrollTop()>=1){$('.header-container').addClass('sticky');}else{$('.header-container').removeClass('sticky');}
if($(window).scrollTop()<=parseInt($('.footer-container').offset().top)-screen.height){$('.footer-container').addClass('sticky');$('.footer-container').removeClass('unsticky');}else{$('.footer-container').removeClass('sticky');$('.footer-container').addClass('unsticky');}}else{if($(window).scrollTop()<=1){$('.header-container').removeClass('sticky');setTimeout(function(){$('.header-container .sticky-wrapper').css({height:$('.header-container .header').outerHeight()});},500);}else{$('.header-container').addClass('sticky');}
if($(window).scrollTop()<=parseInt($('.footer-container').offset().top)-screen.height){$('.footer-container').addClass('sticky');$('.footer-container').removeClass('unsticky');}else{$('.footer-container').removeClass('sticky');$('.footer-container').addClass('unsticky');}}
if($('.spy-wrapper').size()){if($(window).scrollTop()>=parseInt($('.spy-wrapper').offset().top)-$('.header').outerHeight()-parseInt($('.header').css('margin-top'))){$('.spy-wrapper').addClass('sticky');$('.spy-wrapper .spy').css({'top':$('.header').outerHeight()+$('#header-enquire').outerHeight()+parseInt($('.header').css('margin-top'))});}else{$('.spy-wrapper').removeClass('sticky');$('.spy-wrapper .spy').removeAttr('style');}
$('.spy li').each(function(){if($(window).scrollTop()>=parseInt($($(this).find('a').attr('href')).offset().top)-$('.header').outerHeight()-$('.spy').outerHeight()&&$(window).scrollTop()<=parseInt($($(this).find('a').attr('href')).offset().top)-$('.header').outerHeight()-$('.spy').outerHeight()+$($(this).find('a').attr('href')).outerHeight()-1){$(this).addClass('active');}else{$(this).removeClass('active');}});}
lastScrollTop=st;}
initialScrollEvent=false;parallaxScroll(0);});$('.spy li').bind('click',function(e){e.preventDefault();var th=$(this)
if($(window).scrollTop()<2){window.scrollTo(0,2);setTimeout(function(){$("html, body").animate({scrollTop:parseInt($(th.find('a').attr('href')).offset().top)-$('.header').outerHeight()-$('.spy').outerHeight()-parseInt($('.header').css('margin-top'))},1000);},300);}else{$("html, body").animate({scrollTop:parseInt($($(this).find('a').attr('href')).offset().top)-$('.header').outerHeight()-$('.spy').outerHeight()-parseInt($('.header').css('margin-top'))},1000);}});},10);$(window).bind('resize',function(e){$('.header-container').removeClass('sticky');$('.header-container .sticky-wrapper').css({height:$('.header-container .header').outerHeight()});$('.footer-container').removeClass('sticky');$('.footer-container').addClass('unsticky');$('.footer-container .sticky-wrapper').css({height:$('.footer-container .footer').outerHeight()});$('.spy-wrapper').removeClass('sticky');$('.spy-wrapper').css({height:$('.spy').outerHeight()});$(window).trigger('scroll');if(parseInt($('.gallery-container li img').height())!=0){$('.gallery-container li a').css({height:$('.gallery-container li img').height()});}
if($(window).height()<600){$('.modal-box .modal-body').css('max-height',$(window).height()*.9);}else{$('.modal-box .modal-body').removeAttr('style');}
$('.rates .tabs table td').each(function(){var idx=$(this).index();$(this).attr('data-text',$('.rates .tabs table th:eq('+idx+')').text());});});setTimeout(function(){$(window).trigger('resize');},1000);$('.toggler').bind('click',function(event){event.preventDefault();$(this).toggleClass('active');$('.nav').toggleClass('open');});$('html').bind('click',function(e){if($(window).width()<768){if($(e.target).is('.nav li, .nav li a, .toggler, .toggler *')){return true;}
$('.toggler').removeClass('active');$('.nav').removeClass('open');$('.subnav').removeClass('slide');}});if($('.hero-container .slideshow').size()){var owl_header=$('.hero-container .slideshow');owl_header.addClass('owl-carousel').owlCarousel({autoplay:true,autoplayTimeout:9000,autoplayHoverPause:true,dots:false,lazyLoad:true,items:1,center:true,loop:true,animateIn:'fadeInRight',animateOut:'fadeOutLeft',nav:true,navText:['<img src="'+dir+'/images/slide-arrow-prev.png">','<img src="'+dir+'/images/slide-arrow-next.png">']});}
$('.ta-link').show().parent('p').appendTo('.right');var owl_review=$('.reviews .content .slides');owl_review.addClass('owl-carousel').owlCarousel({autoplay:true,autoplayTimeout:4000,autoplayHoverPause:true,dots:false,loop:true,lazyLoad:true,autoHeight:true,items:1,animateIn:'fadeInUp',animateOut:'fadeOutUp'});if($('.reviews-container').size()){if($('.reviews-container .review').size()){pagination('.reviews-pagination','.reviews-container .review',10)}else if($('.reviews-container .slides > li').size()){pagination('.reviews-pagination','.reviews-container .slides > li',10)}}
if($('.gallery-container').size()){$('body').append('<div class="popup-container popup-slide"><span class="close"><i class="fa fa-times" aria-hidden="true"></i></span><ul class="owl-carousel owl-slide"></ul></div>');$('body').append('<div class="popup-container popup-light"><span class="close"><i class="fa fa-times" aria-hidden="true"></i></span><ul class="owl-carousel owl-light"></ul></div>');var slide_ind=0;var slide_cnt=$('.gallery-container li').size();var popup_holder='';$('.gallery-container li').each(function(){var imgurl=$(this).find('a').attr('href');var imgalt=$(this).find('img').attr('alt');popup_holder+='<li><div class="loader"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div><img data-src="'+imgurl+'" alt="'+imgalt+'" /><span class="caption">'+imgalt+'</span></li>';});$('.popup-container .owl-slide').html(popup_holder);$('.popup-container .owl-slide').owlCarousel({autoPlay:false,navigation:true,navigationText:['<i class="fa fa-chevron-left" aria-hidden="true"></i>','<i class="fa fa-chevron-right" aria-hidden="true"></i>'],paginationSpeed:1000,goToFirstSpeed:2000,singleItem:true,transitionStyle:'fade'});$('.popup-container .owl-light').html('');$('.gallery-container li a').bind('click',function(e){e.preventDefault();slide_ind=$(this).parent().index();if($(window).width()>1023){var owlpopup=$(".popup-container .owl-carousel").data('owlCarousel');owlpopup.goTo($(this).parent().index());$('.popup-slide').fadeIn();}else{var imgurl=$(this).attr('href');var imgalt=$(this).find('img').attr('alt');$('.popup-light .owl-light').removeClass('owl-hidden').addClass('owl-loaded').html('<div class="owl-wrapper-outer"><div class="owl-wrapper" style="display:block"><div class="owl-item" style="float: none;"><li><div class="loader"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div><img data-src="'+imgurl+'" alt="'+imgalt+'" /><span class="caption">'+imgalt+'</span></li></div></div></div><div class="owl-controls">><div class="owl-buttons"><div class="owl-prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></div><div class="owl-next"><i class="fa fa-chevron-right" aria-hidden="true"></i></div></div></div>');$('.popup-light .owl-carousel').css({opacity:1,display:'block'});$('.popup-light').fadeIn();}
$('.popup-container li img').each(function(){if(!$(this).hasClass('lazyload')&&!$(this).hasClass('lazyloading')&&!$(this).hasClass('lazyloaded')){$(this).addClass('lazyload');}});});$(document).on('click','.popup-light .owl-light .owl-nav .owl-prev',function(e){e.preventDefault();if(slide_ind>0){$('.gallery-container li').eq(slide_ind-1).find('a').trigger('click');}});$(document).on('click','.popup-light .owl-light .owl-nav .owl-next',function(e){e.preventDefault();if(slide_ind<slide_cnt){$('.gallery-container li').eq(slide_ind+1).find('a').trigger('click');}});var lastX;$('.popup-light').bind('touchstart',function(e){lastX=e.originalEvent.touches[0].clientX;});$('.popup-light').bind('touchend',function(e){var currentX=e.originalEvent.changedTouches[0].clientX;if(currentX>lastX){if(slide_ind>0){$('.gallery-container li').eq(slide_ind-1).find('a').trigger('click');}}else if(currentX<lastX){if(slide_ind<slide_cnt){$('.gallery-container li').eq(slide_ind+1).find('a').trigger('click');}}});$('.popup-container').bind('click',function(e){e.preventDefault();if($(e.target).is('span, i, img, .owl-prev, .owl-next')){return true;}
$('.popup-container').fadeOut();});$('.popup-container .close').bind('click',function(e){e.preventDefault();$('.popup-container').fadeOut();});$('.gallery-container li img').bind('load',function(){if(parseInt($('.gallery-container li img').height())!=0){$('.gallery-container li a').css({height:$('.gallery-container li img').height()});$('.gallery-container li img').unbind('load');}});pagination('.gallery-pagination','.gallery-container li',12)}
$(document).ready(function(){var bookingbar=$('#header-enquire').offset().top-$('.header').outerHeight()-parseInt($('.header').css('margin-top'));$(window).scroll(function(){var currentScroll=$(window).scrollTop();if(currentScroll>=bookingbar){$('#header-enquire').css({position:'fixed',top:$('.header').outerHeight()+parseInt($('.header').css('margin-top'))});}else{$('#header-enquire').css({position:'relative',top:0});}});});if($('.spy-tabs').size()){var tabsintval=setInterval(function(){if(parseInt($('.spy-tabs ul:first a:first').outerHeight())>0){$('.spy-tabs ul:first').wrap('<span class="spy" />').parent().wrap('<span class="spy-wrapper" />').parent().css({height:$('.spy-tabs ul:first a:first').outerHeight(),display:'block'});clearInterval(tabsintval);}},1000);}
$('.tabs > ul, .tabs .spy ul').each(function(){var first_tab_id=$(this).children('li:first a').attr('href');if(window.location.hash&&!$('.tabs .tabs').size()){$('.tabs ul li a[href^="'+window.location.hash+'"]').trigger('click');}else{$(this).children('li').removeClass('active');$(this).children('li:first').addClass('current active');$(this).closest('.tabs').find('div:first').addClass('current');}});$('.tabs > ul li a, .tabs .spy ul li a').bind('click',function(e){e.preventDefault();var tab_id=$(this).attr('href');$(this).closest('ul').find('li').removeClass('current active');$(this).closest('.tabs').children('div').removeClass('current');$(this).closest('li').addClass('current active');$(this).closest('.tabs').find(tab_id).addClass('current');});$('body').append('<div class="modal-overlay js-modal-close" style="display: none;"></div><div id="popup" class="modal-box"><header><a href="#" class="js-modal-close close">×</a></header><div class="modal-body"></div></div>');$('[data-dialog]').bind('click',function(e){e.preventDefault();$(".modal-overlay").fadeTo(500,0.7);$('#popup .modal-body').load($(this).attr('data-dialog'));$('#popup').fadeIn();});$(".js-modal-close, .modal-overlay").bind('click',function(e){e.preventDefault();$(".modal-box, .modal-overlay").fadeOut(500);});$('[data-popover]').bind('mouseover',function(){var drct=$(this).attr('data-popover-direction');var ths=$(this);if(!$('.popover').size())$('body').append('<div class="popover" />');$('.popover').html(ths.attr('data-popover')).addClass('hover');switch(drct){case'bottom':$('.popover').addClass('bottom').css({top:ths.offset().top+((ths.outerHeight()+10)+$('.popover').outerHeight()),left:ths.offset().left+(ths.outerWidth()/2)});break;default:$('.popover').removeClass('bottom').css({top:ths.offset().top-(ths.outerHeight()/2),left:ths.offset().left+(ths.outerWidth()/2)});break;}});$('[data-popover]').bind('mouseleave',function(){$('.popover').removeClass('hover').removeAttr('style');});$('#opener').bind('click',function(e){e.preventDefault();$(".modal-overlay").fadeTo(500,0.7);$('#popup .modal-body').html($('#dialog').html());$('#popup').fadeIn();});$(".js-modal-close, .modal-overlay").bind('click',function(e){e.preventDefault();$(".modal-box, .modal-overlay").fadeOut(500);});if($('#map_div').size()){initialize_map('map_div','/ketewel-bali.html',[{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]}]);}
if($('#map_div_locale').size()){initialize_map('map_div_locale','/ketewel-bali.html',[{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]}]);}});function parallaxScroll(gutter){var scrolledY=$(window).scrollTop()-gutter;if(scrolledY<0){scrolledY=0;}
if($('.hero img').hasClass('lazyloaded')){$('.hero img').removeClass('lazyloaded');}
$('.hero img').css('top','+'+((scrolledY*0.5))+'px');$('.hero img').css('position','relative');$('.hero img').addClass('new-parallax');$('.hero').css({'height':$('.hero img').height()+'px','overflow':'hidden'});if(scrolledY<=0){$('.hero, .hero img').removeAttr('style');}}
function pagination(pagination_container,element,limit){var lim=limit;var len=$(element).length;$(element).addClass('current');$(element+':gt('+(lim-1)+')').removeClass('current');var totalPage=Math.round(len/lim);if(lim<len){$(pagination_container).html('');$(pagination_container).append('<a href="javascript:void(0);" class="prev"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a>');for(var i=1;i<=totalPage;i++){$(pagination_container).append('<a href="javascript:void(0);" class="page">'+i+'</a>');}
$(pagination_container).append('<a href="javascript:void(0);" class="next"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>');$(pagination_container+' a.page:first').addClass('active');$(pagination_container+' a.prev').css({'opacity':0,'visibility':'hidden'});}
$(pagination_container+' a.page').click(function(){var index=$(this).index();var gt=lim*index;$(pagination_container+' a.page').removeClass('active');$(this).addClass('active');$(element).removeClass('current');for(var i=gt-lim;i<gt;i++){$(element+':eq('+i+')').addClass('current');}});$(pagination_container+' a.prev').click(function(){var index=$(pagination_container+' a.page.active').index()-2;var gt=lim*(index+1);$(pagination_container+' a.page').removeClass('active');$(pagination_container+' a.page:eq('+index+')').addClass('active');$(element).removeClass('current');for(var i=gt-lim;i<gt;i++){$(element+':eq('+i+')').addClass('current');}});$(pagination_container+' a.next').click(function(){var index=$(pagination_container+' a.page.active').index();var gt=lim*(index+1);$(pagination_container+' a.page').removeClass('active');$(pagination_container+' a.page:eq('+index+')').addClass('active');$(element).removeClass('current');for(var i=gt-lim;i<gt;i++){$(element+':eq('+i+')').addClass('current');}});$(pagination_container+' a').click(function(){if($(pagination_container+' a.page:first').hasClass('active')){$(pagination_container+' a.prev').css({'opacity':0,'visibility':'hidden'});}else{$(pagination_container+' a.prev').removeAttr('style');}
if($(pagination_container+' a.page:last').hasClass('active')){$(pagination_container+' a.next').css({'opacity':0,'visibility':'hidden'});}else{$(pagination_container+' a.next').removeAttr('style');}
$("html, body").animate({scrollTop:parseInt($(element).parent().offset().top)-$('.header').outerHeight()},500);});}
function initialize_map(id,url,style){var latlng=new google.maps.LatLng(lat,long);var latlng_center=new google.maps.LatLng(lat,long);if(style){var options={zoom:15,center:latlng_center,scrollwheel:false,mapTypeControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};}else{var options={zoom:16,center:latlng,mapTypeControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};}
var map=new google.maps.Map(document.getElementById(id),options);var styles=[{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]}];if(style){map.setOptions({styles:style});}else{map.setOptions({styles:styles});}
var image=new google.maps.MarkerImage('/resources/theylangylang/images/lemarquer.png',new google.maps.Size(46,60),new google.maps.Point(0,0),new google.maps.Point(23.5,56));if(style){var marker=new google.maps.Marker({position:new google.maps.LatLng(lat,long),map:map,animation:google.maps.Animation.DROP,icon:image});if(url){marker.addListener('click',function(){window.location=url;});}}else{var marker=new google.maps.Marker({position:new google.maps.LatLng(lat,long),map:map,animation:google.maps.Animation.DROP});var infowindow=new google.maps.InfoWindow({content:infocontent});marker.addListener('click',function(){infowindow.open(map,marker1);});}
if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){map.setOptions({'draggable':false});map.setCenter(latlng);var longpress;$(document).bind('touchstart',function(event){longpress=setTimeout(function(){map.setOptions({'draggable':true});},200)}).bind('touchend',function(event){clearTimeout(longpress);map.setOptions({'draggable':false});});}
$(window).resize(function(){if(style){map.setCenter(latlng_center);}else{map.setCenter(latlng);}
if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){map.setCenter(latlng);}});}