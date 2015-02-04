<?php
/**
 * Plugin Name: My CSV Plugin
 * Plugin URI: http://localhost.com/wordpress/my-csv-plugin/
 * Description: This plugin adds some Facebook Open Graph tags to single posts plus a Menu Page and an Options Page
 * and some functions.
 * Version: 1.0.0
 * Author: Sunil Chandurkar
 * Author URI: http://www.ayasofyawebdesign.com
 * License: GPL2
 */

/**************PLUGIN DESCRIPTION*************************/
/*
 * This plugin does three things -
 * 1. Creates a custom post type called 'book'
 * 2. Creates a custom user meta field called user_security_field
 * 3. Downloads or displays a csv file containing post data.
 */

/*****************Create a Custom post type***********/
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'book',
    array(
      'labels' => array(
        'name' => __( 'Books' ),
        'singular_name' => __( 'Book' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
}

/*****************Create a Custom User Meta Field**************/
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

	<h3>User Security information</h3>

	<table class="form-table">

		<tr>
			<th><label for="user_security_field">User Security Field</label></th>

			<td>
				<input type="text" name="user_security_field" id="user_security_field" value="<?php echo esc_attr( get_the_author_meta( 'user_security_field', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Pet's name.</span>
			</td>
		</tr>

	</table>
<?php }

//Save the "user_security_field" data
add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
//this function can retrieve and save form data
	update_user_meta( $user_id, 'user_security_field', $_POST['user_security_field'] );
}

/*
 * This function Downloads or displays a csv file containing post data.
 * @return void
 */

function my_csv_export() {

//check if user is admin
if ( ! current_user_can( 'manage_options' ) ) {
    		die("Only Admins are allowed to download this file.");
	}

//Send Headers if query string variable "show" is not set.
// mime type is text/csv and file should be offered for download
    
if(! isset($_GET["show"])){
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=posts.csv');
        }

//Row Headings
$header_row = array(
		0 => 'Post Title',
		1 => 'Post Permalink',
		2 => 'Author Email',
                3 => 'Post Meta'
	);

// array of data rows
$data_rows = array();

//using the global $wpdb to access the database
global $wpdb;

$sql = "SELECT p1.ID, p1.post_author, p1.post_title, u1.user_email FROM wp_posts as p1 
join wp_users as u1 on
p1.post_author = u1.ID
WHERE p1.post_status ='draft' and p1.post_type ='book'";
//execute the query
$results = $wpdb->get_results( $sql );

//loop through the array
    foreach ( $results as $result ) {
        //print_r($result);
        
            $row = array();
            $row[0] = $result->post_title ;
            $row[1] = get_permalink($result->ID);
            $row[2] = esc_attr($result->user_email);
            $row[3] = esc_attr( get_the_author_meta( 'user_security_field', $result->post_author ));
            $data_rows[] = $row;
           }

//open the output stream as if it is a file
    $fh = fopen('php://output', 'w');

//write the header row
    fputcsv( $fh, $header_row );

    foreach ( $data_rows as $data_row ) {
        // write the data rows
            @fputcsv( $fh, $data_row );
    }
//close file handle
    fclose( $fh );
	
}

function example_add_rewrite_rules() {
//add url rewrite rules 
    add_rewrite_rule( 'library.csv', 'index.php?p=17', 'top' );
    add_rewrite_rule( 'library.csv?show=', 'index.php?p=17&show=1', 'top' );
    flush_rewrite_rules();
 
}


add_action( 'init', 'example_add_rewrite_rules' );
