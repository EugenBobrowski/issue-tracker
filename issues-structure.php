<?php


class Issues_Structure
{
    protected static $instance;

    private function __construct()
    {

        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_project_taxonomy'));
        add_action('init', array($this, 'register_client_taxonomy'));

        if ($this->check_required_plugins()) $this->issue_details();


    }

    public function register_post_type()
    {
        $args = array(
            'public' => true,
            'label' => 'Issues',
            'menu_icon' => 'dashicons-clipboard',
            'menu_position' => 3,
            'capability_type' => 'page',
            'hierarchical' => true,
            'supports' => array('title', 'editor', 'author', 'page-attributes', 'comments'),
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
                    'name' => _x('Projects', 'taxonomy general name', 'textdomain'),
                    'singular_name' => _x('Project', 'taxonomy singular name', 'textdomain'),
                    'search_items' => __('Search Projects', 'textdomain'),
                    'popular_items' => __('Popular Projects', 'textdomain'),
                    'all_items' => __('All Projects', 'textdomain'),
                    'parent_item' => null,
                    'parent_item_colon' => null,
                    'edit_item' => __('Edit Project', 'textdomain'),
                    'update_item' => __('Update Project', 'textdomain'),
                    'add_new_item' => __('Add New Project', 'textdomain'),
                    'new_item_name' => __('New Project Name', 'textdomain'),
                    'separate_items_with_commas' => __('Separate Projects with commas', 'textdomain'),
                    'add_or_remove_items' => __('Add or remove Projects', 'textdomain'),
                    'choose_from_most_used' => __('Choose from the most used Projects', 'textdomain'),
                    'not_found' => __('No Projects found.', 'textdomain'),
                    'menu_name' => __('Projects', 'textdomain'),
                ),
                'rewrite' => array('slug' => 'project'),
                'hierarchical' => true,
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
            )
        );
    }

    public function register_client_taxonomy()
    {
        register_taxonomy(
            'issues_clients',
            'issues',
            array(
                'label' => __('Clients'),
                'labels' => array(
                    'name' => _x('Clients', 'taxonomy general name', 'textdomain'),
                    'singular_name' => _x('Client', 'taxonomy singular name', 'textdomain'),
                    'search_items' => __('Search Clients', 'textdomain'),
                    'popular_items' => __('Popular Clients', 'textdomain'),
                    'all_items' => __('All Clients', 'textdomain'),
                    'parent_item' => null,
                    'parent_item_colon' => null,
                    'edit_item' => __('Edit Client', 'textdomain'),
                    'update_item' => __('Update Client', 'textdomain'),
                    'add_new_item' => __('Add New Client', 'textdomain'),
                    'new_item_name' => __('New Client Name', 'textdomain'),
                    'separate_items_with_commas' => __('Separate Clients with commas', 'textdomain'),
                    'add_or_remove_items' => __('Add or remove Clients', 'textdomain'),
                    'choose_from_most_used' => __('Choose from the most used Clients', 'textdomain'),
                    'not_found' => __('No Clients found.', 'textdomain'),
                    'menu_name' => __('Clients', 'textdomain'),
                ),
                'rewrite' => array('slug' => 'client'),
                'hierarchical' => true,
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
            )
        );
    }

    public function issue_details()
    {
        new Atf_Metabox('issues_meta', 'Issue Details', 'issues', array(
            'priority' => array(
                'title' => __('Priority'),
                'type' => 'select',
                'options' => array(
                    'blocker' => 'blocker',
                    'critical' => 'critical',
                    'major' => 'major',
                    'minor' => 'minor',
                    'trivial' => 'trivial',
                ),
                'default' => 'major'
            ),
            'date' => array(
                'title' => __('Deadline'),
                'type' => 'datepicker',
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
