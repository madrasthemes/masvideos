/*global ajaxurl, masvideos_movie_import_params */
;(function ( $, window ) {

	/**
	 * movieImportForm handles the import process.
	 */
	var movieImportForm = function( $form ) {
		this.$form           = $form;
		this.xhr             = false;
		this.mapping         = masvideos_movie_import_params.mapping;
		this.position        = 0;
		this.file            = masvideos_movie_import_params.file;
		this.update_existing = masvideos_movie_import_params.update_existing;
		this.delimiter       = masvideos_movie_import_params.delimiter;
		this.security        = masvideos_movie_import_params.import_nonce;

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
	movieImportForm.prototype.run_import = function() {
		var $this = this;

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action          : 'masvideos_do_ajax_movie_import',
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
						window.location = response.data.url + '&movies-imported=' + parseInt( $this.imported, 10 ) + '&movies-failed=' + parseInt( $this.failed, 10 ) + '&movies-updated=' + parseInt( $this.updated, 10 ) + '&movies-skipped=' + parseInt( $this.skipped, 10 );
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
	 * Function to call movieImportForm on jQuery selector.
	 */
	$.fn.masvideos_movie_importer = function() {
		new movieImportForm( this );
		return this;
	};

	$( '.masvideos-importer' ).masvideos_movie_importer();

})( jQuery, window );
