jQuery( function( $ ) {

    // masvideos_single_video_params is required to continue.
    if ( typeof masvideos_single_video_params === 'undefined' ) {
        return false;
    }

    $( 'body' )
        // Star ratings for comments
        .on( 'init', '#rating', function() {
            $( '#rating' ).hide().before( '<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a><a class="star-6" href="#">6</a><a class="star-7" href="#">7</a><a class="star-8" href="#">8</a><a class="star-9" href="#">9</a><a class="star-10" href="#">10</a></span></p>' );
        } )
        .on( 'click', '#respond p.stars a', function() {
            var $star       = $( this ),
                $rating     = $( this ).closest( '#respond' ).find( '#rating' ),
                $container  = $( this ).closest( '.stars' );

            $rating.val( $star.text() );
            $star.siblings( 'a' ).removeClass( 'active' );
            $star.addClass( 'active' );
            $container.addClass( 'selected' );

            return false;
        } )
        .on( 'click', '#respond #submit', function() {
            var $rating = $( this ).closest( '#respond' ).find( '#rating' ),
                rating  = $rating.val();

            if ( $rating.length > 0 && ! rating && masvideos_single_video_params.review_rating_required === 'yes' ) {
                window.alert( masvideos_single_video_params.i18n_required_rating_text );

                return false;
            }
        } );

    // Init Star Ratings
    $( '#rating' ).trigger( 'init' );

    /**
     * Video gallery class.
     */
    var VideoGallery = function( $target, args ) {
        this.$target = $target;
        this.$images = $( '.masvideos-gallery__image', $target );

        // No images? Abort.
        if ( 0 === this.$images.length ) {
            this.$target.css( 'opacity', 1 );
            return;
        }

        // Make this object available.
        $target.data( 'video_gallery', this );

        // Pick functionality to initialize...
        this.photoswipe_enabled = typeof PhotoSwipe !== 'undefined' && masvideos_single_video_params.photoswipe_enabled;

        // ...also taking args into account.
        if ( args ) {
            this.photoswipe_enabled = false === args.photoswipe_enabled ? false : this.photoswipe_enabled;
        }

        // Bind functions to this.
        this.initPhotoswipe       = this.initPhotoswipe.bind( this );
        this.getGalleryItems      = this.getGalleryItems.bind( this );
        this.openPhotoswipe       = this.openPhotoswipe.bind( this );

        if ( this.photoswipe_enabled ) {
            this.initPhotoswipe();
        }
    };

    /**
     * Init PhotoSwipe.
     */
    VideoGallery.prototype.initPhotoswipe = function() {
        this.$target.on( 'click', '.masvideos-gallery__image a', this.openPhotoswipe );
    };

    /**
     * Get video gallery image items.
     */
    VideoGallery.prototype.getGalleryItems = function() {
        var $slides = this.$images,
            items   = [];

        if ( $slides.length > 0 ) {
            $slides.each( function( i, el ) {
                var img = $( el ).find( 'img' );

                if ( img.length ) {
                    var large_image_src = img.attr( 'data-large_image' ),
                        large_image_w   = img.attr( 'data-large_image_width' ),
                        large_image_h   = img.attr( 'data-large_image_height' ),
                        item            = {
                            src  : large_image_src,
                            w    : large_image_w,
                            h    : large_image_h,
                            title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
                        };
                    items.push( item );
                }
            } );
        }

        return items;
    };

    /**
     * Open photoswipe modal.
     */
    VideoGallery.prototype.openPhotoswipe = function( e ) {
        e.preventDefault();

        var pswpElement = $( '.pswp' )[0],
            items       = this.getGalleryItems(),
            eventTarget = $( e.target ),
            clicked;

            clicked = eventTarget.closest( '.masvideos-gallery__image' );

        var options = $.extend( {
            index: $( clicked ).index(),
            addCaptionHTMLFn: function( item, captionEl ) {
                if ( ! item.title ) {
                    captionEl.children[0].textContent = '';
                    return false;
                }
                captionEl.children[0].textContent = item.title;
                return true;
            }
        }, masvideos_single_video_params.photoswipe_options );

        // Initializes and opens PhotoSwipe.
        var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
        photoswipe.init();
    };

    /**
     * Function to call masvdieos_video_gallery on jquery selector.
     */
    $.fn.masvdieos_video_gallery = function( args ) {
        new VideoGallery( this, args || masvideos_single_video_params );
        return this;
    };

    /*
     * Initialize all galleries on page.
     */
    $( '.masvideos-video-gallery' ).each( function() {

        $( this ).trigger( 'masvdieos-video-gallery-before-init', [ this, masvideos_single_video_params ] );

        $( this ).masvdieos_video_gallery( masvideos_single_video_params );

        $( this ).trigger( 'masvdieos-video-gallery-after-init', [ this, masvideos_single_video_params ] );

    } );
} );