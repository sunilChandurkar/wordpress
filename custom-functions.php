<?php

/* 
 * These are my own functions which will be added to functions.php
 */

/*
 * /wp-content/themes/twentyfifteen/sunils-functions.php
 * append or prepend a string to title
 */


add_filter('wp_title', 'add_sunil');

/*
 * @param string $title the title of the page
 * @return string
 */

function add_sunil($title){
    return $title . ' Sunil Chandurkar';
}

/*************Adding a Meta Box to a post***********/

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'smashing_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'smashing_post_meta_boxes_setup' );

/* Meta box setup function called when a new or existing post is loaded on the editor screen. */
function smashing_post_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. The hook allows meta box registration for any post type. */
  add_action( 'add_meta_boxes', 'smashing_add_post_meta_boxes' );
  
  /* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'smashing_save_post_class_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function smashing_add_post_meta_boxes() {

  add_meta_box(
    'smashing-post-class',      // Unique ID
    esc_html__( 'Post Class', 'example' ),    // Title
    'smashing_post_class_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
}

/* Display the post meta box. 
 * @param $object a post object
 * @param $box metabox array
 */

function smashing_post_class_meta_box( $object, $box ) { ?>

<?php wp_nonce_field( basename( __FILE__ ), 'smashing_post_class_nonce' ); ?>

<p>
<label for="smashing-post-class">
<?php _e( "Add a custom CSS class, which will be applied to WordPress' post class.", 'example' ); ?>
</label>
    <br />
<input class="widefat" type="text" name="smashing-post-class" id="smashing-post-class" value="<?php
echo esc_attr( get_post_meta( $object->ID, 'smashing_post_class', true ) ); 
?>" size="30" />
</p>
<?php }

/* Save the meta box's post metadata. */
function smashing_save_post_class_meta( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['smashing_post_class_nonce'] )
    || !wp_verify_nonce( $_POST['smashing_post_class_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_meta_value =
  ( isset( $_POST['smashing-post-class'] ) ? sanitize_html_class( $_POST['smashing-post-class'] ) : '' );

  /* Get the meta key. */
  $meta_key = 'smashing_post_class';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

/* Filter the post class hook with our custom post class function. */
add_filter( 'post_class', 'smashing_post_class' );

/*
 * adds to array of post classes
 * @param array of post classes $classes
 * @return array of post classes $classes
 */

function smashing_post_class( $classes ) {

  /* Get the current post ID. */
  $post_id = get_the_ID();

  /* If we have a post ID, proceed. */
  if ( !empty( $post_id ) ) {

    /* Get the custom post class. */
    $post_class = get_post_meta( $post_id, 'smashing_post_class', true );

    /* If a post class was input, sanitize it and add it to the post class array. */
    if ( !empty( $post_class ) )
      $classes[] = sanitize_html_class( $post_class );
  }

  return $classes;
}

/**************ON THEME ACTIVATION**********/

function sc_on_activation(){
// global $wpdb to create table and insert data
global $wpdb;
$sql = 'CREATE TABLE Persons
(
PersonID int unsigned not null auto_increment,
LastName varchar(255),
FirstName varchar(255),
Address varchar(255),
City varchar(255),
Primary Key (PersonID)
)';

$wpdb->query($sql);

// using $wpdb->insert( $table, $data, $format);
$table = 'Persons';
$data = array(
    'PersonID' => NULL, 
    'LastName' => 'Chandurkar', 
    'FirstName' => 'Sunil', 
    'Address' => '12 Ballard Avenue',
    'City' => 'Valley Stream'
            );
$format = array("%d", "%s", "%s", "%s", "%s");
$wpdb->show_errors();
$wpdb->insert($table, $data, $format);
}

// After theme has been switched to Sunil's twentyfifteen, the function 'sc_on_activation' will be called
add_action('after_switch_theme', 'sc_on_activation');

/***********ON THEME DEACTIVATION**************/

// drops table persons
function sc_on_deactivation(){
global $wpdb;
$wpdb->show_errors();
//drop table Persons
$wpdb->query("Drop table if exists Persons");
}

// When Sunil's twentyfifteen is deactivated the above function will be called

add_action('switch_theme', 'sc_on_deactivation');

//retrieve an option added in the plugin file

function sc_append_to_post($content){
      if(is_single()){
             if (get_option( 'Activated_Plugin')) {
         $content .= get_option('Activated_Plugin') . "<br>";                          
  }
}
return $content;
}

//add callback to the filter
add_filter('the_content', 'sc_append_to_post');

/************SHORTCODES****************/
/*
 * @return string
 */
function my_first_shortcode(){
   
$techie = '<a href="http://www.cnn.com/tech">Latest Tech News</a>';
   
    return $techie;
}

//shortcode name and callback function  
add_shortcode('tech-news', 'my_first_shortcode');
  
// use in post editor like this [tech-news]

/*************ENQUEUE SCRIPTS***********/
/*
 * Proper way to enqueue scripts
 */

function theme_name_scripts() {
/*
 * @param string name of script
 * @param string script src
 * @param string dependency
 * @param string version
 * @param boolean add script in footer true
 * @return void
 */
    
wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/validate.js', array('jquery'), '90210', true );
}

add_action('wp_enqueue_scripts', 'theme_name_scripts' );

/****************ADD WIDGETS******************/


add_action( 'widgets_init', 'register_my_widget' );

function register_my_widget(){
    register_widget('XWidget');
}

class XWidget extends WP_Widget {
    
//constructor    
function __construct() {
$widget_ops = array( 'classname' => 'xwidget', 'description' => __('A widget that displays the authors name ', 'twentyfifteen') );
$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'example-widget' );
$this->WP_Widget( 'example-widget', __('The XWidget', 'twentyfifteen'), $widget_ops, $control_ops );
    }
    
function widget( $args, $instance ) {
extract( $args );
//var_dump($args); die();
//Our variables from the widget settings.
$title = apply_filters('widget_title', $instance['title'] );
$name = $instance['name'];
$nickname = $instance['nickname'];
$show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;

echo $before_widget;

// Display the widget title 
if ( $title )
    echo $before_title . $title . $after_title;

//Display the name 
if ( $name )
    printf( '<p>' . __('Hey there Sailor! My name is %1$s.', 'example') . '</p>', $name );

if ( $show_info )
printf( $nickname );

echo $after_widget;
    }

//Update the widget 

function update( $new_instance, $old_instance ) {
$instance = $old_instance;

//Strip tags from title and name to remove HTML 
$instance['title'] = strip_tags( $new_instance['title'] );
$instance['name'] = strip_tags( $new_instance['name'] );
$instance['nickname'] = strip_tags( $new_instance['nickname'] );
$instance['show_info'] = $new_instance['show_info'];

return $instance;
    }


function form( $instance ) {

//Set up some default widget settings.
$defaults = array( 
    'title' => __('The XWidget', 'twentyfifteen'), 
    'name' => __('Sunil Chandurkar', 'twentyfifteen'), 
    'nickname' => __('PHP RockStar', 'twentyfifteen'),
    'show_info' => true 
    );

//merge the arrays
$instance = wp_parse_args( (array) $instance, $defaults ); ?>


<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'twentyfifteen'); ?></label>
<input id="<?php echo $this->get_field_id( 'title' ); ?>" 
name="<?php echo $this->get_field_name( 'title' ); ?>" 
value="<?php echo $instance['title']; ?>" style="width:100%;" />
</p>


<p>
<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Your Name:', 'twentyfifteen'); ?></label>
<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
</p>


<p>
<label for="<?php echo $this->get_field_id( 'nickname' ); ?>"><?php _e('Your Nickname:', 'twentyfifteen'); ?></label>
<input id="<?php echo $this->get_field_id( 'nickname' ); ?>" name="<?php echo $this->get_field_name( 'nickname' ); ?>" value="<?php echo $instance['nickname']; ?>" style="width:100%;" />
</p>


<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['show_info'], true ); ?> id="<?php echo $this->get_field_id( 'show_info' ); ?>" name="<?php echo $this->get_field_name( 'show_info' ); ?>" /> 
<label for="<?php echo $this->get_field_id( 'show_info' ); ?>"><?php _e('Display info publicly?', 'example'); ?></label>
</p>

<?php
}
}

