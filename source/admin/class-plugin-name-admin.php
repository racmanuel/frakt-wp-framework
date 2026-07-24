<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The unique prefix of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
     */
    private $plugin_prefix;

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
     * @param      string $plugin_name       The name of this plugin.
     * @param      string $plugin_prefix    The unique prefix of this plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct($plugin_name, $plugin_prefix, $version)
    {

        $this->plugin_name   = $plugin_name;
        $this->plugin_prefix = $plugin_prefix;
        $this->version       = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_styles($hook_suffix)
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/plugin-name-admin.css', [], $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_scripts($hook_suffix)
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/plugin-name-admin.js', ['jquery'], $this->version, false);

    }

    /**
     * Fixes the assets URL for Secure Custom Fields (SCF).
     *
     * Overrides the default URL used by SCF to serve assets,
     * pointing it to the custom location within this plugin.
     *
     * @param string $url The original assets URL.
     * @return string The overridden assets URL pointing to the plugin path.
     */
    public function scf_fix_assets_url($url)
    {
        return MY_SCF_URL;
    }

	/**
	 * Sets the save path for SCF JSON files.
	 *
	 * This method defines where SCF should save JSON field group definitions.
	 *
	 * @param string $path The default save path.
	 * @return string The custom save path inside the plugin.
	 */
    public function scf_json_save_point($path)
    {
        $path = plugin_dir_path(__FILE__) . '../scf-json';
        return $path;
    }

	/**
	 * Sets the load path for SCF JSON files.
	 *
	 * This method defines where SCF should look for existing JSON field group definitions.
	 *
	 * @param array $paths Array of default JSON load paths.
	 * @return array Modified array of load paths including the custom path.
	 */
    public function scf_json_load_point($paths)
    {
        unset($paths[0]); // Remove the default path.
        $paths[] = plugin_dir_path(__FILE__) . '../scf-json';
        return $paths;
    }

	/**
	 * Disables the Secure Custom Fields admin menu.
	 *
	 * This keeps the SCF menu hidden from the WordPress admin interface.
	 *
	 * @return bool False to prevent the menu from showing.
	 */
    public function scf_show_admin_menu()
    {
        return true;
    }

	/**
	 * Disables SCF update notifications.
	 *
	 * Prevents the plugin from displaying update messages or attempting to update.
	 *
	 * @return bool False to disable update checks.
	 */
    public function scf_show_updates()
    {
        return false;
    }
}
