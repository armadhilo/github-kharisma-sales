! function($) {
    "use strict";

    var $window = $(window);

    function initComponents() {
        // popovers
        $('[data-toggle="popover"]').popover();

        // tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // icons
        feather.replace();

        // smooth scroll - scroll to target
        $('.smooth-scroll').on('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top - 0
            }, 1500, 'easeInOutExpo');
            event.preventDefault();
        });

        // back to top
        $('.back-to-top').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 3000);
            return false;
        });
    }


    function initMenu() {
        $('.navbar-toggle').on('click', function (event) {
            $(this).toggleClass('open');
            $('#navigation').slideToggle(400);
        });
        
        $('.navigation-menu>li').slice(-1).addClass('last-elements');
        
        $('.menu-arrow,.submenu-arrow').on('click', function (e) {
            if ($(window).width() < 992) {
                e.preventDefault();
                $(this).parent('li').toggleClass('open').find('.submenu:first').toggleClass('open');
            }
        });
        
        $(".navigation-menu a").each(function () {
            if (this.href == window.location.href) {
                var parent = $(this).parent();
                parent.addClass("active"); 
                parent.parent().parent().addClass("active"); 
                parent.parent().parent().parent().parent().addClass("active"); 
            }
        });
    
        // Clickable Menu
        $(".has-submenu a").on('click', function() {
            if(window.innerWidth < 992){
                var parent = $(this).parent();
                var siblings = $(this).siblings('.submenu');

                if(parent.hasClass('open')){
                    siblings.removeClass('open');
                    parent.removeClass('open');
                } else {
                    siblings.addClass('open');
                    parent.addClass('open');
                }
            }
        });
    }

    function initLoader() {
        $window.on('load', function() {
            $('#status').fadeOut();
            $('#preloader').delay(350).fadeOut('slow');
            $('body').delay(350).css({
                'overflow': 'visible'
            });
        }); 
    }


    function handleSticky() {
        var scroll = $window.scrollTop();

        if (scroll >= 50) {
            $(".sticky").addClass("nav-sticky");
        } else {
            $(".sticky").removeClass("nav-sticky");
        }
    }

    function handleScrollToTop() {
        if ($window.scrollTop() > 100) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    }
    

    // on doc ready - init components, menus, etc
    $window.ready(function() {
        initComponents();
        initLoader();
        initMenu();
    });

    // on window scroll, handling scroll to top and stikcy
    $window.scroll(function(e){
        handleScrollToTop();
        handleSticky();
    }); 

}(jQuery)