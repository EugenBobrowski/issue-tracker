<?php


class Issues_Structure
{
    protected static $instance;

    private function __construct()
    {

        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_project_taxonomy'));

        if ($this->check_required_plugins()) $this->issue_details();


    }

    public function register_post_type()
    {
        $args = array(
            'public' => true,
            'label' => 'Issues',
            'menu_icon' => 'dashicons-clipboard',
            'supports' => array('title'),

        );
        register_post_type('issues', $args);
    }

    public function register_project_taxonomy()
    {
        register_taxonomy(
            'issues_project',
            'issues',
            array(
                'label' => __('Project'),
                'labels' => array(
                    'name'                       => _x( 'Projects', 'taxonomy general name', 'textdomain' ),
                    'singular_name'              => _x( 'Project', 'taxonomy singular name', 'textdomain' ),
                    'search_items'               => __( 'Search Projects', 'textdomain' ),
                    'popular_items'              => __( 'Popular Projects', 'textdomain' ),
                    'all_items'                  => __( 'All Projects', 'textdomain' ),
                    'parent_item'                => null,
                    'parent_item_colon'          => null,
                    'edit_item'                  => __( 'Edit Project', 'textdomain' ),
                    'update_item'                => __( 'Update Project', 'textdomain' ),
                    'add_new_item'               => __( 'Add New Project', 'textdomain' ),
                    'new_item_name'              => __( 'New Project Name', 'textdomain' ),
                    'separate_items_with_commas' => __( 'Separate Projects with commas', 'textdomain' ),
                    'add_or_remove_items'        => __( 'Add or remove Projects', 'textdomain' ),
                    'choose_from_most_used'      => __( 'Choose from the most used Projects', 'textdomain' ),
                    'not_found'                  => __( 'No Projects found.', 'textdomain' ),
                    'menu_name'                  => __( 'Projects', 'textdomain' ),
                ),
                'rewrite' => array('slug' => 'project'),
                'hierarchical' => true,
                'show_ui' => false,
            )
        );
    }

    public function issue_details()
    {
        new Atf_Metabox('issues_meta', 'Issue Details', 'Issues', array(
            'date' => array(
                'title' => __('Date of Race'),
                'type' => 'text',
            ),
            'state' => array(
                'title' => __('State'),
                'type' => 'text',
            ),
            'total_participants_men' => array(
                'title' => __('Total Participants men'),
                'type' => 'text',
            ),
            'total_participants_women' => array(
                'title' => __('Total Participants Women'),
                'type' => 'text',
            ),
            'total_participants' => array(
                'title' => __('total number of participants'),
                'type' => 'text',
            ),
            'avg_finishing_time' => array(
                'title' => __('average finishing time for all participants'),
                'type' => 'text',
            ),
            'fastest_finishing_time_men' => array(
                'title' => __('fastest finishing time men'),
                'type' => 'text',
            ),
            'fastest_finishing_time_women' => array(
                'title' => __('fastest finishing time women'),
                'type' => 'text',
            ),
            'entry_fee' => array(
                'title' => __('entry fee to run the race (none members)'),
                'type' => 'text',
            ),
            'finishers_region' => array(
                'title' => __('Finisher\'s region of origin'),
                'type' => 'text',
            ),
            'gender' => array(
                'title' => __('Gender'),
                'type' => 'text',
            ),
            'temperature_the_day' => array(
                'title' => __('temperature the day of marathon'),
                'type' => 'text',
            ),
            'humidity_the_day' => array(
                'title' => __('humidity the day of marathon'),
                'type' => 'text',
            ),
            'aid_stations_number' => array(
                'title' => __('number of aid stations'),
                'type' => 'text',
            ),
            'avg_altitude' => array(
                'title' => __('Avg. altitude for marathon'),
                'type' => 'text',
            ),
            'dnf_number' => array(
                'title' => __('number of DNF\'s in a race'),
                'type' => 'text',
            ),
            'fastest_marathon' => array(
                'title' => __('fastest marathon?'),
                'type' => 'text',
            ),
            'state_with_most_marathon' => array(
                'title' => __('state with most marathons'),
                'type' => 'text',
            ),
            'pace_groups_number' => array(
                'title' => __('number of pace groups'),
                'type' => 'text',
            ),

        ));
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

Issues_Structure::get_instance();
