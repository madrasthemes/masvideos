( function( $, window ) {
    'use strict';

    var is_rtl = $( 'body,html' ).hasClass( 'rtl' );

    /**
     * Gallery Post Slide Show
     */

    $( '.article__attachment--gallery > .gallery' ).each( function() {
        var $this = $( this ), showDots = true, columns = 1, classNames = '', index = 0;

        classNames = $this.attr( 'class' );
        index      = classNames.indexOf( 'gallery-columns-') + 16;
        columns    = Number( classNames.substr( index, 1 ) );

        showDots =  ! ( ( $this.find( '.gallery-item' ).length / columns ) > 10 );

        $this.slick( {
            slidesToShow: columns,
            slidesToScroll: columns,
            dots: showDots,
            arrows: ! showDots
        } );
    } );

    $('[data-ride="vodi-slick-carousel"]').each( function() {
        var $slick_target = false;

        if ( $(this).data( 'slick' ) !== 'undefined' && $(this).find( $(this).data( 'wrap' ) ).length > 0 ) {
            $slick_target = $(this).find( $(this).data( 'wrap' ) );
            $slick_target.data( 'slick', $(this).data( 'slick' ) );
        } else if ( $(this).data( 'slick' ) !== 'undefined' && $(this).is( $(this).data( 'wrap' ) ) ) {
            $slick_target = $(this);
        }

        if( $slick_target ) {
            $slick_target.slick();
        }
    });

    /**
     * Movies Carousel
     */
    $( '.movies-carousel__inner:not([data-ride="vodi-slick-carousel"])' ).each( function() {
        $( this ).slick();
    } );


    /**
     * Videos Carousel
     */
    $( '.videos-carousel__inner:not([data-ride="vodi-slick-carousel"])' ).each( function() {
        $( this ).slick();
    } );

    /**
     * Playlist Carousel
     */
    $( '.movies-collection-carousel__inner:not([data-ride="vodi-slick-carousel"])' ).each( function() {
        $( this ).slick();
    } );

    /*===================================================================================*/
    /*  Off Canvas Menu
    /*===================================================================================*/

    $( '.site-header__offcanvas .navbar-toggler' ).on( 'click', function() {
        $( this ).closest('.site-header__offcanvas').toggleClass( "toggled" );
    } );


    // Hamburger Sidebar Close Trigger when click outside menu slide
    $( document ).on( 'click', function( event ) {
        if ( $( '.site-header__offcanvas' ).hasClass( 'toggled' ) ) {
            if ( ! $( '.navbar-toggler' ).is( event.target ) && 0 === $( '.navbar-toggler' ).has( event.target ).length && ! $( '.offcanvas-collapse' ).is( event.target ) && 0 === $( '.offcanvas-collapse' ).has( event.target ).length ) {
                $( '.site-header__offcanvas' ).removeClass( 'toggled' );
            }
        }
    });

    /*===================================================================================*/
    /*  Tab Widgets
    /*===================================================================================*/

    $( '.vtw-tabbed-tabs').each( function(){
        var tabs =  $( this );

        tabs.on( 'click', '.vtw-tabbed-nav li', function( e ){
            e.preventDefault();
            var tab = $( this );
            var t = tab.attr( 'data-tab' );
            if ( typeof t !== "undefined" ) {
               $( '.vtw-tabbed-nav li', tabs ).removeClass('tab-active' );
                tab.addClass( 'tab-active' );
                $( '.vtw-tabbed-cont',  tabs).removeClass('tab-active');
                $( '.vtw-tabbed-cont.'+t,  tabs).addClass('tab-active');

            }
        } );

        $( '.vtw-tabbed-nav li', tabs).eq( 0).trigger( 'click' );
    } );

    /*===================================================================================*/
    /*  Header v3 search
    /*===================================================================================*/

    // Search Toggler
    $( '.site-header__inner .site-header__search .search-btn' ).on( 'click', function(e) {
        $( this ).closest('.site-header__search').toggleClass( "show" );
        //e.preventDefault();
        //$('.search-field').animate({width: 'toggle'});
    } );

//     $(document).ready( function(){
//     $('.search-submit').click( function() {
//         var toggleWidth = $(".search-field").width() == 350 ? "0px" : "350px";
//         $('.search-field').animate({ width: toggleWidth });
//     });
// });

    //Search Close Trigger when click outside
    $( document ).on( 'click', function(event) {
        if ( $( '.site-header__search' ).hasClass( 'show' ) ) {
            if ( ! $( '.site-header__search' ).is( event.target ) && 0 === $( '.site-header__search' ).has( event.target ).length ) {
                $( '.site-header__search' ).removeClass( "show" );
                $('.search-form').removeClass( 'animated fadeInRight' );
            }

        }
    });

    /*===================================================================================*/
    /*  Deal Countdown timer
    /*===================================================================================*/

    $( '.deal-countdown-timer' ).each( function() {
        var deal_countdown_text = vodi_options.deal_countdown_text;

        // set the date we're counting down to
        var deal_time_diff = $(this).children('.deal-time-diff').text();
        var countdown_output = $(this).children('.deal-countdown');
        var target_date = ( new Date().getTime() ) + ( deal_time_diff * 1000 );

        // variables for time units
        var days, hours, minutes, seconds;

        // update the tag with id "countdown" every 1 second
        setInterval( function () {

            // find the amount of "seconds" between now and target
            var current_date = new Date().getTime();
            var seconds_left = (target_date - current_date) / 1000;

            // do some time calculations
            days = parseInt(seconds_left / 86400);
            seconds_left = seconds_left % 86400;

            hours = parseInt(seconds_left / 3600);
            seconds_left = seconds_left % 3600;

            minutes = parseInt(seconds_left / 60);
            seconds = parseInt(seconds_left % 60);

            // format countdown string + set tag value
            countdown_output.html( '<span data-value="' + days + '" class="days"><span class="value">' + days +  '</span><b>' + deal_countdown_text.days_text + '</b></span><span class="hours"><span class="value">' + hours + '</span><b>' + deal_countdown_text.hours_text + '</b></span><span class="minutes"><span class="value">'
            + minutes + '</span><b>' + deal_countdown_text.mins_text + '</b></span><span class="seconds"><span class="value">' + seconds + '</span><b>' + deal_countdown_text.secs_text + '</b></span>' );

        }, 1000 );
    });

    /*===================================================================================*/
    /*  Shop Grid/List Switcher
    /*===================================================================================*/

    $( document ).ready( function() {
        $( '.archive-view-switcher' ).on( 'click', '.nav-link', function() {
            $( '.vodi-archive-wrapper' ).attr( 'data-view', $(this).data( 'archiveClass' ) );
        });
    });


} )( jQuery );
