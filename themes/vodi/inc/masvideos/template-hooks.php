<?php

require_once get_template_directory() . '/inc/masvideos/template-hooks/archive-tv-shows.php';
require_once get_template_directory() . '/inc/masvideos/template-hooks/loop.php';

add_filter( 'masvideos_movies_pagination_args', 'masvideos_vodi_movies_pagination_args', 10 );