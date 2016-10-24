<?php


class Issue_Tracker_Shortcode_Dashboard
{

    protected static $instance;

    private function __construct()
    {
        add_shortcode('issues_dashboard', array($this, 'shortcode'));
    }

    public function shortcode($attr)
    {

        $issuers_query = new WP_Query(array('post_type' => 'issues'));

        ob_start();

        ?>
        <div class="issues-dashboard">
            <?php

            if ($issuers_query->have_posts()) { ?>
                <table class="issues-list">
                    <thead>
                    <tr>
                        <td>#</td>
                        <th><?php _e('Issue', 'issue-tracker'); ?></th>
                        <th class="project-column "><?php _e('Project', 'issue-tracker'); ?></th>
                    </tr>
                    </thead><?php
                    while ($issuers_query->have_posts()) {
                        $issuers_query->the_post();
                        $meta = get_post_meta(get_the_ID(), 'issues_meta', true);
                        $projects = get_the_term_list(get_the_ID(), 'issues_project');
                        ?>
                        <tr>
                            <th><?php echo get_the_ID(); ?></th>
                            <td>
                                <i class="priority-icon <?php echo $meta['priority']; ?>" data-priority="<?php echo $meta['priority']; ?>"></i>
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                            <td class="project-column issue-projects-list"><?php echo get_the_term_list(get_the_ID(), 'issues_project', '', ' ', ''); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
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


    public function check_required_plugins()
    {
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

