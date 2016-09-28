<?php



class Issue_Tracker_Shortcode_Dashboard {

    protected static $instance;

    private function __construct()
    {
        add_shortcode('issues_dashboard', array($this, 'shortcode'));
    }

    public function shortcode ($attr) {

        wp_enqueue_style('issues-front', plugin_dir_url(__FILE__) . 'assets/issues-front.css');

        $issuers_query = new WP_Query( array('post_type'=> 'issues') );

        ob_start();

        ?>
        <div class="issues-dashboard">
        <?php

        if ( $issuers_query->have_posts() ) { ?>
            <ul class="issues-list"> <?php
            while ( $issuers_query->have_posts() ) {
                $issuers_query->the_post();

                ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php
            }
            ?>
            </ul>
            <?php
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            // no posts found
        }

        ?>
        </div>
        <?php

        return ob_get_clean();
    }



    public function check_required_plugins () {
        return in_array('fields-for-all/fields-for-all.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Issue_Tracker_Shortcode_Dashboard::get_instance();

