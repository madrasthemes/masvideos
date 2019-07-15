/*global ajaxurl, masvideos_tv_show_import_params */
;(function ( $, window ) {

    /**
     * tvShowImportForm handles the import process.
     */
    var tvShowImportForm = function( $form ) {
        this.$form           = $form;
        this.xhr             = false;
        this.mapping         = masvideos_tv_show_import_params.mapping;
        this.position        = 0;
        this.file            = masvideos_tv_show_import_params.file;
        this.update_existing = masvideos_tv_show_import_params.update_existing;
        this.delimiter       = masvideos_tv_show_import_params.delimiter;
        this.security        = masvideos_tv_show_import_params.import_nonce;

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
    tvShowImportForm.prototype.run_import = function() {
        var $this = this;

        $.ajax( {
            type: 'POST',
            url: ajaxurl,
            data: {
                action          : 'masvideos_do_ajax_tv_show_import',
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
                        window.location = response.data.url + '&tv-shows-imported=' + parseInt( $this.imported, 10 ) + '&tv-shows-failed=' + parseInt( $this.failed, 10 ) + '&tv-shows-updated=' + parseInt( $this.updated, 10 ) + '&tv-shows-skipped=' + parseInt( $this.skipped, 10 );
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
     * Function to call tvShowImportForm on jQuery selector.
     */
    $.fn.masvideos_tv_show_importer = function() {
        new tvShowImportForm( this );
        return this;
    };

    $( '.masvideos-importer' ).masvideos_tv_show_importer();

})( jQuery, window );
