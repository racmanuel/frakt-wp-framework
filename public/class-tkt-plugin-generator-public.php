<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Plugin_Generator
 * @subpackage Tkt_Plugin_Generator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, enqueues styles and builds form.
 * Gets, creates, renames and populates new Plugin files.
 * Downloads files.
 *
 * @todo Delete Files.
 *
 * @package    Tkt_Plugin_Generator
 * @subpackage Tkt_Plugin_Generator/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_Plugin_Generator_Public
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
     * @param      string $plugin_name       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tkt-plugin-generator-public.css', [], $this->version, 'all');

    }

    /**
     * Register the ShortCode.
     *
     * @since 1.0.0
     */
    public function add_shortcode()
    {

        if (! is_admin() && (! defined('DOING_AJAX') || ! DOING_AJAX)) {
            add_shortcode('generate_plugin', [$this, 'generate_plugin_form']);
        }

    }

    /**
     * ShortCode to show the Generate Form.
     *
     * @since 1.0.0
     * @param array $atts The ShortCode attributes.
     */
    public function generate_plugin_form($atts)
    {

        ob_start();

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/tkt-plugin-generator-public-display.php';

        $output = ob_get_clean();

        return $output;

    }

    /**
     * Create new Folder with files,
     * Replace all strings and rename files,
     * Zip new folder and Download.
     *
     * @since 1.0.0
     * @param array $new_data Array with new data to use for replace.
     */
    public function replace_zip_and_download($new_data)
    {

        // Validate POST and Nonce.
        if (false === $this->validate_post_and_nonce()) {
            return;
        }

        // Get New POSTed data.
        $new_data = $this->get_new_data();

        // Build all necessary variables.
        $source    = plugin_dir_path(__DIR__) . 'source';
        $filename  = $new_data['plugin_file_name'];
        $zip_name  = $new_data['plugin_file_name'] . '.zip';
        $orig_path = plugin_dir_path(__DIR__) . 'builds/' . $filename;
        $zip_path  = plugin_dir_path(__DIR__) . 'builds/' . $zip_name;

        // Create a copy of the source files to the new source.
        $this->create_source_copy($source, $orig_path, 0755);

        // Find all files in the new source.
        $files = $this->find_all_files($orig_path);

        foreach ($files as $file) {

            // Replace all strings (including filenames) in the new source.
            $this->replace_names($file, $new_data);

        }

        // 🧩 Ejecutar composer install si existe composer.json en el nuevo origen
        $composer_json_path = $orig_path . '/composer.json';

        if (file_exists($composer_json_path)) {
            $cmd    = 'cd ' . escapeshellarg($orig_path) . ' && composer install --no-dev --prefer-dist 2>&1';
            $output = shell_exec($cmd);

            // Guarda salida del comando por si se necesita depurar
            file_put_contents(plugin_dir_path(__DIR__) . 'composer.log', $output);
        }

        // Build a new zip with the new source.
        $zip = $this->zip_up_folder_recursive($orig_path, $zip_path);

        if (true === $zip) {

            // Download the new Zip.
            $this->download_zip($zip_path, $zip_name);

        }

    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        https://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string $source    Source path.
     * @param       string $dest      Destination path.
     * @param       int    $permissions New folder creation permissions.
     * @return      bool     Returns true on success, false on failure.
     */
    private function create_source_copy($source, $dest, $permissions = 0755)
    {

        $source_hash = $this->hash_directory($source);

        // Check for symlinks.
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file.
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory.
        if (! is_dir($dest)) {
            mkdir($dest, $permissions, true);
        }

        // Loop through the folder.
        $dir = dir($source);

        while (false !== $entry = $dir->read()) {

            // Skip pointers.
            if ('.' == $entry || '..' == $entry) {
                continue;
            }

            // Deep copy directories.
            if ($source_hash != $this->hash_directory($source . '/' . $entry)) {
                $this->create_source_copy("$source/$entry", "$dest/$entry", $permissions);
            }
        }

        // Clean up.
        $dir->close();

        return true;

    }

    /**
     * Copy directory inside itself.
     *
     * In case of coping a directory inside itself,
     * hash check the directory.
     * Otherwise and infinite loop of copying is generated.
     *
     * @since 1.0.0
     * @param string $directory the path to the directory.
     * @return string | bool md5'd path of file/dir, or false.
     */
    private function hash_directory($directory)
    {

        if (! is_dir($directory)) {

            return false;

        }

        $files = [];
        $dir   = dir($directory);

        while (false !== ($file = $dir->read())) {

            if ('.' != $file && '..' != $file) {

                if (is_dir($directory . '/' . $file)) {

                    $files[] = $this->hash_directory($directory . '/' . $file);

                } else {

                    $files[] = md5_file($directory . '/' . $file);

                }
            }
        }

        $dir->close();

        return md5(implode('', $files));
    }

    /**
     * Find all files inside directory.
     *
     * Recursively find all files in a directory,
     * inclisive files in subdirectories.
     *
     * @since 1.0.0
     * @param string $source the path to the directory.
     * @return array $result Array with all file paths.
     */
    private function find_all_files($source)
    {

        $root   = scandir($source);
        $result = [];

        foreach ($root as $value) {

            if ('.' === $value || '..' === $value) {
                continue;
            }

            if (is_file("$source/$value")) {
                $result[] = "$source/$value";
                continue;
            }

            $sub_files = is_array($this->find_all_files("$source/$value")) ? $this->find_all_files("$source/$value") : [];

            foreach ($sub_files as $value) {

                $result[] = $value;

            }
        }

        return $result;

    }

    /**
     * Replace all strings and filenames to be replaced.
     *
     * In the newly generated New Source Folder, replace all strings,
     * rename file names.
     *
     * @since 1.0.0
     * @since 2.0.0 Added PHP version, fixed replacements, added prefix pfx_.
     * @param string $file the path to the file.
     * @param array  $new_data Array with all new data to use for repalcement.
     */
    private function replace_names($file, $new_data = [])
    {

        if (empty($new_data)) {
            return;
        }

        $file_contents = file_get_contents($file);
        $file_contents = str_replace('pfx_', $new_data['plugin_prefix'], $file_contents);
        $file_contents = str_replace('My Plugin Name', $new_data['plugin_full_name'], $file_contents);
        $file_contents = str_replace('plugin-name', $new_data['plugin_file_name'], $file_contents);
        $file_contents = str_replace('Plugin Human Name', $new_data['plugin_human_name'], $file_contents);
        $file_contents = str_replace('Plugin_Name', $new_data['plugin_class_name'], $file_contents);
        $file_contents = str_replace('https://plugin.com/plugin-uri/', $new_data['plugin_uri'], $file_contents);
        $file_contents = str_replace('1.0.0', $new_data['plugin_version'], $file_contents);
        $file_contents = str_replace('This is a short description of what the plugin does. It\'s displayed in the WordPress admin area.', $new_data['plugin_description'], $file_contents);
        $file_contents = str_replace('https://example.com', $new_data['author_uri'], $file_contents);
        $file_contents = str_replace('Requires at least: X.X', 'Requires at least: ' . $new_data['plugin_requires'], $file_contents);
        $file_contents = str_replace('Tested up to:      X.X', 'Tested up to:      ' . $new_data['plugin_tested'], $file_contents);
        $file_contents = str_replace('Tested up to: X.X', 'Tested up to: ' . $new_data['plugin_tested'], $file_contents);
        $file_contents = str_replace('Stable tag: 1.0.0', 'Stable tag: ' . $new_data['plugin_stable'], $file_contents);
        $file_contents = str_replace('comments, spam', $new_data['plugin_tags'], $file_contents);
        $file_contents = str_replace('https://donate.tld/', $new_data['donate_link'], $file_contents);
        $file_contents = str_replace('PLUGIN_NAME_', $new_data['plugin_const_name'], $file_contents);
        $file_contents = str_replace('Your Name or Your Company Name', $new_data['author'], $file_contents);
        $file_contents = str_replace('Your Name', $new_data['author'], $file_contents);
        $file_contents = str_replace('<email@example.com>', $new_data['author_email'], $file_contents);
        $file_contents = str_replace('Requires PHP:      X.X', 'Requires PHP:      ' . $new_data['plugin_requires_php'], $file_contents);
        $new_file      = str_replace('plugin-name', $new_data['plugin_file_name'], $file);

        file_put_contents($file, $file_contents);
        rename($file, $new_file);

    }

    /**
     * Create a Zip of the new source folder.
     *
     * This code requires PHP to be compiled with ZIP Support.
     *
     * Installation for Linux users:
     * compile PHP with zip support by using the –enable-zip configure option.
     *
     * Installation for Windows users:
     * As of PHP 5.3 this extension is inbuilt.
     * Before, Windows users need to enable php_zip.dll inside of php.ini in order to use its functions.
     *
     * @since 1.0.1
     * @param string $source the source folder path.
     * @param string $destination the destination folder path.
     */
    private function zip_up_folder_recursive($source, $destination)
    {
        if (! extension_loaded('zip') || ! file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();

        if (! $zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..'])) {
                    continue;
                }

                $file          = realpath($file);
                $relative_path = substr($file, strlen($source) + 1);

                if (is_dir($file)) {
                    $zip->addEmptyDir($relative_path);
                } elseif (is_file($file)) {
                    $zip->addFile($file, $relative_path);
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        $zip->close();

        // Eliminar carpeta si se requiere después de comprimir
        //$this->delete_file($source);

        return true;
    }

    /**
     * Download the ZIPped file.
     *
     * @since 1.0.1
     * @param string $zip_path The Path to the file to download.
     * @param string $zip_name The Name of the ZIP.
     */
    private function download_zip($zip_path, $zip_name)
    {

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $zip_name);
        header('Content-length: ' . filesize($zip_path));
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($zip_path);

        //$this->delete_file($zip_path);

    }

    /**
     * Delete the ZIPped file.
     *
     * @since 1.1.1
     * @param string $path The Path to the file to delete.
     */
    private function delete_file($path)
    {

        $targets = [
            'zip'    => $path,
            'folder' => str_replace('.zip', '', $path),
        ];

        foreach ($targets as $item) {

            if (is_dir($item)) {

                $files = glob($item . '*', GLOB_MARK); // GLOB_MARK adds a slash to directories returned.

                foreach ($files as $file) {

                    $this->delete_file($file);

                }

                if (is_dir($item)) {
                    rmdir($item);
                }
            } elseif (is_file($item)) {

                unlink($item);

            }
        }

    }

    /**
     * Check for $_POST and Nonce.
     *
     * @since 1.0.1
     * @return bool false or wp_die() If $_POST is not set, return. If $_POST is set and Nonce is invalid, wp_die().
     */
    private function validate_post_and_nonce()
    {

        if (empty($_POST) || (! empty($_POST) && ! isset($_POST['tkt_plugin_generator_submit']))) {

            return false;

        }

        if (! empty($_POST)
            && isset($_POST['tkt_plugin_generator_submit'])
            && ! isset($_POST['generate_plugin_nonce'])
            || (isset($_POST['generate_plugin_nonce'])
                && ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['generate_plugin_nonce'])), 'generate_plugin_submit')
            )
        ) {

            wp_die(
                esc_html('Invalid Form Submission'),
                esc_html('Invalid Form Submission'),
                [
                    'response'  => intval(422),
                    'code'      => esc_html('invalid_form_submission'),
                    'back_link' => (bool) true,
                ]
            );

        }

    }

    /**
     * Get new data from $_POST
     *
     * @since 1.0.1
     * @return array $new_data The Data submitted in the form.
     */
    private function get_new_data()
    {

        /**
         * WPCS FALSE ALARM.
         *
         * WPCS throws a "Processing Form data without nonce verification" here.
         * However this is a false alarm, we do indeed validate the form, $_POST AND the NONCE in
         * validate_post_and_nonce() method of same class.
         * That method returns or wp_die()'s if falsy, thus we are safe.
         *
         * @since 1.0.1
         * @since 2.0.0 Added PHP version.
         * @see $this->validate_post_and_nonce();
         * @see $this->replace_zip_and_download();
         */
        $new_data = [
            'plugin_requires_php' => isset($_POST['plugin_requires_php']) ? sanitize_text_field(wp_unslash($_POST['plugin_requires_php'])) : '7.0.0',
            'plugin_prefix'       => isset($_POST['plugin_prefix']) ? sanitize_title(wp_unslash($_POST['plugin_prefix']), '', 'save') . '_' : 'pfx_',
            'plugin_full_name'    => isset($_POST['plugin_name']) ? sanitize_text_field(wp_unslash($_POST['plugin_name'])) : 'My Plugin Name',
            'plugin_file_name'    => isset($_POST['plugin_slug']) ? sanitize_title(wp_unslash($_POST['plugin_slug']), '', 'save') : 'plugin-slug',
            'plugin_human_name'   => isset($_POST['plugin_human']) ? sanitize_text_field(wp_unslash($_POST['plugin_human']), '', 'save') : sanitize_text_field(wp_unslash($_POST['plugin_name'])),
            'plugin_class_name'   => str_replace('-', '_', ucwords(isset($_POST['plugin_slug']) ? sanitize_title(wp_unslash($_POST['plugin_slug']), '', 'save') : 'plugin-slug', '-')),
            'plugin_uri'          => isset($_POST['plugin_uri']) ? esc_url_raw(wp_unslash($_POST['plugin_uri'])) : 'https://plugin.com/plugin-name-uri/',
            'plugin_description'  => isset($_POST['plugin_description']) ? sanitize_text_field(wp_unslash($_POST['plugin_description'])) : 'This is a short description of what the plugin does. It\'s displayed in the WordPress admin area.',
            'plugin_version'      => isset($_POST['plugin_version']) ? sanitize_text_field(wp_unslash($_POST['plugin_version'])) : '1.0.0',
            'author_uri'          => isset($_POST['author_uri']) ? esc_url_raw(wp_unslash($_POST['author_uri'])) : 'https://author.com/',
            'plugin_requires'     => isset($_POST['plugin_requires']) ? sanitize_text_field(wp_unslash($_POST['plugin_requires'])) : '4.9',
            'plugin_tested'       => isset($_POST['plugin_tested']) ? sanitize_text_field(wp_unslash($_POST['plugin_tested'])) : '5.8',
            'plugin_stable'       => isset($_POST['plugin_stable']) ? sanitize_text_field(wp_unslash($_POST['plugin_stable'])) : '1.0.0',
            'plugin_tags'         => isset($_POST['plugin_tags']) ? sanitize_text_field(wp_unslash($_POST['plugin_tags'])) : 'comments, spam',
            'donate_link'         => isset($_POST['donate_link']) ? esc_url_raw(wp_unslash($_POST['donate_link'])) : 'https://donate.tld/',
            'plugin_const_name'   => strtoupper(str_replace('-', '_', ucwords(isset($_POST['plugin_slug']) ? sanitize_title(wp_unslash($_POST['plugin_slug']), '', 'save') : 'plugin-slug'))) . '_',
            'author'              => isset($_POST['author']) ? sanitize_text_field(wp_unslash($_POST['author'])) : 'Your Name or Your Company Name',
            'author_email'        => isset($_POST['author_email']) ? '<' . sanitize_email(wp_unslash($_POST['author_email'])) . '>' : '<your@email.com>',
        ];

        return $new_data;

    }

}
