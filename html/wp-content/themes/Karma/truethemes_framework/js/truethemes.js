/*
* Information about scripts in truethemes_framework/global/javascript.php
*/


/* ------------------------------------------------------------------------
jQuery functions on page load
 * ------------------------------------------------------------------------- */
jQuery(document).ready(function () {
    initScrollTop(); //initialise scroll top
if (jQuery.browser.msie || jQuery.browser.opera) { jQuery(window).load(function() {jQuery('.big-banner #main .main-area').css("padding-top", "118px");}); }
});


/* ------------------------------------------------------------------------
Main Navigation
 * ------------------------------------------------------------------------- */

function initNav() {
    var nav = jQuery("#menu-main-nav");
    var duration = 260;
    jQuery(nav).find(".sub-menu").css({
        left: 0
    });
    jQuery(nav).find("> li").each(function () {
        var height = jQuery(this).find("> .drop").height();
        jQuery(this).find("> .drop").css({
            display: "none",
            height: 0,
            overflow: "hidden"
        });
        jQuery(this).find(".drop li > .drop").css({
            display: "none",
            width: 0
        });
        if (!jQuery.browser.msie) {
            jQuery(this).find("> .drop").css({
                "opacity": 0
            });
            jQuery(this).find(".drop li > .drop").css({
                "opacity": 0
            })
        }
        jQuery(this).mouseenter(function () {
            jQuery(this).addClass("hover");
            var drop = jQuery(this).find("> .drop");
            if (jQuery.browser.msie) {
                jQuery(drop).css({
                    display: "block"
                }).stop().animate({
                    "height": height
                }, duration, function () {
                    jQuery(this).css({
                        "overflow": "visible"
                    })
                })
            } else {
                jQuery(drop).css({
                    display: "block"
                }).stop().animate({
                    "height": height,
                    "opacity": 1
                }, duration, function () {
                    jQuery(this).css({
                        "overflow": "visible"
                    })
                })
            }
        }).mouseleave(function () {
            var _this = jQuery(this);
            if (jQuery.browser.msie) {
                jQuery(this).find("> .drop").stop().css({
                    "overflow": "hidden"
                }).animate({
                    "height": 0
                }, duration, function () {
                    jQuery(_this).removeClass("hover")
                })
            } else {
                jQuery(this).find("> .drop").stop().css({
                    "overflow": "hidden"
                }).animate({
                    "height": 0,
                    "opacity": 0
                }, duration, function () {
                    jQuery(_this).removeClass("hover")
                })
            }
        });
        jQuery(this).find(".drop ul > li ").mouseenter(function () {
            jQuery(this).addClass("hover");
            var pageW = getPageSize()[2];
            if (pageW < jQuery(this).offset().left + 236 * 2) {
                jQuery(this).find("> .drop").css({
                    left: 'auto',
                    right: 236
                })
            }
            if (jQuery.browser.msie) {
                jQuery(this).find("> .drop").css({
                    display: 'block'
                }).stop().animate({
                    "width": 236
                }, duration, function () {
                    jQuery(this).css({
                        overflow: 'visible'
                    })
                })
            } else {
                jQuery(this).find("> .drop").css({
                    display: 'block'
                }).stop().animate({
                    "width": 236,
                    "opacity": 1
                }, duration, function () {
                    jQuery(this).css({
                        overflow: 'visible'
                    })
                })
            }
        }).mouseleave(function () {
            jQuery(this).removeClass("hover");
            if (jQuery.browser.msie) {
                jQuery(this).find("> .drop").stop().css({
                    overflow: 'hidden'
                }).animate({
                    width: 0
                }, duration, function () {
                    jQuery(this).css({
                        display: 'none'
                    })
                })
            } else {
                jQuery(this).find("> .drop").stop().css({
                    overflow: 'hidden'
                }).animate({
                    width: 0,
                    "opacity": 0
                }, duration, function () {
                    jQuery(this).css({
                        display: 'none'
                    })
                })
            }
        })
    })
}


(function (B) {
    B(document).ready(function () {
        var mainNav = B('#menu-main-nav');
        var lis = mainNav.find('li');
        var shownav = jQuery("#menu-main-nav");
        lis.children('ul').wrap('<div class="c" / >');
        var cElems = B('.c');
        cElems.wrap('<div class="drop" / >');
        cElems.before('<div class="t"></div>');
        cElems.after('<div class="b"></div>');
        jQuery(shownav).find(".sub-menu").css({
            display: "block"
        });
        initNav()
    })
})(jQuery);
(function (C) {
    C(document).ready(function () {
        jQuery("#menu-main-nav li:has(ul)").addClass("parent")
    })
})(jQuery);





function getPageSize(){var xScroll,yScroll;if(window.innerHeight&&window.scrollMaxY){xScroll=document.body.scrollWidth;yScroll=window.innerHeight+window.scrollMaxY}else if(document.body.scrollHeight>document.body.offsetHeight){xScroll=document.body.scrollWidth;yScroll=document.body.scrollHeight}else if(document.documentElement&&document.documentElement.scrollHeight>document.documentElement.offsetHeight){xScroll=document.documentElement.scrollWidth;yScroll=document.documentElement.scrollHeight}else{xScroll=document.body.offsetWidth;yScroll=document.body.offsetHeight}var windowWidth,windowHeight;if(self.innerHeight){windowWidth=self.innerWidth;windowHeight=self.innerHeight}else if(document.documentElement&&document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;windowHeight=document.documentElement.clientHeight}else if(document.body){windowWidth=document.body.clientWidth;windowHeight=document.body.clientHeight}if(yScroll<windowHeight){pageHeight=windowHeight}else{pageHeight=yScroll}if(xScroll<windowWidth){pageWidth=windowWidth}else{pageWidth=xScroll}return[pageWidth,pageHeight,windowWidth,windowHeight]}





/* ------------------------------------------------------------------------
Portfolio Image Fade
 * ------------------------------------------------------------------------- */
(function (jQuery) {
    jQuery(window).load(function () {

        	jQuery('[class^="attachment"]').each(function(index){
			var t = jQuery('[class^="attachment"]').length;
			if(t>0){ // if there is image length, we fade in
				jQuery(this).delay(400*index).fadeIn(500);
				}  
     		});

   });
})(jQuery);



/* ------------------------------------------------------------------------
Portfolio Image Hover
 * ------------------------------------------------------------------------- */
(function (jQuery) {
    jQuery(document).ready(function () {
        jQuery('.preload').hover(function () {
            jQuery(this).children().first().children().first().stop(true);
            jQuery(this).children().first().children().first().fadeTo('normal', .90)
        }, function () {
            jQuery(this).children().first().children().first().stop(true);
            jQuery(this).children().first().children().first().fadeTo('normal', 0)
        })
    })
})(jQuery);





/* ------------------------------------------------------------------------
Button Hover
 * ------------------------------------------------------------------------- */
if (jQuery.browser.msie) { /* time to download a new browser */
} else {
    jQuery(document).ready(function () {
        jQuery(".ka_button, #ka-submit, #searchform #searchsubmit, .ka-form-submit, #mc_signup #mc_signup_submit, .fade-me").hover(function () {
            jQuery(this).stop().animate({
                opacity: 0.7
            }, 250)
        }, function () {
            jQuery(this).stop().animate({
                opacity: 1.0
            }, 250)
        })
    });
    jQuery(document).ready(function () {
        jQuery(".social_icons a").hover(function () {
            jQuery(this).stop().animate({
                opacity: 0.65
            }, 200)
        }, function () {
            jQuery(this).stop().animate({
                opacity: 1
            }, 200)
        })
    });
}




/* ------------------------------------------------------------------------
Scroll to Top
 * ------------------------------------------------------------------------- */
function initScrollTop() {
    var change_speed = 1200;
    jQuery('a.link-top').click(function () {
        if (!jQuery.browser.opera) {
            jQuery('body').animate({
                scrollTop: 0
            }, {
                queue: false,
                duration: change_speed
            })
        }
        jQuery('html').animate({
            scrollTop: 0
        }, {
            queue: false,
            duration: change_speed
        });
        return false
    })
}