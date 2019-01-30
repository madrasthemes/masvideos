/*global masvideos_enhanced_select_params */
jQuery( function( $ ) {

	function getEnhancedSelectFormatString() {
		return {
			'language': {
				errorLoading: function() {
					// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
					return masvideos_enhanced_select_params.i18n_searching;
				},
				inputTooLong: function( args ) {
					var overChars = args.input.length - args.maximum;

					if ( 1 === overChars ) {
						return masvideos_enhanced_select_params.i18n_input_too_long_1;
					}

					return masvideos_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
				},
				inputTooShort: function( args ) {
					var remainingChars = args.minimum - args.input.length;

					if ( 1 === remainingChars ) {
						return masvideos_enhanced_select_params.i18n_input_too_short_1;
					}

					return masvideos_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
				},
				loadingMore: function() {
					return masvideos_enhanced_select_params.i18n_load_more;
				},
				maximumSelected: function( args ) {
					if ( args.maximum === 1 ) {
						return masvideos_enhanced_select_params.i18n_selection_too_long_1;
					}

					return masvideos_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
				},
				noResults: function() {
					return masvideos_enhanced_select_params.i18n_no_matches;
				},
				searching: function() {
					return masvideos_enhanced_select_params.i18n_searching;
				}
			}
		};
	}

	try {
		$( document.body )

			.on( 'masvideos-enhanced-select-init', function() {

				// Regular select boxes
				$( ':input.masvideos-enhanced-select, :input.chosen_select' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = $.extend({
						minimumResultsForSearch: 10,
						allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' )
					}, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				});

				$( ':input.masvideos-enhanced-select-nostd, :input.chosen_select_nostd' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = $.extend({
						minimumResultsForSearch: 10,
						allowClear:  true,
						placeholder: $( this ).data( 'placeholder' )
					}, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				});

				// Ajax enhanced search box
				$( ':input.masvideos-enhanced-search' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = {
						allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
						escapeMarkup: function( m ) {
							return m;
						},
						ajax: {
							url:         masvideos_enhanced_select_params.ajax_url,
							dataType:    'json',
							delay:       250,
							data:        function( params ) {
								return {
									term:     params.term,
									action:   $( this ).data( 'action' ) || 'masvideos_json_search_videos',
									security: $( this ).data( 'nonce_key' ) ? masvideos_enhanced_select_params[$( this ).data( 'nonce_key' )] : masvideos_enhanced_select_params.search_movies_nonce,
									exclude:  $( this ).data( 'exclude' ),
									include:  $( this ).data( 'include' ),
									limit:    $( this ).data( 'limit' )
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, text ) {
										terms.push( { id: id, text: text } );
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					};

					select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );

					if ( $( this ).data( 'sortable' ) ) {
						var $select = $(this);
						var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable({
							placeholder : 'ui-state-highlight select2-selection__choice',
							forcePlaceholderSize: true,
							items       : 'li:not(.select2-search__field)',
							tolerance   : 'pointer',
							stop: function() {
								$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function() {
									var id     = $( this ).data( 'data' ).id;
									var option = $select.find( 'option[value="' + id + '"]' )[0];
									$select.prepend( option );
								} );
							}
						});
					// Keep multiselects ordered alphabetically if they are not sortable.
					} else if ( $( this ).prop( 'multiple' ) ) {
						$( this ).on( 'change', function(){
							var $children = $( this ).children();
							$children.sort(function(a, b){
								var atext = a.text.toLowerCase();
								var btext = b.text.toLowerCase();

								if ( atext > btext ) {
									return 1;
								}
								if ( atext < btext ) {
									return -1;
								}
								return 0;
							});
							$( this ).html( $children );
						});
					}
				});

				// Ajax customer search boxes
				$( ':input.masvideos-customer-search' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = {
						allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '1',
						escapeMarkup: function( m ) {
							return m;
						},
						ajax: {
							url:         masvideos_enhanced_select_params.ajax_url,
							dataType:    'json',
							delay:       1000,
							data:        function( params ) {
								return {
									term:     params.term,
									action:   'masvideos_json_search_customers',
									security: masvideos_enhanced_select_params.search_customers_nonce,
									exclude:  $( this ).data( 'exclude' )
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, text ) {
										terms.push({
											id: id,
											text: text
										});
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					};

					select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );

					if ( $( this ).data( 'sortable' ) ) {
						var $select = $(this);
						var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable({
							placeholder : 'ui-state-highlight select2-selection__choice',
							forcePlaceholderSize: true,
							items       : 'li:not(.select2-search__field)',
							tolerance   : 'pointer',
							stop: function() {
								$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function() {
									var id     = $( this ).data( 'data' ).id;
									var option = $select.find( 'option[value="' + id + '"]' )[0];
									$select.prepend( option );
								} );
							}
						});
					}
				});

				// Ajax category search boxes
				$( ':input.masvideos-category-search' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = $.extend( {
						allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
						placeholder       : $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3,
						escapeMarkup      : function( m ) {
							return m;
						},
						ajax: {
							url:         masvideos_enhanced_select_params.ajax_url,
							dataType:    'json',
							delay:       250,
							data:        function( params ) {
								return {
									term:     params.term,
									action:   'masvideos_json_search_categories',
									security: masvideos_enhanced_select_params.search_categories_nonce
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, term ) {
										terms.push({
											id:   term.slug,
											text: term.formatted_name
										});
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					}, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				});
			})

			// MasVideos Backbone Modal
			.on( 'masvideos_backbone_modal_before_remove', function() {
				$( '.masvideos-enhanced-select, :input.masvideos-enhanced-search, :input.masvideos-customer-search' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
			})

			.trigger( 'masvideos-enhanced-select-init' );

		$( 'html' ).on( 'click', function( event ) {
			if ( this === event.target ) {
				$( '.masvideos-enhanced-select, :input.masvideos-enhanced-search, :input.masvideos-customer-search' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
			}
		} );
	} catch( err ) {
		// If select2 failed (conflict?) log the error but don't stop other scripts breaking.
		window.console.log( err );
	}
});
