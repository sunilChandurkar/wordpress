<?php
/**
 * Plugin Name: Plug and Play
 * Plugin URI: http://localhost.com/wordpress/plug-and-play/
 * Description: This plugin adds some Facebook Open Graph tags to single posts plus a Menu Page and an Options Page
 * and some functions.
 * Version: 1.0.0
 * Author: Sunil Chandurkar
 * Author URI: http://www.ayasofyawebdesign.com
 * License: GPL2
 */

/***************ADD META TAGS FOR FB************/
// The wp_head action hook is triggered within the <head></head> 
// section of the user's template by the wp_head() function.

add_action('wp_head', 'my_facebook_tags');

/*
 * outputs meta tags for facebook
 * @return html meta tags
 */
function my_facebook_tags(){
  if( is_single() ) {
  ?>
    <meta property="og:title" content="<?php the_title() ?>" />
    <meta property="og:site_name" content="<?php bloginfo( 'name' ) ?>" />
    <meta property="og:url" content="<?php the_permalink() ?>" />
    <meta property="og:description" content="<?php the_excerpt() ?>" />
    <meta property="og:type" content="article" />
    
    <?php 
      if ( has_post_thumbnail() ) :
        $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' ); 
    ?>
      <meta property="og:image" content="<?php echo $image[0]; ?>"/>  
    <?php endif; ?>
    
  <?php
  }
}

/********************EMAIL NOTIFICATION ON NEW POST*************/

// the event is triggered when a post is published
add_action( 'publish_post', 'post_published_notification', 10, 2 );

/*
 * sends email to author
 * @param int $ID, id of the post
 * @param object $post, the post object
 */
function post_published_notification( $ID, $post ) {
    $email = get_the_author_meta( 'user_email', $post->post_author );
    $subject = 'Published ' . $post->post_title;
    $message = 'We just published your post: ' . $post->post_title . ' take a look: ' . get_permalink( $ID ); 
    wp_mail( $email, $subject, $message );
}

/****************Add Menu and Options Pages**********/
//This action is used to add extra submenus and menu options to the admin panel's 
//menu structure. It runs after the basic admin panel menu structure is in place.

add_action('admin_menu', 'my_plugin_menu');

//this function adds the menu and options pages
function my_plugin_menu() {

/*
 * adds the menu page
 * @param string page title
 * @param string menu title
 * @param string capability
 * @param string menu slug
 * @param function my_plugin_settings_page
 * @param icon
 */
    
add_menu_page('My Plugin Settings', 'Plugin Settings', 'administrator', 
        'my-plugin-settings', 'my_plugin_settings_page', 'dashicons-admin-generic');

/*
 * adds the options page
 * @param string page title
 * @param string menu title
 * @param string capability
 * @param string menu slug
 * @param function my_options_func
 */

add_options_page('My Options', 'My Options', 'administrator', 'my-options', 'my_options_func');
}

function my_plugin_settings_page() {
    
?>
<div class="wrap">
<h2>Setting Details</h2>
 
<form method="post" action="options.php">
    <?php settings_fields( 'my-plugin-settings-group' ); //print nonce, hidden fields etc. ?>
    <?php do_settings_sections( 'my-plugin-settings-group' ); ?>
    <table class="form-table">
    <tr valign="top">
        <th scope="row">Color Scheme</th>
        <td><input type="text" name="color_scheme" value="<?php echo esc_attr( get_option('color_scheme') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
 
</form>
</div>
<?php
}

// admin_init is triggered before any other hook when a user accesses the admin area. 
add_action( 'admin_init', 'my_plugin_settings' );

function my_plugin_settings() {
    // register settings that will be shown on the default WP settings pages
        register_setting( 'my-plugin-settings-group', 'color_scheme' );
}

/*
 * Usage in the Header
 * <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(). '/' . get_option('color_scheme') . '.css'; ?>">
 */

// Add the Options Page
function my_options_func(){
?>
<div class="wrap">
<h2>Setting Details</h2>
 
<form method="post" action="options.php">
    <?php settings_fields( 'my-options-page-group' ); ?>
    <?php do_settings_sections( 'my-options-page-group' ); ?>
    <table class="form-table">
       <tr valign="top">
        <th scope="row">Footer Notice</th>
        <td><input type="text" name="footer_notice" value="<?php echo esc_attr( get_option('footer_notice') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
 
</form>
</div>
<?php      
} //ends my_options_func

/*
 * admin_init is triggered before any other hook when a user accesses the admin area.
 * using it to register a new setting for use by a plugin
 */
add_action('admin_init', 'my_options_page_settings');

/*
 * registers settings with name value pairs
 * @ param none
 * @ return void
 */ 
 
function my_options_page_settings(){
    //register settings that will be shown on the default WP settings pages
    register_setting('my-options-page-group', 'footer_notice'); 
}

// Usage in footer.php echo get_option('footer_notice');

/******************Plugin Activation and Deactivation Functions*********/

//this function will be called on plugin activation
function on_my_plugin_activation(){
add_option('Activated_Plugin', 'Plug and Play is active.' );
}

register_activation_hook(__FILE__, 'on_my_plugin_activation');

function on_my_plugin_deactivation(){
    if (get_option('Activated_Plugin')) {

        delete_option('Activated_Plugin');

    }
    }

register_deactivation_hook(__FILE__, 'on_my_plugin_deactivation');
?>
