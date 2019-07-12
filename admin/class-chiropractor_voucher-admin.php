<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://michaelrafallo.wordpress.com/
 * @since      1.0.0
 *
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/admin
 * @author     RafnetCoder <michaelrafallo@gmail.com>
 */
class Chiropractor_voucher_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chiropractor_voucher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chiropractor_voucher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chiropractor_voucher-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chiropractor_voucher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chiropractor_voucher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chiropractor_voucher-admin.js', array( 'jquery' ), date('Ymdhis'), false );

	}

	function create_post_type() {
		
		// https://www.wpbeginner.com/wp-tutorials/how-to-create-custom-post-types-in-wordpress/

		$singular_name = "Voucher";
		$plural_name   = "Vouchers";
		$slug		   = $this->plugin_name;

		$labels = array(
			"name" => $plural_name,
			"singular_name" => $singular_name,
			"menu_name" => "Chiropractor ".$plural_name,
			"all_items" => "All ".$singular_name,
	 		"add_new_item" => "Add New ".$singular_name,
	 		"edit_item" => "Edit ".$singular_name,
		);

		$args = array(
			"label" => $plural_name,
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"exclude_from_search" => true,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => $slug, "with_front" => true ),
			"query_var" => true,
			"menu_icon" => "dashicons-tag",
			"supports" => array( "title" ),
		);

		register_post_type( $slug, $args );

	}

	// Disabled SEO yoast when available on this post type
	function remove_yoast_seo_admin_filters() {
	    global $wpseo_meta_columns;

		$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

	    if ( $wpseo_meta_columns && $post_type == $this->plugin_name ) {
	        remove_action( 'restrict_manage_posts', array( $wpseo_meta_columns , 'posts_filter_dropdown' ) );
	        remove_action( 'restrict_manage_posts', array( $wpseo_meta_columns , 'posts_filter_dropdown_readability' ) );
	    }
	}

	// remove the Yoast SEO columns 
	function yoast_seo_remove_columns( $columns ) {
		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-title'] );
		unset( $columns['wpseo-metadesc'] );
		unset( $columns['wpseo-focuskw'] );
		unset( $columns['wpseo-score-readability'] );
		unset( $columns['wpseo-links'] );
		return $columns;
	}

	// Remove table row actions
	function remove_row_actions( $actions )
	{
		global $post;

	    if( get_post_type() === $this->plugin_name ) {
	        unset( $actions['view'] );	    		 
	        unset( $actions['inline hide-if-no-js'] );
	    }

	    return $actions;
	}

	function voucher_details_meta_box() {
		add_meta_box(
			'voucher_details', // $id
			'Voucher Details', // $title
			array($this, 'show_voucher_details_meta_box'), // $callback
			$this->plugin_name, // $screen
			'normal', // $context
			'high' // $priority
		);
	}

	function save_voucher_fields_meta( $post_id ) {

		if( isset($_POST['fields']) ) {
			foreach ($_POST['fields'] as $key => $value) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		if( isset($_POST['text_position']) ) {
			update_post_meta( $post_id, 'text_position', $_POST['text_position'] );		
		}

	}

	function show_voucher_details_meta_box() {
    	global $post, $wpdb;

    	$image 		     = get_post_meta( $post->ID, 'image', true ); 
    	$completion_page = get_post_meta( $post->ID, 'completion_page', true ); 
    	$expiration_days = get_post_meta( $post->ID, 'expiration_days', true ); 
    	$form 		     = get_post_meta( $post->ID, 'form', true ); 

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/voucher-details.php';
    }

	function page_columns($columns)
	{
	    $columns = array(
	        'cb'                => '<input type="checkbox" />',
	        'post_title'      	=> 'Name',
	        'expiration_days'   => 'Expiration Days',
	        'form'     			=> 'Form',
	        'completion_page'   => 'Completion Page',
	        'date'          	=> 'Date',
	    );
	    
	    return $columns;
	}

	function custom_columns($column)
	{
	    global $post, $wpdb;
	    
	    $columns = array(
	        'expiration_days' => 'Expiration Days',
	    );

	    if ( $column == 'post_title' ) {
	        echo $post->post_title;
	    } 

	    if ( $column == 'completion_page' ) {
		    $completion_page_id = get_post_meta( $post->ID, 'completion_page', true ); 
			$p = get_post( $completion_page_id);
			echo '<a href="'.admin_url('post.php?action=edit&post=').$completion_page_id.'">'.$p->post_title.'</a>';
	    } 

	    if ( $column == 'form' ) {
		    $form_id = get_post_meta( $post->ID, 'form', true ); 
			$p = GFAPI::get_form( $form_id );
			echo '<a href="'.admin_url('admin.php?page=gf_edit_forms&id=').$form_id.'">'.$p['title'].'</a>';
	    }
	    
	    foreach ($columns as $key => $value) {
	        if ( $column == $key ) {
	            echo get_post_meta( $post->ID, $key, true );
	        } 
	    }

	}

	function confirm_change($confirmation, $form, $lead, $ajax){

		$args = array(
			'post_type'		=> $this->plugin_name,
			'meta_query'	=> array(
				array(
					'key'   => 'form',
					'value'	=> $lead['form_id']
				)
			)
		);

		$voucher = new WP_Query( $args );

		$voucher_id = @$voucher->posts[0]->ID;

		if( $voucher_id ) {

	    	$completion_page = get_post_meta( $voucher_id, 'completion_page', true ); 
	    	$image 		     = get_post_meta( $voucher_id, 'image', true ); 
	    	$expiration_days = get_post_meta( $voucher_id, 'expiration_days', true ); 
			$text_position   = get_post_meta( $voucher_id, 'text_position', true ); 

			$uri = array( $lead['form_id'], $lead['id'], strtotime($lead['date_created']) );

		 	$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

			$filename = implode('-', $uri).'.'.$ext;

			// Blind filtering name in array
			ksort($lead);

			$filtered_name = array_filter($lead, function($v, $k) {
			  return (strpos($k, '.') !== false) && $v;
			}, ARRAY_FILTER_USE_BOTH);

			$data = array(
				'exp'  => date('m.d.Y', strtotime('+'.$expiration_days.' days')),
				'name' => ucwords(implode(' ', $filtered_name)),
				'text_position' => $text_position
			);

			$this->create_voucher($image, $filename, $data);

			parse_str(parse_url( $confirmation['redirect'], PHP_URL_QUERY), $uri);

			$params = array_merge( $uri, array( 'img' => $filename ) );
			$url = get_permalink($completion_page).'?'.http_build_query( $params );

		    $confirmation = array('redirect' => $url);

		}

	    return $confirmation;	   

	}

	function create_voucher($img, $filename, $data) {

		$folder = 'vouchers';
		$upload_dir = wp_upload_dir();
		$vouchers_dir = $upload_dir['basedir'].'/'.$folder;

	 	$ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

		if(!is_dir( $vouchers_dir )) {
			mkdir($vouchers_dir);
		}

		$font = WP_PLUGIN_DIR.'/'.$this->plugin_name.'/admin/font/Mont-HeavyDEMO.otf';

		// Path Images
		$im = ImageCreateFromJpeg($img);
		if( $ext == 'png') {
			$im = ImageCreateFromPng($img); 
		}

		// Text Color
		$color = ImageColorAllocate($im, 38, 38, 38); 
		
		$pxX = @$data['text_position']['name']['x'] ? $data['text_position']['name']['x'] : 1875; 
		$pxY = @$data['text_position']['name']['y'] ? $data['text_position']['name']['y'] : 735;
		$font_size = @$data['text_position']['name']['font_size'] ? $data['text_position']['name']['font_size'] : 40;
		$limit = @$data['text_position']['name']['limit'] ? $data['text_position']['name']['limit'] : 18;

		$name = $data['name'];
		if( strlen($name) > $limit ) {
	 		$name = substr( $name, 0, $limit) . '..';
		}

		ImagettfText($im, $font_size, 0, $pxX, $pxY, $color, $font, strtoupper($name));

		$pxX = @$data['text_position']['exp']['x'] ? $data['text_position']['exp']['x'] : 2070;
		$pxY = @$data['text_position']['exp']['y'] ? $data['text_position']['exp']['y'] : 1052;
		$font_size = @$data['text_position']['exp']['font_size'] ? $data['text_position']['exp']['font_size'] : 38;

		ImagettfText($im, $font_size, 0, $pxX, $pxY, $color, $font, $data['exp']);

		imagePng($im, $upload_dir['basedir']."/{$folder}/{$filename}");

		ImageDestroy($im);

		// tell the browser that the content is an image
		if( $ext == 'png') {
			header('Content-type: image/png');
		} else {
			header('Content-type: image/jpeg');		
		}
	}

	function delete_entry_post( $entry_id ) {
	 
	    //getting entry object
	    $entry = GFAPI::get_entry( $entry_id );

		$uri = array( $entry['form_id'], $entry['id'], strtotime($entry['date_created']) );
		
		$filename = implode('-', $uri);

		$folder = 'vouchers';
		$upload_dir = wp_upload_dir();
		$image = $upload_dir['basedir'].'/'.$folder.'/'.$filename.'.*';

		foreach( glob($image) as $img) {
			if( file_exists($img) ) {
				unlink($img);
			}
		}

	}

	function add_to_details( $form, $entry ) {
		if( $img = $this->get_voucher_image($entry) ) {
			echo '<b>Voucher</b>';
			echo '<div style="border: 2px dashed #9E9E9E;margin: 10px 0;padding:10px;background:#fff;"><img src="'.$img.'" style="width: 100%;"></div>';
		}
	}

	function get_voucher_image($entry) {
		$uri = array( $entry['form_id'], $entry['id'], strtotime($entry['date_created']) );

		$filename = implode('-', $uri);

		$folder = 'vouchers';
		$upload_dir = wp_upload_dir();
		$image = $upload_dir['basedir'].'/'.$folder.'/'.$filename.'.*';

		foreach( glob($image) as $img) {
			if( file_exists($img) ) {
				return str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $img);
			}
		}
	}

	function generate_output() {
		
		$image = $_POST['fields']['image'];

		$filename = end(explode('/', $image));

		$folder = 'vouchers';
		$upload_dir = wp_upload_dir();
		$op_img = 'wk-vouchers-output.'.strtolower(pathinfo($image, PATHINFO_EXTENSION));;
		$vouchers_dir = $upload_dir['baseurl'].'/'.$folder.'/'.$op_img.'?v='.date('YmdHis');

		$data = array(
			'exp'  => date('m.d.Y', strtotime('+'.$_POST['fields']['expiration_days'].' days')),
			'name' => 'Juan Dela Cruz Abante',
			'text_position' => $_POST['text_position']
		);

		$this->create_voucher($image, $op_img, $data);

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/voucher-output.php';
		exit;
	}

}

function voucher_func() {
	$folder = 'vouchers';
	$img = $_GET['img'];
	$upload_dir = wp_upload_dir();
	$image = $upload_dir['baseurl'].'/'.$folder.'/'.$img;

	if( $img && file_exists($upload_dir['basedir'].'/'.$folder.'/'.$img) ) {
		return '<div style="border: 2px dashed #9E9E9E;margin: 10px 0;padding:10px;background:#fff;"><a href="'.$image.'" download><img src="'.$image.'" width="100%;" style="display: block;border: 1px solid #9E9E9E;"></a></div>';
	} 
}
add_shortcode("voucher", 'voucher_func');

function load_media_files() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_media_files' );

add_filter('gform_form_args', 'no_ajax_on_all_forms', 10, 1);
function no_ajax_on_all_forms($args){
    $args['ajax'] = false;
    return $args;
}
