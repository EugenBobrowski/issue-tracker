<?php

class Issues_Comments
{
    protected static $instance;

    private function __construct()
    {
        add_filter('comment_form_field_comment', array($this, 'add_fields'));


    }


    public function add_fields($comment_field)
    {
        $args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
        $html5    = 'html5' === $args['format'];

        $comment_field .= '<p class="comment-form-url"><label for="url">' . __('Time') . '</label> ' .
            '<input id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="" size="30" maxlength="200" /></p>';
        return $comment_field;
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Issues_Comments::get_instance();
