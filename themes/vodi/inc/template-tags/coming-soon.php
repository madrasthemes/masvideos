<?php

if ( ! function_exists( 'vodi_comingsoon_site_content_section' ) ) {
    function vodi_comingsoon_site_content_section() {
	?><section class="cs-site-content-section">
        <div class="cs-site-content-section__subscribe-section">
            <div class="cs-site-content-section__caption">
                <h2 class="cs-site-content-section__title">Get Ready For Our<br> Vodi Launch</h2> 
                <p class="cs-site-content-section__sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit.<br> Amet quisquam fugiat ipsa.</p>
            </div>    
        </div>
        <div class="cs-site-content-section__timer-section deal-countdown-timer">
            <span class="deal-time-diff" style="display:none;"></span>
            <div class="deal-countdown countdown">
            </div>
        </div>
        <?php
        if( ! empty( $timer_value ) ) :
        $deal_end_time = strtotime( $timer_value );
        $current_time = strtotime( 'now' );
        $time_diff = ( $deal_end_time - $current_time );

        if( $time_diff > 0 ) : ?>
            <div class="cs-site-content__cs-timer-section deal-countdown-timer">
                <span class="deal-time-diff" style="display:none;"><?php echo esc_html( $time_diff ); ?></span>
                <div class="deal-countdown countdown">
                </div>
            </div>
            <?php endif;
            endif; ?>
            <div class="cs-site-content-section__vodi-subscribe-form">
                <form method="get" class="subscribe-form">
                    <input type="text" class="email" placeholder="Enter E-mail Address"/>
                    <button class="btn-subscribe">SUBSCRIBE</button>
                </form>
            </div>    
    </section>
	<?php
    }
}