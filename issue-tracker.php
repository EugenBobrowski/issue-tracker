<?php

/*
Plugin Name: Issue Tracker
Plugin URI: http://wordpress.org/plugins/fields-for-all/
Description: This plugin require fields-for-all.
Author: Eugen Bobrowski
Version: 1.0
Author URI: http://atf.li
*/


class Issue_Tracker
{
    protected static $instance;


    private function __construct()
    {
        $this->load_structure();
        register_activation_hook(__FILE__, array($this, 'activation'));
        add_action('widgets_init', array($this, 'load_widgets'));
        add_action('init', array($this, 'load_shortcodes'));
        add_action('init', array($this, 'customize_comments'));
    }

    public function load_structure()
    {
        include_once plugin_dir_path(__FILE__) . 'issues-structure.php';
    }

    public function activation()
    {
        flush_rewrite_rules();
    }

    public function load_widgets()
    {
        require_once plugin_dir_path(__FILE__) . 'widget-toggl.php';
        register_widget('Issue_Tracker_Toggl_Widget');
    }

    public function load_shortcodes()
    {
        require_once plugin_dir_path(__FILE__) . 'shortcode-dashboard.php';
    }

    public function customize_comments()
    {
        require_once plugin_dir_path(__FILE__) . 'issue-comments.php';
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

add_action('plugins_loaded', array('Issue_Tracker', 'get_instance'));