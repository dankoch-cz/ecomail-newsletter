<?php
/*
		Plugin Name: Ecomail Newsletter
		Plugin URI:  https://github.com/adam-laita/ecomail-newsletter/
		Description: Ecomail Newsletter plugin.
		Version:     1.0.0
		Author:      Daniel Koch, Adam Laita
		Author URI:  https://github.com/adam-laita/ecomail-newsletter/
		License:     GPL3
		License URI: https://www.gnu.org/licenses/gpl-3.0.html
		Text Domain: klen
		Domain Path: /languages
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Path, directory & basename
define('KLEN_PATH_DIR', plugin_dir_path(__FILE__));
define('KLEN_PATH_URL', plugin_dir_url(__FILE__));
define('KLEN_BASENAME', plugin_basename(__FILE__));

class KLEN_Ecomail
{

	/**
	 *
	 */
	public function __construct()
	{
		$this->actions();
		$this->load_plugin_files();
		$this->admin_settings_fields();
	}

	/**
	 * List all actions of the plugin
	 *
	 * @return void
	 */
	private function actions()
	{
		add_action( 'wp_enqueue_scripts', array($this, 'register_frontend_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'register_admin_scripts') );
		add_action( 'admin_menu', array($this, 'create_admin_page') );
		add_action( 'admin_init', array( $this, 'language_textdomain' ) );
	}

	/**
	 * Adds textdomain for translations.
	 *
	 * @return void
	 */
	public function language_textdomain()
	{
		load_plugin_textdomain( 'klen', false, dirname( KLEN_BASENAME ) . '/languages' );
	}

	/**
	 * @return void
	 */
	private function load_plugin_files()
	{
		require_once KLEN_PATH_DIR . '/admin/settings/labels.php';
		require_once KLEN_PATH_DIR . '/admin/settings/appearance.php';
		require_once KLEN_PATH_DIR . '/admin/settings/main.php';
		require_once KLEN_PATH_DIR . '/includes/request.php';
		require_once KLEN_PATH_DIR . '/includes/shortcode.php';
		//require_once KLEN_PATH_DIR . '/includes/updater.php';
	}

	/**
	 * Register all frontend scripts used in plugin
	 *
	 * @return void
	 */
	public function register_frontend_scripts()
	{
		$plugin_meta = get_plugin_data(__FILE__);

		// Styles for Ecomail form
		wp_register_style('klen_form', KLEN_PATH_URL . '/assets/css/klen-form.css', null, $plugin_meta['Version'], 'all');

		// Scripts for Ecomail form
		wp_register_script('klen_form', KLEN_PATH_URL . '/assets/js/klen-form.js', null, $plugin_meta['Version'], true);
	}

	/**
	 * Register all admin scripts used in plugin
	 *
	 * @return void
	 */
	public function register_admin_scripts()
	{
		$plugin_meta = get_plugin_data(__FILE__);

		$style = get_option('klen_appearance_style');

		if ( empty( $style ) || $style == 'default' ) {
			// Styles for Ecomail form
			wp_enqueue_style('klen_form', KLEN_PATH_URL . '/assets/css/klen-form.css', null, $plugin_meta['Version'], 'all');
		}

		$current_admin_page = get_current_screen();

		if ( $current_admin_page->base == 'settings_page_klen_admin_page' ) {
			// Styles for admin
			wp_enqueue_style('klen_admin', KLEN_PATH_URL . '/assets/css/klen-admin.css', null, $plugin_meta['Version'], 'all');
		}

		// Scripts for Ecomail form
		wp_enqueue_script('klen_form', KLEN_PATH_URL . '/assets/js/klen-form.js', null, $plugin_meta['Version'], true);
	}

	/**
	 * Create admin menu page
	 *
	 * @return void
	 */
	public function create_admin_page()
	{
		add_submenu_page(
			'options-general.php',
			__('Ecomail Newsletter', 'klen'),
			__('Ecomail Newsletter', 'klen'),
			'manage_options',
			'klen_admin_page',
			array($this, 'admin_page_template')
		);
	}

	/**
	 * Admin menu page labels callback
	 *
	 * @return void
	 */
	public function admin_page_template()
	{
		require_once KLEN_PATH_DIR . '/admin/settings/template.php';
	}

	public function admin_settings_fields()
	{

		// Register a new settings main tab
		register_setting('klen_main', 'klen_api_key');
		register_setting('klen_main', 'klen_list_id');
		register_setting('klen_main', 'klen_subscribers_count');

		// Register a new settings labels tab
		register_setting('klen_labels', 'klen_labels_title');
		register_setting('klen_labels', 'klen_labels_desc');
		register_setting('klen_labels', 'klen_labels_label');
		register_setting('klen_labels', 'klen_labels_placeholder');
		register_setting('klen_labels', 'klen_labels_button');
		register_setting('klen_labels', 'klen_labels_success');
		register_setting('klen_labels', 'klen_labels_error');
		register_setting('klen_labels', 'klen_labels_warning');

		// Register a new settings appearance tab
		register_setting('klen_appearance', 'klen_appearance_style');
	}

}

new KLEN_Ecomail();