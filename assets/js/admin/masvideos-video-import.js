/*global ajaxurl, masvideos_video_import_params */
;(function ( $, window ) {

    /**
     * videoImportForm handles the import process.
     */
    var videoImportForm = function( $form ) {
        this.$form           = $form;
        this.xhr             = false;
        this.mapping         = masvideos_video_import_params.mapping;
        this.position        = 0;
        this.file            = masvideos_video_import_params.file;
        this.update_existing = masvideos_video_import_params.update_existing;
        this.delimiter       = masvideos_video_import_params.delimiter;
        this.security        = masvideos_video_import_params.import_nonce;

        // Number of import successes/failures.
        this.imported = 0;
        this.failed   = 0;
        this.updated  = 0;
        this.skipped  = 0;

        // Initial state.
        this.$form.find('.masvideos-importer-progress').val( 0 );

        this.run_import = this.run_import.bind( this );

        // Start importing.
        this.run_import();
    };

    /**
     * Run the import in batches until finished.
     */
    videoImportForm.prototype.run_import = function() {
        var $this = this;

        $.ajax( {
            type: 'POST',
            url: ajaxurl,
            data: {
                action          : 'masvideos_do_ajax_video_import',
                position        : $this.position,
                mapping         : $this.mapping,
                file            : $this.file,
                update_existing : $this.update_existing,
                delimiter       : $this.delimiter,
                security        : $this.security
            },
            dataType: 'json',
            success: function( response ) {
                if ( response.success ) {
                    $this.position  = response.data.position;
                    $this.imported += response.data.imported;
                    $this.failed   += response.data.failed;
                    $this.updated  += response.data.updated;
                    $this.skipped  += response.data.skipped;
                    $this.$form.find('.masvideos-importer-progress').val( response.data.percentage );

                    if ( 'done' === response.data.position ) {
                        window.location = response.data.url + '&videos-imported=' + parseInt( $this.imported, 10 ) + '&videos-failed=' + parseInt( $this.failed, 10 ) + '&videos-updated=' + parseInt( $this.updated, 10 ) + '&videos-skipped=' + parseInt( $this.skipped, 10 );
                    } else {
                        $this.run_import();
                    }
                }
            }
        } ).fail( function( response ) {
            window.console.log( response );
        } );
    };

    /**
     * Function to call videoImportForm on jQuery selector.
     */
    $.fn.masvideos_video_importer = function() {
        new videoImportForm( this );
        return this;
    };

    $( '.masvideos-importer' ).masvideos_video_importer();

})( jQuery, window );
