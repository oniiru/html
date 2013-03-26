var TTjquery = jQuery.noConflict();

/* ------------------------------------------------------------------------
Main Navigation
 * ------------------------------------------------------------------------- */
TTjquery(document).ready(function () {
    initTabs();
    TTjquery('ul.accordion').accordion({
        active: ".selected",
        autoHeight: false,
        header: ".opener",
        collapsible: true,
        event: "click"
    });
    initScrollTop()
});

function initNav() {
    var nav = TTjquery("#menu-main-nav");
    var duration = 260;
    TTjquery(nav).find(".sub-menu").css({
        left: 0
    });
    TTjquery(nav).find("> li").each(function () {
        var height = TTjquery(this).find("> .drop").height();
        TTjquery(this).find("> .drop").css({
            display: "none",
            height: 0,
            overflow: "hidden"
        });
        TTjquery(this).find(".drop li > .drop").css({
            display: "none",
            width: 0
        });
        if (!TTjquery.browser.msie) {
            TTjquery(this).find("> .drop").css({
                "opacity": 0
            });
            TTjquery(this).find(".drop li > .drop").css({
                "opacity": 0
            })
        }
        TTjquery(this).mouseenter(function () {
            TTjquery(this).addClass("hover");
            var drop = TTjquery(this).find("> .drop");
            if (TTjquery.browser.msie) {
                TTjquery(drop).css({
                    display: "block"
                }).stop().animate({
                    "height": height
                }, duration, function () {
                    TTjquery(this).css({
                        "overflow": "visible"
                    })
                })
            } else {
                TTjquery(drop).css({
                    display: "block"
                }).stop().animate({
                    "height": height,
                    "opacity": 1
                }, duration, function () {
                    TTjquery(this).css({
                        "overflow": "visible"
                    })
                })
            }
        }).mouseleave(function () {
            var _this = TTjquery(this);
            if (TTjquery.browser.msie) {
                TTjquery(this).find("> .drop").stop().css({
                    "overflow": "hidden"
                }).animate({
                    "height": 0
                }, duration, function () {
                    TTjquery(_this).removeClass("hover")
                })
            } else {
                TTjquery(this).find("> .drop").stop().css({
                    "overflow": "hidden"
                }).animate({
                    "height": 0,
                    "opacity": 0
                }, duration, function () {
                    TTjquery(_this).removeClass("hover")
                })
            }
        });
        TTjquery(this).find(".drop ul > li ").mouseenter(function () {
            TTjquery(this).addClass("hover");
            var pageW = getPageSize()[2];
            if (pageW < TTjquery(this).offset().left + 236 * 2) {
                TTjquery(this).find("> .drop").css({
                    left: 'auto',
                    right: 236
                })
            }
            if (TTjquery.browser.msie) {
                TTjquery(this).find("> .drop").css({
                    display: 'block'
                }).stop().animate({
                    "width": 236
                }, duration, function () {
                    TTjquery(this).css({
                        overflow: 'visible'
                    })
                })
            } else {
                TTjquery(this).find("> .drop").css({
                    display: 'block'
                }).stop().animate({
                    "width": 236,
                    "opacity": 1
                }, duration, function () {
                    TTjquery(this).css({
                        overflow: 'visible'
                    })
                })
            }
        }).mouseleave(function () {
            TTjquery(this).removeClass("hover");
            if (TTjquery.browser.msie) {
                TTjquery(this).find("> .drop").stop().css({
                    overflow: 'hidden'
                }).animate({
                    width: 0
                }, duration, function () {
                    TTjquery(this).css({
                        display: 'none'
                    })
                })
            } else {
                TTjquery(this).find("> .drop").stop().css({
                    overflow: 'hidden'
                }).animate({
                    width: 0,
                    "opacity": 0
                }, duration, function () {
                    TTjquery(this).css({
                        display: 'none'
                    })
                })
            }
        })
    })
}



(function(B){B(document).ready(function(){var mainNav=B('#menu-main-nav');var lis=mainNav.find('li');var shownav=TTjquery("#menu-main-nav");lis.children('ul').wrap('<div class="c" / >');var cElems=B('.c');cElems.wrap('<div class="drop" / >');cElems.before('<div class="t"></div>');cElems.after('<div class="b"></div>');TTjquery(shownav).find(".sub-menu").css({display:"block"});initNav()})})(TTjquery);(function(C){C(document).ready(function(){TTjquery("#menu-main-nav li:has(ul)").addClass("parent")})})(TTjquery);





function getPageSize(){var xScroll,yScroll;if(window.innerHeight&&window.scrollMaxY){xScroll=document.body.scrollWidth;yScroll=window.innerHeight+window.scrollMaxY}else if(document.body.scrollHeight>document.body.offsetHeight){xScroll=document.body.scrollWidth;yScroll=document.body.scrollHeight}else if(document.documentElement&&document.documentElement.scrollHeight>document.documentElement.offsetHeight){xScroll=document.documentElement.scrollWidth;yScroll=document.documentElement.scrollHeight}else{xScroll=document.body.offsetWidth;yScroll=document.body.offsetHeight}var windowWidth,windowHeight;if(self.innerHeight){windowWidth=self.innerWidth;windowHeight=self.innerHeight}else if(document.documentElement&&document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;windowHeight=document.documentElement.clientHeight}else if(document.body){windowWidth=document.body.clientWidth;windowHeight=document.body.clientHeight}if(yScroll<windowHeight){pageHeight=windowHeight}else{pageHeight=yScroll}if(xScroll<windowWidth){pageWidth=windowWidth}else{pageWidth=xScroll}return[pageWidth,pageHeight,windowWidth,windowHeight]}





/* ------------------------------------------------------------------------
Portfolio Image Fade
 * ------------------------------------------------------------------------- */
/**
(function (TTjquery) {
    TTjquery(document).ready(function () {});
    TTjquery(document).ready(function () {
        var t = TTjquery('[class^="attachment"]').length,
            i = 0;
        var init = setInterval(function () {
            if(t>0){
            TTjquery('[class^="attachment"]').eq(i).delay(400).fadeIn(500);
            }
            i++;
            if (i == t) {
                clearInterval(init);
                delete init
            }
        }, 200)
    })
})(TTjquery);
**/

(function (TTjquery) {
    TTjquery(window).load(function () {

        	TTjquery('[class^="attachment"]').each(function(index){
			var t = TTjquery('[class^="attachment"]').length;
			if(t>0){ // if there is image length, we fade in
				TTjquery(this).delay(400*index).fadeIn(500);
				}  
     		});

   });
})(TTjquery);



/* ------------------------------------------------------------------------
Portfolio Image Hover
 * ------------------------------------------------------------------------- */
(function(TTjquery){TTjquery(document).ready(function(){TTjquery('.preload').hover(function(){TTjquery(this).children().first().children().first().stop(true);TTjquery(this).children().first().children().first().fadeTo('normal',.90)},function(){TTjquery(this).children().first().children().first().stop(true);TTjquery(this).children().first().children().first().fadeTo('normal',0)})})})(TTjquery);





/* ------------------------------------------------------------------------
Button Hover
 * ------------------------------------------------------------------------- */
if (TTjquery.browser.msie) { /* time to download a new browser */ } else {
TTjquery(document).ready(function(){TTjquery(".ka_button, #ka-submit, #searchform #searchsubmit, .ka-form-submit, #mc_signup #mc_signup_submit, .fade-me").hover(function(){TTjquery(this).stop().animate({opacity:0.7},250)},function(){TTjquery(this).stop().animate({opacity:1.0},250)})});
TTjquery(document).ready(function(){TTjquery(".social_icons a").hover(function(){TTjquery(this).stop().animate({opacity:0.65},200)},function(){TTjquery(this).stop().animate({opacity:1},200)})});
}




/* ------------------------------------------------------------------------
Tabs
 * ------------------------------------------------------------------------- */
function initTabs(){TTjquery('ul.tabset').each(function(){var _list=TTjquery(this);var _links=_list.find('a.tab');_links.eq(0).addClass('active');_links.each(function(){var _link=TTjquery(this);var _href=_link.attr('href');var _tab=TTjquery(_href);if(_link.hasClass('active'))_tab.css({"opacity":1,"display":"block"});else _tab.css({"opacity":0,"display":"none"});_link.click(function(){_links.filter('.active').each(function(){TTjquery(TTjquery(this).removeClass('active').attr('href')).animate({"opacity":0},200,function(){TTjquery(this).css({"display":"none"});_link.addClass('active');_tab.css({"display":"block"}).animate({"opacity":1})})});return false})})})}





/* ------------------------------------------------------------------------
Scroll to Top
 * ------------------------------------------------------------------------- */
function initScrollTop(){var change_speed=1200;TTjquery('a.link-top').click(function(){if(!TTjquery.browser.opera){TTjquery('body').animate({scrollTop:0},{queue:false,duration:change_speed})}TTjquery('html').animate({scrollTop:0},{queue:false,duration:change_speed});return false})}








/* ------------------------------------------------------------------------
	Class: prettyPhoto
	Use: Lightbox clone for jQuery
	Author: Stephane Caron (http://www.no-margin-for-errors.com)
	Version: 3.1.2
------------------------------------------------------------------------- */
(function(TTjquery){TTjquery.prettyPhoto={version:'3.1.2'};TTjquery.fn.prettyPhoto=function(pp_settings){pp_settings=jQuery.extend({animation_speed:'normal',slideshow:5000,autoplay_slideshow:false,opacity:0.80,show_title:false,allow_resize:true,default_width:500,default_height:344,counter_separator_label:'/',theme:'light_square',horizontal_padding:20,hideflash:false,wmode:'transparent',autoplay:true,modal:false,deeplinking:false,overlay_gallery:false,keyboard_shortcuts:true,changepicturecallback:function(){},callback:function(){},ie6_fallback:true,markup:'<div class="pp_pic_holder"><div class="ppt">&nbsp;</div><div class="pp_top"><div class="pp_left"></div><div class="pp_middle"></div><div class="pp_right"></div></div><div class="pp_content_container"><div class="pp_left"><div class="pp_right"><div class="pp_content"><div class="pp_loaderIcon"></div><div class="pp_fade"><a href="#" class="pp_expand" title="Expand the image">Expand</a><div class="pp_hoverContainer"><a class="pp_next" href="#">next</a><a class="pp_previous" href="#">previous</a></div><div id="pp_full_res"></div><div class="pp_details"><div class="pp_nav"><a href="#" class="pp_arrow_previous">Previous</a><p class="currentTextHolder">0/0</p><a href="#" class="pp_arrow_next">Next</a></div><p class="pp_description"></p>{pp_social}<a class="pp_close" href="#">Close</a></div></div></div></div></div></div><div class="pp_bottom"><div class="pp_left"></div><div class="pp_middle"></div><div class="pp_right"></div></div></div><div class="pp_overlay"></div>',gallery_markup:'<div class="pp_gallery"><a href="#" class="pp_arrow_previous">Previous</a><div><ul>{gallery}</ul></div><a href="#" class="pp_arrow_next">Next</a></div>',image_markup:'<img id="fullResImage" src="{path}" />',flash_markup:'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',quicktime_markup:'<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',iframe_markup:'<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',inline_markup:'<div class="pp_inline">{content}</div>',custom_markup:'',social_tools:''},pp_settings);var matchedObjects=this,percentBased=false,pp_dimensions,pp_open,pp_contentHeight,pp_contentWidth,pp_containerHeight,pp_containerWidth,windowHeight=TTjquery(window).height(),windowWidth=TTjquery(window).width(),pp_slideshow;doresize=true,scroll_pos=_get_scroll();TTjquery(window).unbind('resize.prettyphoto').bind('resize.prettyphoto',function(){_center_overlay();_resize_overlay();});if(pp_settings.keyboard_shortcuts){TTjquery(document).unbind('keydown.prettyphoto').bind('keydown.prettyphoto',function(e){if(typeof TTjquerypp_pic_holder!='undefined'){if(TTjquerypp_pic_holder.is(':visible')){switch(e.keyCode){case 37:TTjquery.prettyPhoto.changePage('previous');e.preventDefault();break;case 39:TTjquery.prettyPhoto.changePage('next');e.preventDefault();break;case 27:if(!settings.modal)
TTjquery.prettyPhoto.close();e.preventDefault();break;};};};});};TTjquery.prettyPhoto.initialize=function(){settings=pp_settings;if(settings.theme=='pp_default')settings.horizontal_padding=16;if(settings.ie6_fallback&&TTjquery.browser.msie&&parseInt(TTjquery.browser.version)==6)settings.theme="light_square";theRel=TTjquery(this).attr('rel');galleryRegExp=/\[(?:.*)\]/;isSet=(galleryRegExp.exec(theRel))?true:false;pp_images=(isSet)?jQuery.map(matchedObjects,function(n,i){if(TTjquery(n).attr('rel').indexOf(theRel)!=-1)return TTjquery(n).attr('href');}):TTjquery.makeArray(TTjquery(this).attr('href'));pp_titles=(isSet)?jQuery.map(matchedObjects,function(n,i){if(TTjquery(n).attr('rel').indexOf(theRel)!=-1)return(TTjquery(n).find('img').attr('alt'))?TTjquery(n).find('img').attr('alt'):"";}):TTjquery.makeArray(TTjquery(this).find('img').attr('alt'));pp_descriptions=(isSet)?jQuery.map(matchedObjects,function(n,i){if(TTjquery(n).attr('rel').indexOf(theRel)!=-1)return(TTjquery(n).attr('title'))?TTjquery(n).attr('title'):"";}):TTjquery.makeArray(TTjquery(this).attr('title'));set_position=jQuery.inArray(TTjquery(this).attr('href'),pp_images);rel_index=(isSet)?set_position:TTjquery("a[rel^='"+theRel+"']").index(TTjquery(this));_build_overlay(this);if(settings.allow_resize)
TTjquery(window).bind('scroll.prettyphoto',function(){_center_overlay();});TTjquery.prettyPhoto.open();return false;}
TTjquery.prettyPhoto.open=function(event){if(typeof settings=="undefined"){settings=pp_settings;if(TTjquery.browser.msie&&TTjquery.browser.version==6)settings.theme="light_square";pp_images=TTjquery.makeArray(arguments[0]);pp_titles=(arguments[1])?TTjquery.makeArray(arguments[1]):TTjquery.makeArray("");pp_descriptions=(arguments[2])?TTjquery.makeArray(arguments[2]):TTjquery.makeArray("");isSet=(pp_images.length>1)?true:false;set_position=0;_build_overlay(event.target);}
if(TTjquery.browser.msie&&TTjquery.browser.version==6)TTjquery('select').css('visibility','hidden');if(settings.hideflash)TTjquery('object,embed,iframe[src*=youtube],iframe[src*=vimeo]').css('visibility','hidden');_checkPosition(TTjquery(pp_images).size());TTjquery('.pp_loaderIcon').show();if(TTjqueryppt.is(':hidden'))TTjqueryppt.css('opacity',0).show();TTjquerypp_overlay.show().fadeTo(settings.animation_speed,settings.opacity);TTjquerypp_pic_holder.find('.currentTextHolder').text((set_position+1)+settings.counter_separator_label+TTjquery(pp_images).size());if(pp_descriptions[set_position]!=""){TTjquerypp_pic_holder.find('.pp_description').show().html(unescape(pp_descriptions[set_position]));}else{TTjquerypp_pic_holder.find('.pp_description').hide();}
movie_width=(parseFloat(getParam('width',pp_images[set_position])))?getParam('width',pp_images[set_position]):settings.default_width.toString();movie_height=(parseFloat(getParam('height',pp_images[set_position])))?getParam('height',pp_images[set_position]):settings.default_height.toString();percentBased=false;if(movie_height.indexOf('%')!=-1){movie_height=parseFloat((TTjquery(window).height()*parseFloat(movie_height)/100)-150);percentBased=true;}
if(movie_width.indexOf('%')!=-1){movie_width=parseFloat((TTjquery(window).width()*parseFloat(movie_width)/100)-150);percentBased=true;}
TTjquerypp_pic_holder.fadeIn(function(){(settings.show_title&&pp_titles[set_position]!=""&&typeof pp_titles[set_position]!="undefined")?TTjqueryppt.html(unescape(pp_titles[set_position])):TTjqueryppt.html('&nbsp;');imgPreloader="";skipInjection=false;switch(_getFileType(pp_images[set_position])){case'image':imgPreloader=new Image();nextImage=new Image();if(isSet&&set_position<TTjquery(pp_images).size()-1)nextImage.src=pp_images[set_position+1];prevImage=new Image();if(isSet&&pp_images[set_position-1])prevImage.src=pp_images[set_position-1];TTjquerypp_pic_holder.find('#pp_full_res')[0].innerHTML=settings.image_markup.replace(/{path}/g,pp_images[set_position]);imgPreloader.onload=function(){pp_dimensions=_fitToViewport(imgPreloader.width,imgPreloader.height);_showContent();};imgPreloader.onerror=function(){alert('Image cannot be loaded. Make sure the path is correct and image exist.');TTjquery.prettyPhoto.close();};imgPreloader.src=pp_images[set_position];break;case'youtube':pp_dimensions=_fitToViewport(movie_width,movie_height);movie='http://www.youtube.com/embed/'+getParam('v',pp_images[set_position]);(getParam('rel',pp_images[set_position]))?movie+="?rel="+getParam('rel',pp_images[set_position]):movie+="?rel=1";if(settings.autoplay)movie+="&autoplay=1";toInject=settings.iframe_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,movie);break;case'vimeo':pp_dimensions=_fitToViewport(movie_width,movie_height);movie_id=pp_images[set_position];var regExp=/http:\/\/(www\.)?vimeo.com\/(\d+)/;var match=movie_id.match(regExp);movie='http://player.vimeo.com/video/'+match[2]+'?title=0&amp;byline=0&amp;portrait=0';if(settings.autoplay)movie+="&autoplay=1;";vimeo_width=pp_dimensions['width']+'/embed/?moog_width='+pp_dimensions['width'];toInject=settings.iframe_markup.replace(/{width}/g,vimeo_width).replace(/{height}/g,pp_dimensions['height']).replace(/{path}/g,movie);break;case'quicktime':pp_dimensions=_fitToViewport(movie_width,movie_height);pp_dimensions['height']+=15;pp_dimensions['contentHeight']+=15;pp_dimensions['containerHeight']+=15;toInject=settings.quicktime_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,pp_images[set_position]).replace(/{autoplay}/g,settings.autoplay);break;case'flash':pp_dimensions=_fitToViewport(movie_width,movie_height);flash_vars=pp_images[set_position];flash_vars=flash_vars.substring(pp_images[set_position].indexOf('flashvars')+10,pp_images[set_position].length);filename=pp_images[set_position];filename=filename.substring(0,filename.indexOf('?'));toInject=settings.flash_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,filename+'?'+flash_vars);break;case'iframe':pp_dimensions=_fitToViewport(movie_width,movie_height);frame_url=pp_images[set_position];frame_url=frame_url.substr(0,frame_url.indexOf('iframe')-1);toInject=settings.iframe_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{path}/g,frame_url);break;case'ajax':doresize=false;pp_dimensions=_fitToViewport(movie_width,movie_height);doresize=true;skipInjection=true;TTjquery.get(pp_images[set_position],function(responseHTML){toInject=settings.inline_markup.replace(/{content}/g,responseHTML);TTjquerypp_pic_holder.find('#pp_full_res')[0].innerHTML=toInject;_showContent();});break;case'custom':pp_dimensions=_fitToViewport(movie_width,movie_height);toInject=settings.custom_markup;break;case'inline':myClone=TTjquery(pp_images[set_position]).clone().append('<br clear="all" />').css({'width':settings.default_width}).wrapInner('<div id="pp_full_res"><div class="pp_inline"></div></div>').appendTo(TTjquery('body')).show();doresize=false;pp_dimensions=_fitToViewport(TTjquery(myClone).width(),TTjquery(myClone).height());doresize=true;TTjquery(myClone).remove();toInject=settings.inline_markup.replace(/{content}/g,TTjquery(pp_images[set_position]).html());break;};if(!imgPreloader&&!skipInjection){TTjquerypp_pic_holder.find('#pp_full_res')[0].innerHTML=toInject;_showContent();};});return false;};TTjquery.prettyPhoto.changePage=function(direction){currentGalleryPage=0;if(direction=='previous'){set_position--;if(set_position<0)set_position=TTjquery(pp_images).size()-1;}else if(direction=='next'){set_position++;if(set_position>TTjquery(pp_images).size()-1)set_position=0;}else{set_position=direction;};rel_index=set_position;if(!doresize)doresize=true;TTjquery('.pp_contract').removeClass('pp_contract').addClass('pp_expand');_hideContent(function(){TTjquery.prettyPhoto.open();});};TTjquery.prettyPhoto.changeGalleryPage=function(direction){if(direction=='next'){currentGalleryPage++;if(currentGalleryPage>totalPage)currentGalleryPage=0;}else if(direction=='previous'){currentGalleryPage--;if(currentGalleryPage<0)currentGalleryPage=totalPage;}else{currentGalleryPage=direction;};slide_speed=(direction=='next'||direction=='previous')?settings.animation_speed:0;slide_to=currentGalleryPage*(itemsPerPage*itemWidth);TTjquerypp_gallery.find('ul').animate({left:-slide_to},slide_speed);};TTjquery.prettyPhoto.startSlideshow=function(){if(typeof pp_slideshow=='undefined'){TTjquerypp_pic_holder.find('.pp_play').unbind('click').removeClass('pp_play').addClass('pp_pause').click(function(){TTjquery.prettyPhoto.stopSlideshow();return false;});pp_slideshow=setInterval(TTjquery.prettyPhoto.startSlideshow,settings.slideshow);}else{TTjquery.prettyPhoto.changePage('next');};}
TTjquery.prettyPhoto.stopSlideshow=function(){TTjquerypp_pic_holder.find('.pp_pause').unbind('click').removeClass('pp_pause').addClass('pp_play').click(function(){TTjquery.prettyPhoto.startSlideshow();return false;});clearInterval(pp_slideshow);pp_slideshow=undefined;}
TTjquery.prettyPhoto.close=function(){if(TTjquerypp_overlay.is(":animated"))return;TTjquery.prettyPhoto.stopSlideshow();TTjquerypp_pic_holder.stop().find('object,embed').css('visibility','hidden');TTjquery('div.pp_pic_holder,div.ppt,.pp_fade').fadeOut(settings.animation_speed,function(){TTjquery(this).remove();});TTjquerypp_overlay.fadeOut(settings.animation_speed,function(){if(TTjquery.browser.msie&&TTjquery.browser.version==6)TTjquery('select').css('visibility','visible');if(settings.hideflash)TTjquery('object,embed,iframe[src*=youtube],iframe[src*=vimeo]').css('visibility','visible');TTjquery(this).remove();TTjquery(window).unbind('scroll.prettyphoto');settings.callback();doresize=true;pp_open=false;delete settings;});};function _showContent(){TTjquery('.pp_loaderIcon').hide();projectedTop=scroll_pos['scrollTop']+((windowHeight/2)-(pp_dimensions['containerHeight']/2));if(projectedTop<0)projectedTop=0;TTjqueryppt.fadeTo(settings.animation_speed,1);TTjquerypp_pic_holder.find('.pp_content').animate({height:pp_dimensions['contentHeight'],width:pp_dimensions['contentWidth']},settings.animation_speed);TTjquerypp_pic_holder.animate({'top':projectedTop,'left':(windowWidth/2)-(pp_dimensions['containerWidth']/2),width:pp_dimensions['containerWidth']},settings.animation_speed,function(){TTjquerypp_pic_holder.find('.pp_hoverContainer,#fullResImage').height(pp_dimensions['height']).width(pp_dimensions['width']);TTjquerypp_pic_holder.find('.pp_fade').fadeIn(settings.animation_speed);if(isSet&&_getFileType(pp_images[set_position])=="image"){TTjquerypp_pic_holder.find('.pp_hoverContainer').show();}else{TTjquerypp_pic_holder.find('.pp_hoverContainer').hide();}
if(pp_dimensions['resized']){TTjquery('a.pp_expand,a.pp_contract').show();}else{TTjquery('a.pp_expand').hide();}
if(settings.autoplay_slideshow&&!pp_slideshow&&!pp_open)TTjquery.prettyPhoto.startSlideshow();if(settings.deeplinking)
setHashtag();settings.changepicturecallback();pp_open=true;});_insert_gallery();};function _hideContent(callback){TTjquerypp_pic_holder.find('#pp_full_res object,#pp_full_res embed').css('visibility','hidden');TTjquerypp_pic_holder.find('.pp_fade').fadeOut(settings.animation_speed,function(){TTjquery('.pp_loaderIcon').show();callback();});};function _checkPosition(setCount){(setCount>1)?TTjquery('.pp_nav').show():TTjquery('.pp_nav').hide();};function _fitToViewport(width,height){resized=false;_getDimensions(width,height);imageWidth=width,imageHeight=height;if(((pp_containerWidth>windowWidth)||(pp_containerHeight>windowHeight))&&doresize&&settings.allow_resize&&!percentBased){resized=true,fitting=false;while(!fitting){if((pp_containerWidth>windowWidth)){imageWidth=(windowWidth-200);imageHeight=(height/width)*imageWidth;}else if((pp_containerHeight>windowHeight)){imageHeight=(windowHeight-200);imageWidth=(width/height)*imageHeight;}else{fitting=true;};pp_containerHeight=imageHeight,pp_containerWidth=imageWidth;};_getDimensions(imageWidth,imageHeight);if((pp_containerWidth>windowWidth)||(pp_containerHeight>windowHeight)){_fitToViewport(pp_containerWidth,pp_containerHeight)};};return{width:Math.floor(imageWidth),height:Math.floor(imageHeight),containerHeight:Math.floor(pp_containerHeight),containerWidth:Math.floor(pp_containerWidth)+(settings.horizontal_padding*2),contentHeight:Math.floor(pp_contentHeight),contentWidth:Math.floor(pp_contentWidth),resized:resized};};function _getDimensions(width,height){width=parseFloat(width);height=parseFloat(height);TTjquerypp_details=TTjquerypp_pic_holder.find('.pp_details');TTjquerypp_details.width(width);detailsHeight=parseFloat(TTjquerypp_details.css('marginTop'))+parseFloat(TTjquerypp_details.css('marginBottom'));TTjquerypp_details=TTjquerypp_details.clone().addClass(settings.theme).width(width).appendTo(TTjquery('body')).css({'position':'absolute','top':-10000});detailsHeight+=TTjquerypp_details.height();detailsHeight=(detailsHeight<=34)?36:detailsHeight;if(TTjquery.browser.msie&&TTjquery.browser.version==7)detailsHeight+=8;TTjquerypp_details.remove();TTjquerypp_title=TTjquerypp_pic_holder.find('.ppt');TTjquerypp_title.width(width);titleHeight=parseFloat(TTjquerypp_title.css('marginTop'))+parseFloat(TTjquerypp_title.css('marginBottom'));TTjquerypp_title=TTjquerypp_title.clone().appendTo(TTjquery('body')).css({'position':'absolute','top':-10000});titleHeight+=TTjquerypp_title.height();TTjquerypp_title.remove();pp_contentHeight=height+detailsHeight;pp_contentWidth=width;pp_containerHeight=pp_contentHeight+titleHeight+TTjquerypp_pic_holder.find('.pp_top').height()+TTjquerypp_pic_holder.find('.pp_bottom').height();pp_containerWidth=width;}
function _getFileType(itemSrc){if(itemSrc.match(/youtube\.com\/watch/i)){return'youtube';}else if(itemSrc.match(/vimeo\.com/i)){return'vimeo';}else if(itemSrc.match(/\b.mov\b/i)){return'quicktime';}else if(itemSrc.match(/\b.swf\b/i)){return'flash';}else if(itemSrc.match(/\biframe=true\b/i)){return'iframe';}else if(itemSrc.match(/\bajax=true\b/i)){return'ajax';}else if(itemSrc.match(/\bcustom=true\b/i)){return'custom';}else if(itemSrc.substr(0,1)=='#'){return'inline';}else{return'image';};};function _center_overlay(){if(doresize&&typeof TTjquerypp_pic_holder!='undefined'){scroll_pos=_get_scroll();contentHeight=TTjquerypp_pic_holder.height(),contentwidth=TTjquerypp_pic_holder.width();projectedTop=(windowHeight/2)+scroll_pos['scrollTop']-(contentHeight/2);if(projectedTop<0)projectedTop=0;if(contentHeight>windowHeight)
return;TTjquerypp_pic_holder.css({'top':projectedTop,'left':(windowWidth/2)+scroll_pos['scrollLeft']-(contentwidth/2)});};};function _get_scroll(){if(self.pageYOffset){return{scrollTop:self.pageYOffset,scrollLeft:self.pageXOffset};}else if(document.documentElement&&document.documentElement.scrollTop){return{scrollTop:document.documentElement.scrollTop,scrollLeft:document.documentElement.scrollLeft};}else if(document.body){return{scrollTop:document.body.scrollTop,scrollLeft:document.body.scrollLeft};};};function _resize_overlay(){windowHeight=TTjquery(window).height(),windowWidth=TTjquery(window).width();if(typeof TTjquerypp_overlay!="undefined")TTjquerypp_overlay.height(TTjquery(document).height()).width(windowWidth);};function _insert_gallery(){if(isSet&&settings.overlay_gallery&&_getFileType(pp_images[set_position])=="image"&&(settings.ie6_fallback&&!(TTjquery.browser.msie&&parseInt(TTjquery.browser.version)==6))){itemWidth=52+5;navWidth=(settings.theme=="facebook"||settings.theme=="pp_default")?50:30;itemsPerPage=Math.floor((pp_dimensions['containerWidth']-100-navWidth)/itemWidth);itemsPerPage=(itemsPerPage<pp_images.length)?itemsPerPage:pp_images.length;totalPage=Math.ceil(pp_images.length/itemsPerPage)-1;if(totalPage==0){navWidth=0;TTjquerypp_gallery.find('.pp_arrow_next,.pp_arrow_previous').hide();}else{TTjquerypp_gallery.find('.pp_arrow_next,.pp_arrow_previous').show();};galleryWidth=itemsPerPage*itemWidth;fullGalleryWidth=pp_images.length*itemWidth;TTjquerypp_gallery.css('margin-left',-((galleryWidth/2)+(navWidth/2))).find('div:first').width(galleryWidth+5).find('ul').width(fullGalleryWidth).find('li.selected').removeClass('selected');goToPage=(Math.floor(set_position/itemsPerPage)<totalPage)?Math.floor(set_position/itemsPerPage):totalPage;TTjquery.prettyPhoto.changeGalleryPage(goToPage);TTjquerypp_gallery_li.filter(':eq('+set_position+')').addClass('selected');}else{TTjquerypp_pic_holder.find('.pp_content').unbind('mouseenter mouseleave');}}
function _build_overlay(caller){settings.markup=settings.markup.replace('{pp_social}',(settings.social_tools)?settings.social_tools:'');TTjquery('body').append(settings.markup);TTjquerypp_pic_holder=TTjquery('.pp_pic_holder'),TTjqueryppt=TTjquery('.ppt'),TTjquerypp_overlay=TTjquery('div.pp_overlay');if(isSet&&settings.overlay_gallery){currentGalleryPage=0;toInject="";for(var i=0;i<pp_images.length;i++){if(!pp_images[i].match(/\b(jpg|jpeg|png|gif)\b/gi)){classname='default';img_src='';}else{classname='';img_src=pp_images[i];}
toInject+="<li class='"+classname+"'><a href='#'><img src='"+img_src+"' width='50' alt='' /></a></li>";};toInject=settings.gallery_markup.replace(/{gallery}/g,toInject);TTjquerypp_pic_holder.find('#pp_full_res').after(toInject);TTjquerypp_gallery=TTjquery('.pp_pic_holder .pp_gallery'),TTjquerypp_gallery_li=TTjquerypp_gallery.find('li');TTjquerypp_gallery.find('.pp_arrow_next').click(function(){TTjquery.prettyPhoto.changeGalleryPage('next');TTjquery.prettyPhoto.stopSlideshow();return false;});TTjquerypp_gallery.find('.pp_arrow_previous').click(function(){TTjquery.prettyPhoto.changeGalleryPage('previous');TTjquery.prettyPhoto.stopSlideshow();return false;});TTjquerypp_pic_holder.find('.pp_content').hover(function(){TTjquerypp_pic_holder.find('.pp_gallery:not(.disabled)').fadeIn();},function(){TTjquerypp_pic_holder.find('.pp_gallery:not(.disabled)').fadeOut();});itemWidth=52+5;TTjquerypp_gallery_li.each(function(i){TTjquery(this).find('a').click(function(){TTjquery.prettyPhoto.changePage(i);TTjquery.prettyPhoto.stopSlideshow();return false;});});};if(settings.slideshow){TTjquerypp_pic_holder.find('.pp_nav').prepend('<a href="#" class="pp_play">Play</a>')
TTjquerypp_pic_holder.find('.pp_nav .pp_play').click(function(){TTjquery.prettyPhoto.startSlideshow();return false;});}
TTjquerypp_pic_holder.attr('class','pp_pic_holder '+settings.theme);TTjquerypp_overlay.css({'opacity':0,'height':TTjquery(document).height(),'width':TTjquery(window).width()}).bind('click',function(){if(!settings.modal)TTjquery.prettyPhoto.close();});TTjquery('a.pp_close').bind('click',function(){TTjquery.prettyPhoto.close();return false;});TTjquery('a.pp_expand').bind('click',function(e){if(TTjquery(this).hasClass('pp_expand')){TTjquery(this).removeClass('pp_expand').addClass('pp_contract');doresize=false;}else{TTjquery(this).removeClass('pp_contract').addClass('pp_expand');doresize=true;};_hideContent(function(){TTjquery.prettyPhoto.open();});return false;});TTjquerypp_pic_holder.find('.pp_previous, .pp_nav .pp_arrow_previous').bind('click',function(){TTjquery.prettyPhoto.changePage('previous');TTjquery.prettyPhoto.stopSlideshow();return false;});TTjquerypp_pic_holder.find('.pp_next, .pp_nav .pp_arrow_next').bind('click',function(){TTjquery.prettyPhoto.changePage('next');TTjquery.prettyPhoto.stopSlideshow();return false;});_center_overlay();};if(!pp_alreadyInitialized&&getHashtag()){pp_alreadyInitialized=true;hashIndex=getHashtag();hashRel=hashIndex;hashIndex=hashIndex.substring(hashIndex.indexOf('/')+1,hashIndex.length-1);hashRel=hashRel.substring(0,hashRel.indexOf('/'));setTimeout(function(){TTjquery("a[rel^='"+hashRel+"']:eq("+hashIndex+")").trigger('click');},50);}
return this.unbind('click.prettyphoto').bind('click.prettyphoto',TTjquery.prettyPhoto.initialize);};function getHashtag(){url=location.href;hashtag=(url.indexOf('#!')!=-1)?decodeURI(url.substring(url.indexOf('#!')+2,url.length)):false;return hashtag;};function setHashtag(){if(typeof theRel=='undefined')return;location.hash='!'+theRel+'/'+rel_index+'/';};function getParam(name,url){name=name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");var regexS="[\\?&]"+name+"=([^&#]*)";var regex=new RegExp(regexS);var results=regex.exec(url);return(results==null)?"":results[1];}})(jQuery);var pp_alreadyInitialized=false;
/*
 * TTjquery UI 1.7.2
 *
 * Copyright (c) 2009 AUTHORS.txt (http://TTjqueryui.com/about)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://docs.TTjquery.com/UI
 */
TTjquery.ui||(function(c){var i=c.fn.remove,d=c.browser.mozilla&&(parseFloat(c.browser.version)<1.9);c.ui={version:"1.7.2",plugin:{add:function(k,l,n){var m=c.ui[k].prototype;for(var j in n){m.plugins[j]=m.plugins[j]||[];m.plugins[j].push([l,n[j]])}},call:function(j,l,k){var n=j.plugins[l];if(!n||!j.element[0].parentNode){return}for(var m=0;m<n.length;m++){if(j.options[n[m][0]]){n[m][1].apply(j.element,k)}}}},contains:function(k,j){return document.compareDocumentPosition?k.compareDocumentPosition(j)&16:k!==j&&k.contains(j)},hasScroll:function(m,k){if(c(m).css("overflow")=="hidden"){return false}var j=(k&&k=="left")?"scrollLeft":"scrollTop",l=false;if(m[j]>0){return true}m[j]=1;l=(m[j]>0);m[j]=0;return l},isOverAxis:function(k,j,l){return(k>j)&&(k<(j+l))},isOver:function(o,k,n,m,j,l){return c.ui.isOverAxis(o,n,j)&&c.ui.isOverAxis(k,m,l)},keyCode:{BACKSPACE:8,CAPS_LOCK:20,COMMA:188,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38}};if(d){var f=c.attr,e=c.fn.removeAttr,h="http://www.w3.org/2005/07/aaa",a=/^aria-/,b=/^wairole:/;c.attr=function(k,j,l){var m=l!==undefined;return(j=="role"?(m?f.call(this,k,j,"wairole:"+l):(f.apply(this,arguments)||"").replace(b,"")):(a.test(j)?(m?k.setAttributeNS(h,j.replace(a,"aaa:"),l):f.call(this,k,j.replace(a,"aaa:"))):f.apply(this,arguments)))};c.fn.removeAttr=function(j){return(a.test(j)?this.each(function(){this.removeAttributeNS(h,j.replace(a,""))}):e.call(this,j))}}c.fn.extend({remove:function(){c("*",this).add(this).each(function(){c(this).triggerHandler("remove")});return i.apply(this,arguments)},enableSelection:function(){return this.attr("unselectable","off").css("MozUserSelect","").unbind("selectstart.ui")},disableSelection:function(){return this.attr("unselectable","on").css("MozUserSelect","none").bind("selectstart.ui",function(){return false})},scrollParent:function(){var j;if((c.browser.msie&&(/(static|relative)/).test(this.css("position")))||(/absolute/).test(this.css("position"))){j=this.parents().filter(function(){return(/(relative|absolute|fixed)/).test(c.curCSS(this,"position",1))&&(/(auto|scroll)/).test(c.curCSS(this,"overflow",1)+c.curCSS(this,"overflow-y",1)+c.curCSS(this,"overflow-x",1))}).eq(0)}else{j=this.parents().filter(function(){return(/(auto|scroll)/).test(c.curCSS(this,"overflow",1)+c.curCSS(this,"overflow-y",1)+c.curCSS(this,"overflow-x",1))}).eq(0)}return(/fixed/).test(this.css("position"))||!j.length?c(document):j}});c.extend(c.expr[":"],{data:function(l,k,j){return !!c.data(l,j[3])},focusable:function(k){var l=k.nodeName.toLowerCase(),j=c.attr(k,"tabindex");return(/input|select|textarea|button|object/.test(l)?!k.disabled:"a"==l||"area"==l?k.href||!isNaN(j):!isNaN(j))&&!c(k)["area"==l?"parents":"closest"](":hidden").length},tabbable:function(k){var j=c.attr(k,"tabindex");return(isNaN(j)||j>=0)&&c(k).is(":focusable")}});function g(m,n,o,l){function k(q){var p=c[m][n][q]||[];return(typeof p=="string"?p.split(/,?\s+/):p)}var j=k("getter");if(l.length==1&&typeof l[0]=="string"){j=j.concat(k("getterSetter"))}return(c.inArray(o,j)!=-1)}c.widget=function(k,j){var l=k.split(".")[0];k=k.split(".")[1];c.fn[k]=function(p){var n=(typeof p=="string"),o=Array.prototype.slice.call(arguments,1);if(n&&p.substring(0,1)=="_"){return this}if(n&&g(l,k,p,o)){var m=c.data(this[0],k);return(m?m[p].apply(m,o):undefined)}return this.each(function(){var q=c.data(this,k);(!q&&!n&&c.data(this,k,new c[l][k](this,p))._init());(q&&n&&c.isFunction(q[p])&&q[p].apply(q,o))})};c[l]=c[l]||{};c[l][k]=function(o,n){var m=this;this.namespace=l;this.widgetName=k;this.widgetEventPrefix=c[l][k].eventPrefix||k;this.widgetBaseClass=l+"-"+k;this.options=c.extend({},c.widget.defaults,c[l][k].defaults,c.metadata&&c.metadata.get(o)[k],n);this.element=c(o).bind("setData."+k,function(q,p,r){if(q.target==o){return m._setData(p,r)}}).bind("getData."+k,function(q,p){if(q.target==o){return m._getData(p)}}).bind("remove",function(){return m.destroy()})};c[l][k].prototype=c.extend({},c.widget.prototype,j);c[l][k].getterSetter="option"};c.widget.prototype={_init:function(){},destroy:function(){this.element.removeData(this.widgetName).removeClass(this.widgetBaseClass+"-disabled "+this.namespace+"-state-disabled").removeAttr("aria-disabled")},option:function(l,m){var k=l,j=this;if(typeof l=="string"){if(m===undefined){return this._getData(l)}k={};k[l]=m}c.each(k,function(n,o){j._setData(n,o)})},_getData:function(j){return this.options[j]},_setData:function(j,k){this.options[j]=k;if(j=="disabled"){this.element[k?"addClass":"removeClass"](this.widgetBaseClass+"-disabled "+this.namespace+"-state-disabled").attr("aria-disabled",k)}},enable:function(){this._setData("disabled",false)},disable:function(){this._setData("disabled",true)},_trigger:function(l,m,n){var p=this.options[l],j=(l==this.widgetEventPrefix?l:this.widgetEventPrefix+l);m=c.Event(m);m.type=j;if(m.originalEvent){for(var k=c.event.props.length,o;k;){o=c.event.props[--k];m[o]=m.originalEvent[o]}}this.element.trigger(m,n);return !(c.isFunction(p)&&p.call(this.element[0],m,n)===false||m.isDefaultPrevented())}};c.widget.defaults={disabled:false};c.ui.mouse={_mouseInit:function(){var j=this;this.element.bind("mousedown."+this.widgetName,function(k){return j._mouseDown(k)}).bind("click."+this.widgetName,function(k){if(j._preventClickEvent){j._preventClickEvent=false;k.stopImmediatePropagation();return false}});if(c.browser.msie){this._mouseUnselectable=this.element.attr("unselectable");this.element.attr("unselectable","on")}this.started=false},_mouseDestroy:function(){this.element.unbind("."+this.widgetName);(c.browser.msie&&this.element.attr("unselectable",this._mouseUnselectable))},_mouseDown:function(l){l.originalEvent=l.originalEvent||{};if(l.originalEvent.mouseHandled){return}(this._mouseStarted&&this._mouseUp(l));this._mouseDownEvent=l;var k=this,m=(l.which==1),j=(typeof this.options.cancel=="string"?c(l.target).parents().add(l.target).filter(this.options.cancel).length:false);if(!m||j||!this._mouseCapture(l)){return true}this.mouseDelayMet=!this.options.delay;if(!this.mouseDelayMet){this._mouseDelayTimer=setTimeout(function(){k.mouseDelayMet=true},this.options.delay)}if(this._mouseDistanceMet(l)&&this._mouseDelayMet(l)){this._mouseStarted=(this._mouseStart(l)!==false);if(!this._mouseStarted){l.preventDefault();return true}}this._mouseMoveDelegate=function(n){return k._mouseMove(n)};this._mouseUpDelegate=function(n){return k._mouseUp(n)};c(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate);(c.browser.safari||l.preventDefault());l.originalEvent.mouseHandled=true;return true},_mouseMove:function(j){if(c.browser.msie&&!j.button){return this._mouseUp(j)}if(this._mouseStarted){this._mouseDrag(j);return j.preventDefault()}if(this._mouseDistanceMet(j)&&this._mouseDelayMet(j)){this._mouseStarted=(this._mouseStart(this._mouseDownEvent,j)!==false);(this._mouseStarted?this._mouseDrag(j):this._mouseUp(j))}return !this._mouseStarted},_mouseUp:function(j){c(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate);if(this._mouseStarted){this._mouseStarted=false;this._preventClickEvent=(j.target==this._mouseDownEvent.target);this._mouseStop(j)}return false},_mouseDistanceMet:function(j){return(Math.max(Math.abs(this._mouseDownEvent.pageX-j.pageX),Math.abs(this._mouseDownEvent.pageY-j.pageY))>=this.options.distance)},_mouseDelayMet:function(j){return this.mouseDelayMet},_mouseStart:function(j){},_mouseDrag:function(j){},_mouseStop:function(j){},_mouseCapture:function(j){return true}};c.ui.mouse.defaults={cancel:null,distance:1,delay:0}})(TTjquery);;/*
 * jQuery UI Accordion 1.7.2
 *
 * Copyright (c) 2009 AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://docs.jquery.com/UI/Accordion
 *
 * Depends:
 *	ui.core.js
 */
(function(a){a.widget("ui.accordion",{_init:function(){var d=this.options,b=this;this.running=0;if(d.collapsible==a.ui.accordion.defaults.collapsible&&d.alwaysOpen!=a.ui.accordion.defaults.alwaysOpen){d.collapsible=!d.alwaysOpen}if(d.navigation){var c=this.element.find("a").filter(d.navigationFilter);if(c.length){if(c.filter(d.header).length){this.active=c}else{this.active=c.parent().parent().prev();c.addClass("ui-accordion-content-active")}}}this.element.addClass("ui-accordion ui-widget ui-helper-reset");if(this.element[0].nodeName=="UL"){this.element.children("li").addClass("ui-accordion-li-fix")}this.headers=this.element.find(d.header).addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all").bind("mouseenter.accordion",function(){a(this).addClass("ui-state-hover")}).bind("mouseleave.accordion",function(){a(this).removeClass("ui-state-hover")}).bind("focus.accordion",function(){a(this).addClass("ui-state-focus")}).bind("blur.accordion",function(){a(this).removeClass("ui-state-focus")});this.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom");this.active=this._findActive(this.active||d.active).toggleClass("ui-state-default").toggleClass("ui-state-active").toggleClass("ui-corner-all").toggleClass("ui-corner-top");this.active.next().addClass("ui-accordion-content-active");a("<span/>").addClass("ui-icon "+d.icons.header).prependTo(this.headers);this.active.find(".ui-icon").toggleClass(d.icons.header).toggleClass(d.icons.headerSelected);if(a.browser.msie){this.element.find("a").css("zoom","1")}this.resize();this.element.attr("role","tablist");this.headers.attr("role","tab").bind("keydown",function(e){return b._keydown(e)}).next().attr("role","tabpanel");this.headers.not(this.active||"").attr("aria-expanded","false").attr("tabIndex","-1").next().hide();if(!this.active.length){this.headers.eq(0).attr("tabIndex","0")}else{this.active.attr("aria-expanded","true").attr("tabIndex","0")}if(!a.browser.safari){this.headers.find("a").attr("tabIndex","-1")}if(d.event){this.headers.bind((d.event)+".accordion",function(e){return b._clickHandler.call(b,e,this)})}},destroy:function(){var c=this.options;this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role").unbind(".accordion").removeData("accordion");this.headers.unbind(".accordion").removeClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-corner-top").removeAttr("role").removeAttr("aria-expanded").removeAttr("tabindex");this.headers.find("a").removeAttr("tabindex");this.headers.children(".ui-icon").remove();var b=this.headers.next().css("display","").removeAttr("role").removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active");if(c.autoHeight||c.fillHeight){b.css("height","")}},_setData:function(b,c){if(b=="alwaysOpen"){b="collapsible";c=!c}a.widget.prototype._setData.apply(this,arguments)},_keydown:function(e){var g=this.options,f=a.ui.keyCode;if(g.disabled||e.altKey||e.ctrlKey){return}var d=this.headers.length;var b=this.headers.index(e.target);var c=false;switch(e.keyCode){case f.RIGHT:case f.DOWN:c=this.headers[(b+1)%d];break;case f.LEFT:case f.UP:c=this.headers[(b-1+d)%d];break;case f.SPACE:case f.ENTER:return this._clickHandler({target:e.target},e.target)}if(c){a(e.target).attr("tabIndex","-1");a(c).attr("tabIndex","0");c.focus();return false}return true},resize:function(){var e=this.options,d;if(e.fillSpace){if(a.browser.msie){var b=this.element.parent().css("overflow");this.element.parent().css("overflow","hidden")}d=this.element.parent().height();if(a.browser.msie){this.element.parent().css("overflow",b)}this.headers.each(function(){d-=a(this).outerHeight()});var c=0;this.headers.next().each(function(){c=Math.max(c,a(this).innerHeight()-a(this).height())}).height(Math.max(0,d-c)).css("overflow","auto")}else{if(e.autoHeight){d=0;this.headers.next().each(function(){d=Math.max(d,a(this).outerHeight())}).height(d)}}},activate:function(b){var c=this._findActive(b)[0];this._clickHandler({target:c},c)},_findActive:function(b){return b?typeof b=="number"?this.headers.filter(":eq("+b+")"):this.headers.not(this.headers.not(b)):b===false?a([]):this.headers.filter(":eq(0)")},_clickHandler:function(b,f){var d=this.options;if(d.disabled){return false}if(!b.target&&d.collapsible){this.active.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").find(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header);this.active.next().addClass("ui-accordion-content-active");var h=this.active.next(),e={options:d,newHeader:a([]),oldHeader:d.active,newContent:a([]),oldContent:h},c=(this.active=a([]));this._toggle(c,h,e);return false}var g=a(b.currentTarget||f);var i=g[0]==this.active[0];if(this.running||(!d.collapsible&&i)){return false}this.active.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").find(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header);this.active.next().addClass("ui-accordion-content-active");if(!i){g.removeClass("ui-state-default ui-corner-all").addClass("ui-state-active ui-corner-top").find(".ui-icon").removeClass(d.icons.header).addClass(d.icons.headerSelected);g.next().addClass("ui-accordion-content-active")}var c=g.next(),h=this.active.next(),e={options:d,newHeader:i&&d.collapsible?a([]):g,oldHeader:this.active,newContent:i&&d.collapsible?a([]):c.find("> *"),oldContent:h.find("> *")},j=this.headers.index(this.active[0])>this.headers.index(g[0]);this.active=i?a([]):g;this._toggle(c,h,e,i,j);return false},_toggle:function(b,i,g,j,k){var d=this.options,m=this;this.toShow=b;this.toHide=i;this.data=g;var c=function(){if(!m){return}return m._completed.apply(m,arguments)};this._trigger("changestart",null,this.data);this.running=i.size()===0?b.size():i.size();if(d.animated){var f={};if(d.collapsible&&j){f={toShow:a([]),toHide:i,complete:c,down:k,autoHeight:d.autoHeight||d.fillSpace}}else{f={toShow:b,toHide:i,complete:c,down:k,autoHeight:d.autoHeight||d.fillSpace}}if(!d.proxied){d.proxied=d.animated}if(!d.proxiedDuration){d.proxiedDuration=d.duration}d.animated=a.isFunction(d.proxied)?d.proxied(f):d.proxied;d.duration=a.isFunction(d.proxiedDuration)?d.proxiedDuration(f):d.proxiedDuration;var l=a.ui.accordion.animations,e=d.duration,h=d.animated;if(!l[h]){l[h]=function(n){this.slide(n,{easing:h,duration:e||700})}}l[h](f)}else{if(d.collapsible&&j){b.toggle()}else{i.hide();b.show()}c(true)}i.prev().attr("aria-expanded","false").attr("tabIndex","-1").blur();b.prev().attr("aria-expanded","true").attr("tabIndex","0").focus()},_completed:function(b){var c=this.options;this.running=b?0:--this.running;if(this.running){return}if(c.clearStyle){this.toShow.add(this.toHide).css({height:"",overflow:""})}this._trigger("change",null,this.data)}});a.extend(a.ui.accordion,{version:"1.7.2",defaults:{active:null,alwaysOpen:true,animated:"slide",autoHeight:true,clearStyle:false,collapsible:false,event:"click",fillSpace:false,header:"> li > :first-child,> :not(li):even",icons:{header:"ui-icon-triangle-1-e",headerSelected:"ui-icon-triangle-1-s"},navigation:false,navigationFilter:function(){return this.href.toLowerCase()==location.href.toLowerCase()}},animations:{slide:function(j,h){j=a.extend({easing:"swing",duration:300},j,h);if(!j.toHide.size()){j.toShow.animate({height:"show"},j);return}if(!j.toShow.size()){j.toHide.animate({height:"hide"},j);return}var c=j.toShow.css("overflow"),g,d={},f={},e=["height","paddingTop","paddingBottom"],b;var i=j.toShow;b=i[0].style.width;i.width(parseInt(i.parent().width(),10)-parseInt(i.css("paddingLeft"),10)-parseInt(i.css("paddingRight"),10)-(parseInt(i.css("borderLeftWidth"),10)||0)-(parseInt(i.css("borderRightWidth"),10)||0));a.each(e,function(k,m){f[m]="hide";var l=(""+a.css(j.toShow[0],m)).match(/^([\d+-.]+)(.*)$/);d[m]={value:l[1],unit:l[2]||"px"}});j.toShow.css({height:0,overflow:"hidden"}).show();j.toHide.filter(":hidden").each(j.complete).end().filter(":visible").animate(f,{step:function(k,l){if(l.prop=="height"){g=(l.now-l.start)/(l.end-l.start)}j.toShow[0].style[l.prop]=(g*d[l.prop].value)+d[l.prop].unit},duration:j.duration,easing:j.easing,complete:function(){if(!j.autoHeight){j.toShow.css("height","")}j.toShow.css("width",b);j.toShow.css({overflow:c});j.complete()}})},bounceslide:function(b){this.slide(b,{easing:b.down?"easeOutBounce":"swing",duration:b.down?1000:200})},easeslide:function(b){this.slide(b,{easing:"easeinout",duration:700})}}})})(jQuery);;


TTjquery(document).ready(function(){
TTjquery("a[rel^='prettyPhoto']").prettyPhoto();
if (TTjquery.browser.msie || TTjquery.browser.opera) { TTjquery(window).load(function() {TTjquery('.big-banner #main .main-area').css("padding-top", "118px");}); } else {}
});
