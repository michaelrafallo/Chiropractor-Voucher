<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://michaelrafallo.wordpress.com/
 * @since      1.0.0
 *
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/includes
 * @author     RafnetCoder <michaelrafallo@gmail.com>
 */
class Chiropractor_voucher {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Chiropractor_voucher_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CHIROPRACTOR_VOUCHER_VERSION' ) ) {
			$this->version = CHIROPRACTOR_VOUCHER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'chiropractor_voucher';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Chiropractor_voucher_Loader. Orchestrates the hooks of the plugin.
	 * - Chiropractor_voucher_i18n. Defines internationalization functionality.
	 * - Chiropractor_voucher_Admin. Defines all hooks for the admin area.
	 * - Chiropractor_voucher_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chiropractor_voucher-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chiropractor_voucher-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-chiropractor_voucher-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-chiropractor_voucher-public.php';

		$this->loader = new Chiropractor_voucher_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Chiropractor_voucher_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Chiropractor_voucher_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Chiropractor_voucher_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		$this->loader->add_action( 'init', $plugin_admin, 'create_post_type' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'remove_yoast_seo_admin_filters', 20 );
		$this->loader->add_filter ( 'manage_edit-'.$this->plugin_name.'_columns', $plugin_admin, 'yoast_seo_remove_columns' );

		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'remove_row_actions', 10, 1 );

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'voucher_details_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_voucher_fields_meta' );
		
		$this->loader->add_action("manage_".$this->plugin_name."_posts_custom_column", $plugin_admin, "custom_columns");
		$this->loader->add_filter("manage_".$this->plugin_name."_posts_columns", $plugin_admin, "page_columns");

		$this->loader->add_filter("gform_confirmation", $plugin_admin, 'confirm_change', 10, 4);

		$this->loader->add_action( 'gform_delete_lead', $plugin_admin, 'delete_entry_post' );

		$this->loader->add_action( 'gform_entry_detail', $plugin_admin, 'add_to_details', 10, 2 );

		$this->loader->add_action( 'wp_ajax_my_ajax_request', $plugin_admin, 'generate_output' );
		$this->loader->add_action( 'wp_ajax_nopriv_my_ajax_request', $plugin_admin, 'generate_output' );
		$this->loader->add_action( 'wp_ajax_generate_output', $plugin_admin, 'generate_output' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Chiropractor_voucher_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Chiropractor_voucher_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
