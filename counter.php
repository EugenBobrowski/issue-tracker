<?php

class Counter
{
    protected static $instance;

    private function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_action('wp_footer', [$this, 'modal']);
        add_action('wp_ajax_track_time', array($this, 'ajax_callback'));
        add_action('wp_ajax_nopriv_track_time', array($this, 'ajax_callback'));
    }

    public function assets()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('time-tacking-js', plugin_dir_url(__FILE__) . 'assets/counter.js', ['jquery', 'jquery-ui-draggable'], '1.0' . time(), true);
        wp_localize_script('time-tacking-js', 'time_tracking', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tracktime')));
    }

    public function ajax_callback () {

        check_admin_referer('tracktime');

        $time = absint($_POST['time']);
        $desc = sanitize_text_field($_POST['desc']);
        $issue = absint($_POST['issue']);
        $current_user = wp_get_current_user();

        if (!$current_user->exists())
            wp_send_json('logged out');

        if (empty($time))
            wp_send_json('empty time');

        $commentdata['comment_author']       = $current_user->display_name;
        $commentdata['comment_author_email'] = $current_user->user_email;
        $commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
        $commentdata['user_id'] = $current_user->ID;
        $commentdata['comment_post_ID']  = $issue;
        $commentdata['comment_content']  = $desc;
        $commentdata['comment_type']     = 'time';
        $commentdata['comment_parent']   = 0;

        $comment_id = wp_insert_comment($commentdata);

        update_comment_meta($comment_id, 'time', $time);

        echo $comment_id;

        exit();

    }

    public function modal()
    {
        global $wpdb;
        $current_user = wp_get_current_user();
        ?>
        <div class="time-tacking-modal">
            <div class="overlay"></div>
            <div class="modal-body">
                <div class="handle"></div>
                <a href="#" class="minimize"></a>
                <div class="input-group">
                    <div class="input-group-addon">
                        <input type="text" class="form-control time-field" name="time-field" placeholder="2.5h" style="width: 120px;">
                    </div>
                    <input type="text" class="form-control description-field" name="description-field" placeholder="<?php _e('What are you working on?'); ?>">

                    <span class="input-group-btn">
                        <button class="btn btn-default start-stop" type="button" data-start="<?php _e('Start'); ?>"
                                data-stop="<?php _e('Stop'); ?>"><?php _e('Start'); ?></button>
                    </span>
                </div><!-- /input-group -->

                <table class="time-entries-table">
                    <thead>
                    <th class="time-item-id"     >ID</th>
                    <th class="time-item-message">Message</th>
                    <th class="time-item-time"   >Time</th>
                    </thead>
                    <tbody>
                    <?php

                    $times = $wpdb->prepare("
SELECT comments.`comment_ID`, comments.`comment_content`, meta.`meta_value` 
FROM {$wpdb->comments} AS comments JOIN {$wpdb->commentmeta} AS meta ON comments.`comment_ID` = meta.`comment_ID`
WHERE meta.`meta_key` = 'time' AND comments.`comment_type` = 'time' AND comments.`user_id` = %d 
ORDER BY comments.`comment_date` DESC", $current_user->ID);
                    $times = $wpdb->get_results($times);

//                    var_dump($times);

//                    $times = get_comments([
//                        'author__in' => $current_user->ID,
//                        'type' => 'time']);


                    foreach ($times as $comment) {
                        ?>
                        <tr>
                            <td class="time-item-id"     ><?php echo $comment->comment_ID; ?></td>
                            <td class="time-item-message"><?php echo $comment->comment_content; ?></td>
                            <td class="time-item-time"   ><?php echo time_to_string(get_comment_meta($comment->comment_ID, 'time', true)); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td class="no-time-items" colspan="3" style="<?php if (!empty($times)) echo 'display: none; '?>">No time tracked today</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <td class="time-item-id"      >ID</td>
                    <th class="time-item-message" >Message</th>
                    <th class="time-item-time"    >Time</th>
                    </tfoot>
                </table>

            </div>
        </div>
        <?php
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Counter::get_instance();


function time_to_string ($sec) {
    $string = '';

    // calculate (and subtract) whole days
    $days = floor($sec / 28800);
    $sec -= $days * 28800;
    if ($days > 0) $string .= $days . 'd';

    // calculate (and subtract) whole hours
    $hours = floor($sec / 3600) % 24;
    $sec -= $hours * 3600;
    if (($hours > 0) || ($days > 0)) $string .= $hours . 'h';

    // calculate (and subtract) whole minutes
    $minutes = floor($sec / 60) % 60;
    $sec -= $minutes * 60;
    if ($minutes > 0 || $hours > 0 || $days > 0) $string .= $minutes . 'm';

    // what's left is $seconds
    $seconds = intval($sec) % 60;
    $string .= $seconds . 's';

    return $string;
}
