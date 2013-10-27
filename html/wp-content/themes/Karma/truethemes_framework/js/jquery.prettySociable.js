/* ------------------------------------------------------------------------
Please note - this has been modified from it's original version to be HTML5 compatible
 * ------------------------------------------------------------------------- */
 

/* ------------------------------------------------------------------------
 * prettySociable plugin.
 * Version: 1.2.1
 * Description: Include this plugin in your webpage and let people
 * share your content like never before.
 * Website: http://no-margin-for-errors.com/projects/prettySociable/
 * 						
 * Thank You: 
 * Chris Wallace, for the nice icons
 * http://www.chris-wallace.com/2009/05/28/free-social-media-icons-socialize/
 * ------------------------------------------------------------------------- */
 
(function(jQuery){jQuery.prettySociable={version:1.21};jQuery.prettySociable=function(settings){jQuery.prettySociable.settings=jQuery.extend({animationSpeed:'fast',opacity:0.80,share_label:'Drag to share',label_position:'top',share_on_label:'Share on ',hideflash:false,hover_padding:0,websites:{facebook:{'active':true,'encode':true,'title':'Facebook','url':'http://www.facebook.com/share.php?u=','icon':'https://s3.amazonaws.com/Karma-WP/facebook.png','sizes':{'width':70,'height':70}},twitter:{'active':true,'encode':true,'title':'Twitter','url':'http://twitter.com/home?status=','icon':'https://s3.amazonaws.com/Karma-WP/twitter.png','sizes':{'width':70,'height':70}},delicious:{'active':true,'encode':true,'title':'Delicious','url':'http://del.icio.us/post?url=','icon':'https://s3.amazonaws.com/Karma-WP/delicious.png','sizes':{'width':70,'height':70}},digg:{'active':true,'encode':true,'title':'Digg','url':'http://digg.com/submit?phase=2&url=','icon':'https://s3.amazonaws.com/Karma-WP/digg.png','sizes':{'width':70,'height':70}},linkedin:{'active':false,'encode':true,'title':'LinkedIn','url':'http://www.linkedin.com/shareArticle?mini=true&ro=true&url=','icon':'http://','sizes':{'width':70,'height':70}},reddit:{'active':false,'encode':true,'title':'Reddit','url':'http://reddit.com/submit?url=','icon':'http://files.truethemes.net/themes/karma-wp/reddit.png','sizes':{'width':70,'height':70}},stumbleupon:{'active':false,'encode':false,'title':'StumbleUpon','url':'http://stumbleupon.com/submit?url=','icon':'http://','sizes':{'width':70,'height':70}},tumblr:{'active':false,'encode':true,'title':'tumblr','url':'http://www.tumblr.com/share?v=3&u=','icon':'http://','sizes':{'width':70,'height':70}}},urlshortener:{bitly:{'active':false}},tooltip:{offsetTop:0,offsetLeft:15},popup:{width:900,height:500},callback:function(){}},settings);var websites,settings=jQuery.prettySociable.settings,show_timer,ps_hover;jQuery.each(settings.websites,function(i){var preload=new Image();preload.src=this.icon;});jQuery('a[data-gal^=prettySociable]').hover(function(){_self=this;_container=this;if(jQuery(_self).find('img').size()>0){_self=jQuery(_self).find('img');}else if(jQuery.browser.msie){if(jQuery(_self).find('embed').size()>0){_self=jQuery(_self).find('embed');jQuery(_self).css({'display':'block'});}}else{if(jQuery(_self).find('object').size()>0){_self=jQuery(_self).find('object');jQuery(_self).css({'display':'block'});}}
jQuery(_self).css({'cursor':'move','position':'relative','z-index':1005});offsetLeft=(parseFloat(jQuery(_self).css('borderLeftWidth')))?parseFloat(jQuery(_self).css('borderLeftWidth')):0;offsetTop=(parseFloat(jQuery(_self).css('borderTopWidth')))?parseFloat(jQuery(_self).css('borderTopWidth')):0;offsetLeft+=(parseFloat(jQuery(_self).css('paddingLeft')))?parseFloat(jQuery(_self).css('paddingLeft')):0;offsetTop+=(parseFloat(jQuery(_self).css('paddingTop')))?parseFloat(jQuery(_self).css('paddingTop')):0;ps_hover=jQuery('<div id="ps_hover"> \
        <div class="ps_hd"> \
         <div class="ps_c"></div> \
        </div> \
        <div class="ps_bd"> \
         <div class="ps_c"> \
          <div class="ps_s"> \
          </div> \
         </div> \
        </div> \
        <div class="ps_ft"> \
         <div class="ps_c"></div> \
        </div> \
        <div id="ps_title"> \
         <div class="ps_tt_l"> \
          '+settings.share_label+' \
         </div> \
        </div> \
       </div>').css({'width':jQuery(_self).width()+(settings.hover_padding+8)*2,'top':jQuery(_self).position().top-settings.hover_padding-8+parseFloat(jQuery(_self).css('marginTop'))+offsetTop,'left':jQuery(_self).position().left-settings.hover_padding-8+parseFloat(jQuery(_self).css('marginLeft'))+offsetLeft}).hide().insertAfter(_container).fadeIn(settings.animationSpeed);jQuery('#ps_title').animate({top:-15},settings.animationSpeed);jQuery(ps_hover).find('>.ps_bd .ps_s').height(jQuery(_self).height()+settings.hover_padding*2);fixCrappyBrowser('ps_hover',this);DragHandler.attach(jQuery(this)[0]);jQuery(this)[0].dragBegin=function(e){_self=this;show_timer=window.setTimeout(function(){jQuery('object,embed').css('visibility','hidden');jQuery(_self).animate({'opacity':0},settings.animationSpeed);jQuery(ps_hover).remove();overlay.show();tooltip.show(_self);tooltip.follow(e.mouseX,e.mouseY);sharing.show();},200);};jQuery(this)[0].drag=function(e){tooltip.follow(e.mouseX,e.mouseY);}
jQuery(this)[0].dragEnd=function(element,x,y){jQuery('object,embed').css('visibility','visible');jQuery(this).attr('style',0);overlay.hide();tooltip.checkCollision(element.mouseX,element.mouseY);};},function(){jQuery(ps_hover).fadeOut(settings.animationSpeed,function(){jQuery(this).remove()});}).click(function(){clearTimeout(show_timer);});var tooltip={show:function(caller){tooltip.link_to_share=(jQuery(caller).attr('href')!="#")?jQuery(caller).attr('href'):location.href;if(settings.urlshortener.bitly.active){if(window.BitlyCB){BitlyCB.myShortenCallback=function(data){var result;for(var r in data.results){result=data.results[r];result['longUrl']=r;break;};tooltip.link_to_share=result['shortUrl'];};BitlyClient.shorten(tooltip.link_to_share,'BitlyCB.myShortenCallback');};};attributes=jQuery(caller).attr('data-gal').split(';');for(var i=1;i<attributes.length;i++){attributes[i]=attributes[i].split(':');};desc=(jQuery('meta[name=Description]').attr('content'))?jQuery('meta[name=Description]').attr('content'):"";if(attributes.length==1){attributes[1]=['title',document.title];attributes[2]=['excerpt',desc];}
ps_tooltip=jQuery('<div id="ps_tooltip"> \
         <div class="ps_hd"> \
          <div class="ps_c"></div> \
         </div> \
         <div class="ps_bd"> \
          <div class="ps_c"> \
           <div class="ps_s"> \
           </div> \
          </div> \
         </div> \
         <div class="ps_ft"> \
          <div class="ps_c"></div> \
         </div> \
            </div>').appendTo('body');jQuery(ps_tooltip).find('.ps_s').html("<p><strong>"+attributes[1][1]+"</strong><br />"+attributes[2][1]+"</p>");fixCrappyBrowser('ps_tooltip');},checkCollision:function(x,y){collision="";scrollPos=_getScroll();jQuery.each(websites,function(i){if((x+scrollPos.scrollLeft>jQuery(this).offset().left&&x+scrollPos.scrollLeft<jQuery(this).offset().left+jQuery(this).width())&&(y+scrollPos.scrollTop>jQuery(this).offset().top&&y+scrollPos.scrollTop<jQuery(this).offset().top+jQuery(this).height())){collision=jQuery(this).find('a');}});if(collision!=""){jQuery(collision).click();}
sharing.hide();jQuery('#ps_tooltip').remove();},follow:function(x,y){scrollPos=_getScroll();settings.tooltip.offsetTop=(settings.tooltip.offsetTop)?settings.tooltip.offsetTop:0;settings.tooltip.offsetLeft=(settings.tooltip.offsetLeft)?settings.tooltip.offsetLeft:0;jQuery('#ps_tooltip').css({'top':y+settings.tooltip.offsetTop+scrollPos.scrollTop,'left':x+settings.tooltip.offsetLeft+scrollPos.scrollLeft});}}
var sharing={show:function(){websites_container=jQuery('<ul />');jQuery.each(settings.websites,function(i){var _self=this;if(_self.active){link=jQuery('<a />').attr({'href':'#'}).html('<img src="'+_self.icon+'" alt="'+_self.title+'" width="'+_self.sizes.width+'" height="'+_self.sizes.height+'" />').hover(function(){sharing.showTitle(_self.title,jQuery(this).width(),jQuery(this).position().left,jQuery(this).height(),jQuery(this).position().top);},function(){sharing.hideTitle();}).click(function(){shareURL=(_self.encode)?encodeURIComponent(tooltip.link_to_share):tooltip.link_to_share;popup=window.open(_self.url+shareURL,"prettySociable","location=0,status=0,scrollbars=1,width="+settings.popup.width+",height="+settings.popup.height);});jQuery('<li>').append(link).appendTo(websites_container);};});jQuery('<div id="ps_websites"><p class="ps_label"></p></div>').append(websites_container).appendTo('body');fixCrappyBrowser('ps_websites');scrollPos=_getScroll();jQuery('#ps_websites').css({'top':jQuery(window).height()/2-jQuery('#ps_websites').height()/2+scrollPos.scrollTop,'left':jQuery(window).width()/2-jQuery('#ps_websites').width()/2+scrollPos.scrollLeft});websites=jQuery.makeArray(jQuery('#ps_websites li'));},hide:function(){jQuery('#ps_websites').fadeOut(settings.animationSpeed,function(){jQuery(this).remove()});},showTitle:function(title,width,left,height,top){jQuerylabel=jQuery('#ps_websites .ps_label');jQuerylabel.text(settings.share_on_label+title)
jQuerylabel.css({'left':left-jQuerylabel.width()/2+width/2,'opacity':0,'display':'block'}).stop().animate({'opacity':1,'top':top-height+45},settings.animationSpeed);},hideTitle:function(){jQuery('#ps_websites .ps_label').stop().animate({'opacity':0,'top':10},settings.animationSpeed);}};var overlay={show:function(){jQuery('<div id="ps_overlay" />').css('opacity',0).appendTo('body').height(jQuery(document).height()).fadeTo(settings.animationSpeed,settings.opacity);},hide:function(){jQuery('#ps_overlay').fadeOut(settings.animationSpeed,function(){jQuery(this).remove();});}}
var DragHandler={_oElem:null,attach:function(oElem){oElem.onmousedown=DragHandler._dragBegin;oElem.dragBegin=new Function();oElem.drag=new Function();oElem.dragEnd=new Function();return oElem;},_dragBegin:function(e){var oElem=DragHandler._oElem=this;if(isNaN(parseInt(oElem.style.left))){oElem.style.left='0px';}
if(isNaN(parseInt(oElem.style.top))){oElem.style.top='0px';}
var x=parseInt(oElem.style.left);var y=parseInt(oElem.style.top);e=e?e:window.event;oElem.mouseX=e.clientX;oElem.mouseY=e.clientY;oElem.dragBegin(oElem,x,y);document.onmousemove=DragHandler._drag;document.onmouseup=DragHandler._dragEnd;return false;},_drag:function(e){var oElem=DragHandler._oElem;var x=parseInt(oElem.style.left);var y=parseInt(oElem.style.top);e=e?e:window.event;oElem.style.left=x+(e.clientX-oElem.mouseX)+'px';oElem.style.top=y+(e.clientY-oElem.mouseY)+'px';oElem.mouseX=e.clientX;oElem.mouseY=e.clientY;oElem.drag(oElem,x,y);return false;},_dragEnd:function(){var oElem=DragHandler._oElem;var x=parseInt(oElem.style.left);var y=parseInt(oElem.style.top);oElem.dragEnd(oElem,x,y);document.onmousemove=null;document.onmouseup=null;DragHandler._oElem=null;}};function _getScroll(){if(self.pageYOffset){scrollTop=self.pageYOffset;scrollLeft=self.pageXOffset;}else if(document.documentElement&&document.documentElement.scrollTop){scrollTop=document.documentElement.scrollTop;scrollLeft=document.documentElement.scrollLeft;}else if(document.body){scrollTop=document.body.scrollTop;scrollLeft=document.body.scrollLeft;}
return{scrollTop:scrollTop,scrollLeft:scrollLeft};};function fixCrappyBrowser(element,caller){if(jQuery.browser.msie&&jQuery.browser.version==6){if(typeof DD_belatedPNG!='undefined'){if(element=='ps_websites'){jQuery('#'+element+' img').each(function(){DD_belatedPNG.fixPng(jQuery(this)[0]);});}else{DD_belatedPNG.fixPng(jQuery('#'+element+' .ps_hd .ps_c')[0]);DD_belatedPNG.fixPng(jQuery('#'+element+' .ps_hd')[0]);DD_belatedPNG.fixPng(jQuery('#'+element+' .ps_bd .ps_c')[0]);DD_belatedPNG.fixPng(jQuery('#'+element+' .ps_bd')[0]);DD_belatedPNG.fixPng(jQuery('#'+element+' .ps_ft .ps_c')[0]);DD_belatedPNG.fixPng(jQuery('#'+element+' .ps_ft')[0]);}};};}};})(jQuery);


//prettySocialble init script
jQuery(document).ready(function () {
			jQuery.prettySociable();
			jQuery.prettySociable.settings.urlshortener.bitly.active = true;
});