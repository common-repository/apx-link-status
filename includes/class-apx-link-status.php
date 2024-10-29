<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    APx_Link_Status
 * @subpackage APx_Link_Status/includes
 * @author     Align Pixel <contact@alignpixel.com>
 */
class APx_Link_Status {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      APx_Link_Status_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Set the plugin name and the plugin version
	 * Load the dependencies, define the locale, and set the hooks for the admin area
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'APX_LINK_STATUS_VERSION' ) ) {
			$this->version = APX_LINK_STATUS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'apx-link-status';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Required dependencies for this plugin.
	 *
	 * - APx_Link_Status_Loader. Orchestrates the hooks of the plugin.
	 * - APx_Link_Status_i18n. Defines internationalization functionality.
	 * - APx_Link_Status_Admin. Defines all hooks for the admin area.
	 *
	 * Instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-apx-link-status-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-apx-link-status-i18n.php';

		/**
		 * The class responsible for defining all actions that occur 
		 * in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-apx-link-status-admin.php';

		$this->loader = new APx_Link_Status_Loader();

	}

	/**
	 * Locale for this plugin for internationalization.
	 *
	 * APx_Link_Status_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new APx_Link_Status_i18n();

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

		$plugin_admin = new APx_Link_Status_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'apx_link_status_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'apx_link_status_setting_init' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'apx_links_add_meta_box' );

		/*
		Show Link Status on Admin Column 
		*/
		foreach($plugin_admin->get_apx_link_status_setting['post_type'] as $get_apx_link_status_post_type ):
			$this->loader->add_filter( 'manage_'.$get_apx_link_status_post_type.'_posts_columns', $plugin_admin, 'apx_link_show_on_column' );
			$this->loader->add_action( 'manage_'.$get_apx_link_status_post_type.'_posts_custom_column', $plugin_admin, 'apx_link_show_on_column_result',10,2 );
		endforeach;

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
	 * @return    APx_Link_Status_Loader    Orchestrates the hooks of the plugin.
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
