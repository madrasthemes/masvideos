<?php
/**
 * Template functions in Home v6
 *
 */
if ( ! function_exists( 'vodi_page_control_bar_bottom' ) ) {
    function vodi_page_control_bar_bottom() {
        ?><div class="page-control-bar-bottom">
		    <form method="POST" class="form-vodi-wc-ppp">
		        <select>
		            <option value="20" selected="selected">Show 20</option>
		            <option value="40">Show 40</option>
		            <option value="-1">Show All</option>
		        </select>
		    </form>
		    <p class="result-count">
		        Showing 1–20 of 73 results
		    </p>
		    <nav class="pagination">
		        <ul class="page-numbers">
		            <li>
		                <span aria-current="page" class="page-number current">1</span>
		            </li>
		            <li>
		                <a class="page-number" href="#">2</a>
		            </li>
		            <li>
		                <a class="page-number" href="#">3</a>
		            </li>
		            <li>
		                <a class="page-number" href="#">4</a>
		            </li>
		            <li>
		                <a class="page-number" href="#">5</a>
		            </li>
		            <li>
		                <a class="next page-number" href="#">Next page →</a>
		            </li>
		        </ul>
		    </nav>
		</div>
        <?php
    }
}

if ( ! function_exists( 'vodi_home_v6_video_section' ) ) {
    function vodi_home_v6_video_section() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v6_video_section_default_args', array(
                'section_title'         => esc_html__( ' Featured TV Series', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'Today', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'This week', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'This month', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Last 3 months', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => '',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '4',
                    'limit'                 => '8',
                ),
            ) );

            vodi_video_section( $args );
        }
    }
}
