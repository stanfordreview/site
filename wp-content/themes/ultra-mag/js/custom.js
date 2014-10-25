/* =========================================================
Comment Form
============================================================ */
jQuery(document).ready(function(){
    if(jQuery("#contact-form").length > 0){
        // get front validate localization
        var validateLocalization = kopa_custom_front_localization.validate;

        // Validate the contact form
        jQuery('#contact-form').validate({
        
            // Add requirements to each of the fields
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                },
                message: {
                    required: true,
                    minlength: 10
                }
            },
            
            // Specify what error messages to display
            // when the user does something horrid
            messages: {
                name: {
                    required: validateLocalization.name.required,
                    minlength: jQuery.format(validateLocalization.name.minlength)
                },
                email: {
                    required: validateLocalization.email.required,
                    email: validateLocalization.email.email
                },
                url: {
                    required: validateLocalization.url.required,
                    url: validateLocalization.url.url
                },
                message: {
                    required: validateLocalization.message.required,
                    minlength: jQuery.format(validateLocalization.message.minlength)
                }
            },
            
            // Use Ajax to send everything to processForm.php
            submitHandler: function(form) {
                jQuery("#submit-contact").attr("value", validateLocalization.form.sending);
                jQuery(form).ajaxSubmit({
                    success: function(responseText, statusText, xhr, $form) {
                        jQuery("#response").html(responseText).hide().slideDown("fast");
                        jQuery("#submit-contact").attr("value", validateLocalization.form.submit);
                    }
                });
                return false;
            }
        });
    }
});

/* =========================================================
Sub menu
==========================================================*/
(function($){ //create closure so we can safely use $ as alias for jQuery

    jQuery(document).ready(function(){

        // initialise plugin
        var example = jQuery('#main-menu').superfish({
            //add options here if required
        });
    });

})(jQuery);

/* =========================================================
Mobile menu
============================================================ */
jQuery(document).ready(function () {
     
    jQuery('#mobile-menu > span').click(function () {
 
        var mobile_menu = jQuery('#toggle-view-menu');
 
        if (mobile_menu.is(':hidden')) {
            mobile_menu.slideDown('300');
            jQuery(this).children('span').html('-');    
        } else {
            mobile_menu.slideUp('300');
            jQuery(this).children('span').html('+');    
        }
        
        
         
    });
    
    jQuery('#toggle-view-menu li').click(function () {
 
        var text = jQuery(this).children('div.menu-panel');
 
        if (text.is(':hidden')) {
            text.slideDown('300');
            jQuery(this).children('span').html('-');    
        } else {
            text.slideUp('300');
            jQuery(this).children('span').html('+');    
        }
        
        jQuery(this).toggleClass('active');
         
    });
 
});

/* =========================================================
Create footer mobile menu
============================================================ */
function createMobileMenu(menu_id, mobile_menu_id){
    // Create the dropdown base
    jQuery("<select />").appendTo(menu_id);
    jQuery(menu_id).find('select').first().attr("id",mobile_menu_id);
    
    // Populate dropdown with menu items
    jQuery(menu_id).find('a').each(function() {        
        var el = jQuery(this);       
        
        var selected = '';
        if (el.parent().hasClass('current-menu-item') == true){
            selected = "selected='selected'";
        }        
        
        var depth = el.parents("ul").size();
        var space = '';
        if(depth > 1){
            for(i=1; i<depth; i++){
                space += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }        
        
        jQuery("<option "+selected+" value='"+el.attr("href")+"'>"+space+el.text()+"</option>").appendTo(jQuery(menu_id).find('select').first());
    });
    jQuery(menu_id).find('select').first().change(function() {
        window.location = jQuery(this).find("option:selected").val();
    });    
}

jQuery(document).ready(function(){
    if(jQuery('#bottom-nav').length > 0){
        createMobileMenu('#bottom-nav','responsive-menu');    
    }
});


function createMobileMenu(menu_id, mobile_menu_id){
    // Create the dropdown base
    jQuery("<select />").appendTo(menu_id);
    jQuery(menu_id).find('select').first().attr("id",mobile_menu_id);
    
    // Populate dropdown with menu items
    jQuery(menu_id).find('a').each(function() {        
        var el = jQuery(this);       
        
        var selected = '';
        if (el.parent().hasClass('current-menu-item') == true){
            selected = "selected='selected'";
        }        
        
        var depth = el.parents("ul").size();
        var space = '';
        if(depth > 1){
            for(i=1; i<depth; i++){
                space += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }        
        
        jQuery("<option "+selected+" value='"+el.attr("href")+"'>"+space+el.text()+"</option>").appendTo(jQuery(menu_id).find('select').first());
    });
    jQuery(menu_id).find('select').first().change(function() {
        window.location = jQuery(this).find("option:selected").val();
    });    
}

jQuery(document).ready(function(){
    if(jQuery('#top-nav').length > 0){
        createMobileMenu('#top-nav','top-responsive-menu');    
    }
});

/* =========================================================
Flex Slider
============================================================ */
jQuery(window).load(function(){
  jQuery('.home-slider').each(function () {
    var $this = jQuery(this),
        dataAnimation = $this.data('animation'),
        dataDirection = $this.data('direction'),
        dataSlideshowSpeed = $this.data('slideshow_speed'),
        dataAnimationSpeed = $this.data('animation_speed'),
        dataIsAutoPlay = $this.data('autoplay');

    $this.flexslider({
        animation: dataAnimation,
        direction: dataDirection,
        slideshowSpeed: dataSlideshowSpeed,
        animationSpeed: dataAnimationSpeed,
        slideshow: dataIsAutoPlay,
        smoothHeight: true,
        start: function(slider){
            slider.removeClass('loading');
        }
    });
  });

});

/* =========================================================
Single post slider
============================================================ */
jQuery(window).load(function(){
  
  jQuery('.kp-single-carousel').each(function () {
    var $this = jQuery(this);

    $this.flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 162,
        itemMargin: 5,
        asNavFor: $this.data('main-slider-id')
      });
  });

  jQuery('.kp-single-slider').each(function () {
    var $this = jQuery(this);

    $this.flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: $this.data('carousel-id'),
        start: function(slider){
          jQuery(slider).removeClass('loading');
        }
    });
  })
});

/* =========================================================
Carousel
============================================================ */
jQuery(window).load(function() {
    
    if( jQuery(".kp-featured-carousel").length > 0){
        jQuery('.kp-featured-carousel').carouFredSel({
            responsive: true,
            prev: '#prev-1',
            next: '#next-1',
            width: '100%',
            scroll: 1,
            pagination: "#pager2",
            auto: false,
            items: {
                width: 198,
                height: 'auto',
                visible: {              
                    min: 1,
                    max: 3
                }
            }
        });
    }
    
    if( jQuery(".kopa-gallery-carousel").length > 0){
        jQuery('.kopa-gallery-carousel').carouFredSel({
            responsive: true,
            prev: '#prev-1',
            next: '#next-1',
            width: '100%',
            scroll: 1,
            auto: false,
            items: {
                width: 140,
                height: 'auto',
                visible: {              
                    min: 1,
                    max: 5
                }
            }
        });
    }

    if( jQuery(".kp-ourclients-carousel").length > 0){
        jQuery('.kp-ourclients-carousel').carouFredSel({
            responsive: true,
            prev: '#prev-1',
            next: '#next-1',
            width: '100%',
            scroll: 1,
            pagination: "#pager3",
            auto: false,
            items: {
                width: 198,
                height: 'auto',
                visible: {              
                    min: 1,
                    max: 3
                }
            }
        });
    }
});

/* =========================================================
Twitter
============================================================ */
jQuery(function(){
    jQuery('#tweets').tweetable({
        username: 'philipbeel',
        time: true,
        rotate: false,
        speed: 4000,
        limit: 3,
        replies: false,
        position: 'append',
        failed: "Sorry, twitter is currently unavailable for this user.",
        html5: true,
        onComplete:function($ul){
            jQuery('time').timeago();
        }
    });
});

/* =========================================================
Tabs
============================================================ */
jQuery(document).ready(function() { 
    
    if ( jQuery('.tabs-1').length > 0 ) {
        jQuery( '.tabs-1' ).each(function () {
            var $this = jQuery(this),
                firstTabContentID = $this.find('li a').first().attr('href');

            // add active class to first list item
            $this.children('li').first().addClass('active');

            // hide all tabs
            $this.find('li a').each(function () {
                var tabContentID = jQuery(this).attr('href');
                jQuery(tabContentID).hide();    
            });
            // show only first tab
            jQuery(firstTabContentID).show();

            $this.children('li').on('click', function(e) {
                e.preventDefault();
                var $this = jQuery(this),
                    $currentClickLink = $this.children('a');

                if ( $this.hasClass('active') ) {
                    return;
                } else {
                    $this.addClass('active')
                        .siblings().removeClass('active');
                }

                $this.siblings('li').find('a').each(function () {
                    var tabContentID = jQuery(this).attr('href');
                    jQuery(tabContentID).hide();
                });

                jQuery( $currentClickLink.attr('href') ).fadeIn();

            });
        });
    }
    
});

/* =========================================================
Accordion
========================================================= */
jQuery(document).ready(function() {
        var acc_wrapper=jQuery('.acc-wrapper');
        if (acc_wrapper.length >0) 
        {
            
            jQuery('.acc-wrapper .accordion-container').hide();
            jQuery.each(acc_wrapper, function(index, item){
                jQuery(this).find(jQuery('.accordion-title')).first().addClass('active').next().show();
                
            });
            
            jQuery('.accordion-title').on('click', function(e) {
                kopa_accordion_click(jQuery(this));
                e.preventDefault();
            });
            
            var titles = jQuery('.accordion-title');
            
            jQuery.each(titles,function(){
                kopa_accordion_click(jQuery(this));
            });
        }
        
});

function kopa_accordion_click (obj) {
    if( obj.next().is(':hidden') ) {
        obj.parent().find(jQuery('.active')).removeClass('active').next().slideUp(300);
        obj.toggleClass('active').next().slideDown(300);
                            
    }
jQuery('.accordion-title span').html('+');
    if (obj.hasClass('active')) {
        obj.find('span').first().html('-');              
    } 
}

/* =========================================================
Toggle Boxes
============================================================ */
jQuery(document).ready(function () {
     
    jQuery('#toggle-view li').click(function (event) {
        
        var text = jQuery(this).children('div.panel');
 
        if (text.is(':hidden')) {
            jQuery(this).addClass('active');
            text.slideDown('300');
            jQuery(this).children('span').html('-');                 
        } else {
            jQuery(this).removeClass('active');
            text.slideUp('300');
            jQuery(this).children('span').html('+');               
        }
         
    });
 
});

/* =========================================================
prettyPhoto
============================================================ */
jQuery(document).ready(function(){
    init_image_effect();
});

jQuery(window).resize(function(){
    init_image_effect();
});

function init_image_effect(){    

    var view_p_w = jQuery(window).width();
    var pp_w = 500;
    var pp_h = 344;
    
    if(view_p_w <= 479){
        pp_w = '120%';
        pp_h = '100%';
    }
    else if(view_p_w >= 480 && view_p_w <= 599){
        pp_w = '100%';
        pp_h = '170%';
    }
            
    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
        show_title: false,
        deeplinking:false,
        social_tools:false,
        default_width: pp_w,
        default_height: pp_h
    });    
}

/* =========================================================
Scroll to top
============================================================ */
jQuery(document).ready(function(){

    // hide #back-top first
    jQuery("#back-top").hide();
    
    // fade in #back-top
    jQuery(function () {
        jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() > 200) {
                jQuery('#back-top').fadeIn();
            } else {
                jQuery('#back-top').fadeOut();
            }
        });

        // scroll body to 0px on click
        jQuery('#back-top a').click(function () {
            jQuery('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });

});

/* =========================================================
Sticky menu
============================================================ */
//initiating jQuery 
jQuery(function($) { 
    jQuery(document).ready( function() { 
        //enabling stickUp on the '.navbar-wrapper' class 
        jQuery('.menu-bar').stickUp(); 
    }); 
});