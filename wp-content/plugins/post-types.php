<?php
/*
 Plugin Name: Create Events Post Type
 Description: This plugin registers the 'events' post type
 Vesion: 1.0
 License: GPLv2
 */

global $post;


function dsadev_create_post_type(){
    //set up labels
    $labels = array(
        'name' => 'Events',
        'singular_name' => 'Event',
        'add_new' => 'Add New Event',
        'add_new_item' => 'Add New Event',
        'edit_item' => 'Edit Event',
        'new_item' =>   'New Event',
        'all_items' => 'All Events',
         'view_item' => 'View Events',
        'search_items' => 'Search Events',
        'not_found' => 'No Events Found',
        'not_found_in_trash' => 'No Events found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Events',
    );

    register_post_type('event', array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'supports' => array('title' ),
        'taxonomies' => array('post-tag', 'category'),
        'exclude_from_search' => false,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'events')));

}
add_action('init', 'dsadev_create_post_type');


function dsadev_event_add_metabox (){
    add_meta_box(
        'dsadev-event-metabox',
        __('Event details', 'dsadev'),
        'dsadev_echo_event_metabox',
        'event',
        'normal',
        'core'
    );
}

add_action('add_meta_boxes', 'dsadev_event_add_metabox');

function dsadev_echo_event_metabox($post){
    wp_nonce_field(basename(__FILE__), 'dsadev-event-detail-nonce');
    $event_start_date = get_post_meta($post->ID, 'event-start-date', true);
    $event_end_date = get_post_meta($post->ID, 'event-end-date', true);
    $event_place =   get_post_meta($post->ID, 'event-place', true);
    $event_start_date = ! empty($event_start_date) ? $event_start_date : time();
    $event_end_date = ! empty($event_end_date) ? $event_end_date : $event_start_date;
?>

    <label for="dsadev-event-start-date"><?php _e('Event Start Date:', 'dsadev') ?></label>
    <input class="" type="text" name="dsadev-event-start-date" placeholder="Format: April 05, 2018" value="<?php echo date('F d, Y', $event_start_date); ?>"></br></br>

    <label for="dsadev-event-end-date"><?php _e('Event End Date:', 'dsadev') ?></label>
    <input class="" type="text" name="dsadev-event-end-date" placeholder="Format: April 05, 2018" value="<?php echo date('F d, Y', $event_end_date); ?>"></br></br>

    <label for="dsadev-event-place"><?php _e('Event Place', 'dsadev') ?></label>
    <input class="" type="text" name="dsadev-event-place" placeholder="Dumskaya Sqaure" value="<?php echo  $event_place; ?>">

<?php }

function dsadev_save_event_info($post_id){

    if ('event' != $_POST['post_type']){
        return;
    }
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = (isset($_POST['dsadev-event-detail-nonce']) && (wp_verify_nonce($_POST['dsadev-event-detail-nonce'], basename(__FILE__))))? true : false;

    if($is_autosave || $is_revision || ! $is_valid_nonce){
        return;

    }

    if (isset($_POST['dsadev-event-start-date'])){
        update_post_meta($post_id, 'event-start-date', strtotime($_POST['dsadev-event-start-date']));

    }

    if (isset($_POST['dsadev-event-end-date'])){
        update_post_meta($post_id, 'event-end-date', strtotime($_POST['dsadev-event-end-date']));
    }

    if (isset ($_POST['dsadev-event-place'])){
        update_post_meta($post_id, 'event-place', sanitize_text_field($_POST['dsadev-event-place']));

    }


}
add_action('save_post', 'dsadev_save_event_info');

?>

