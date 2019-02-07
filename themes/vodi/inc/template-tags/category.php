<?php
/**
 * Template tags used in Category Pages
 */


if ( ! function_exists( 'vodi_movie_category_header' ) ) {
      function vodi_movie_category_header() {
      	$category_views = vodi_get_category_views();
    ?>
    <header class="section_header style-2">
        <?php if ( apply_filters( 'vodi_show_page_title', true ) ) : ?>
            <h1 class="section_title">Movies</h1>
        <?php endif; ?>

        <?php vodi_category_filters(); ?>

    </header>

    <?php
  }
}

if ( ! function_exists( 'vodi_movie_category_content' ) ) {
    function vodi_movie_category_content() {
    	$category_views = vodi_get_category_views();
		$data_view = 'grid';
	    foreach( $category_views as $category_view => $category_view_args) {
	        if ( $category_view_args['active'] ) {
	            $data_view = $category_view;
	            break;
	        }
	    }
     ?>
     <ul class="movies columns-5" data-toggle="categories" data-view="<?php echo esc_attr( $data_view ); ?>">
		<?php for( $i=0; $i<10; $i++): ?>
			<li>
            	<?php get_template_part( 'templates/contents/content', 'movie' ); ?>
            </li>
        <?php endfor; ?>
	</ul>

  	<?php
  	}
}

if ( ! function_exists( 'vodi_category_filters' ) ) {
    function vodi_category_filters() {
        $category_views = vodi_get_category_views();
    	?>
		<div class="videos-filters">
            <div class="videos-filters-left">
                <form class="woocommerce-ordering" method="get"> 
                    <select name="orderby" class="orderby">
                        <option value="popularity" selected="selected">Default</option>
                        <option value="rating">Default</option>
                        <option value="date">Default</option>
                        <option value="price">Default</option>
                        <option value="price-desc">Default</option> 
                    </select> 
                    <input type="hidden" name="paged" value="1">
                </form>

                <ul class="videos-type">
                    <li><a href="#" class="active">4K Ultra</a></li>
                    <li><a href="#">Premiers</a></li>
                </ul>
            </div>

            <div class="videos-filters-right">
                <ul class="category-view-switcher nav nav-tabs">
	                <?php foreach( $category_views as $view_id => $category_view ) : ?>
	                    <li class="nav-item"><a id="vodi-category-view-switcher-<?php echo esc_attr( $view_id );?>" class="nav-link <?php $active_class = $category_view[ 'active' ] ? 'active': ''; echo esc_attr( $active_class ); ?>" data-toggle="tab" data-archive-class="<?php echo esc_attr( $view_id );?>" title="<?php echo esc_attr( $category_view[ 'label' ] ); ?>" href="#vodi-category-view-content"><?php vodi_get_template( $category_view[ 'svg' ] ); ?></a></li>
	                <?php endforeach; ?>
                </ul>

                <form method="post" class="form-vodi-wc-ppp">
                    <select>
                        <option value="Custom">Show 30</option>
                        <option value="top-views">Show 20</option>
                        <option value="most-liked">Show 40</option>
                    </select>
                </form>

                <form method="post" class="vodi-advanced-pagination">
                    <select>
                        <option value="Custom">From A to Z</option>
                        <option value="top-views">Top Views</option>
                        <option value="most-liked">Most Liked</option>
                    </select>
                </form>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'vodi_video_category_header' ) ) {
      function vodi_video_category_header() {
    ?>
    <header class="section_header style-2">
        <?php if ( apply_filters( 'vodi_show_page_title', true ) ) : ?>
            <h1 class="section_title">Newest Episodes</h1>
        <?php endif; ?>
    </header>

    <?php vodi_category_filters(); ?>

    <?php
  }
}

if ( ! function_exists( 'vodi_video_category_content' ) ) {
    function vodi_video_category_content() {
    	$category_views = vodi_get_category_views();
		$data_view = 'grid';
	    foreach( $category_views as $category_view => $category_view_args) {
	        if ( $category_view_args['active'] ) {
	            $data_view = $category_view;
	            break;
	        }
	    }
     ?>
     <ul class="videos columns-5" data-toggle="categories" data-view="<?php echo esc_attr( $data_view ); ?>">
		<?php for( $i=0; $i<10; $i++): ?>
			<li>
            	<?php get_template_part( 'templates/contents/content', 'video' ); ?>
            </li>
        <?php endfor; ?>
	</ul>

  	<?php
  	}
}

