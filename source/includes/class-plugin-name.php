<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
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
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
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
     * The unique prefix of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
     */
    protected $plugin_prefix;

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
    public function __construct()
    {

        if (defined('PLUGIN_NAME_VERSION')) {

            $this->version = PLUGIN_NAME_VERSION;

        } else {

            $this->version = '1.0.0';

        }

        $this->plugin_name   = 'plugin-name';
        $this->plugin_prefix = 'pfx_';

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
     * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
     * - Plugin_Name_i18n. Defines internationalization functionality.
     * - Plugin_Name_Admin. Defines all hooks for the admin area.
     * - Plugin_Name_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * Load Composer's autoloader to register PSR-4 classes and any files defined in `composer.json`.
         *
         * This is essential to load dependencies installed via Composer.
         * The path is relative to this file's directory.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';

        /**
         * Load the Secure Custom Fields plugin manually if it's not already loaded.
         *
         * Checks if the ACF (Secure Custom Fields) class is already defined.
         * If not, defines constants for its path and URL, then includes the main plugin file.
         */
        if (! class_exists('ACF')) {

            /**
             * Define the absolute filesystem path to the SCF plugin directory.
             *
             * @constant MY_SCF_PATH
             */
            define('MY_SCF_PATH', plugin_dir_path(dirname(__FILE__)) . 'vendor/secure-custom-fields/');

            /**
             * Define the URL path to the SCF plugin directory.
             *
             * @constant MY_SCF_URL
             */
            define('MY_SCF_URL', plugin_dir_url(dirname(__FILE__)) . 'vendor/secure-custom-fields/');

            /**
             * Include the main plugin file to bootstrap Secure Custom Fields functionality.
             */
            require_once MY_SCF_PATH . 'secure-custom-fields.php';
        }

        /**
         * Load development-only plugins when debugging is enabled.
         *
         * This block ensures that tools like Query Monitor, WP Crontrol,
         * User Switching, and Plugin Check are only loaded in environments
         * where WP_DEBUG is set to true. This prevents unnecessary resource
         * usage or exposure of sensitive tools in production.
         */
        if (defined('WP_DEBUG') && WP_DEBUG) {

            /**
             * Load Query Monitor if it's not already loaded.
             * Useful for debugging SQL queries, hooks, and performance.
             */
            if (! class_exists('QueryMonitor')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/query-monitor/query-monitor.php';
            }

            /**
             * Load WP Crontrol if the constant is not already defined.
             * This allows inspection and management of WP-Cron events.
             */
            if (! defined('WP_CRONTROL_VERSION')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/wp-crontrol/wp-crontrol.php';
            }

            /**
             * Load User Switching if the class is not already loaded.
             * Enables quickly switching between user accounts in admin.
             */
            if (! class_exists('user_switching')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/user-switching/user-switching.php';
            }

            /**
             * Load Plugin Check if the version constant is not yet defined.
             * Provides tools to validate plugin requirements and versions.
             */
            if (! defined('WP_PLUGIN_CHECK_VERSION')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/plugin-check/plugin.php';
            }
        }

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugin-name-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugin-name-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plugin-name-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-plugin-name-public.php';

        $this->loader = new Plugin_Name_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Plugin_Name_I18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Plugin_Name_Admin($this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Hook to modify SCF asset URL
        $this->loader->add_filter('acf/assets_url', $plugin_admin, 'scf_fix_assets_url');

        // Hook to define the path where JSON field groups are saved
        $this->loader->add_filter('acf/settings/save_json', $plugin_admin, 'scf_json_save_point');

        // Hook to define the path(s) from where JSON field groups are loaded
        $this->loader->add_filter('acf/settings/load_json', $plugin_admin, 'scf_json_load_point');

        // Hook to disable SCF admin menu
        $this->loader->add_filter('acf/settings/show_admin', $plugin_admin, 'scf_show_admin_menu');

        // Hook to disable SCF plugin update notifications
        $this->loader->add_filter('acf/settings/show_updates', $plugin_admin, 'scf_show_updates');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Plugin_Name_Public($this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Shortcode name must be the same as in shortcode_atts() third parameter.
        $this->loader->add_shortcode($this->get_plugin_prefix() . 'shortcode', $plugin_public, 'pfx_shortcode_func');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The unique prefix of the plugin used to uniquely prefix technical functions.
     *
     * @since     1.0.0
     * @return    string    The prefix of the plugin.
     */
    public function get_plugin_prefix()
    {
        return $this->plugin_prefix;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
