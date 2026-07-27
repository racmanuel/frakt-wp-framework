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

    $styles_url = plugin_dir_url(__FILE__) . 'css/';

    wp_enqueue_style(
     $this->plugin_name . '-tailwind',
     $styles_url . 'tkt-plugin-generator-tailwind.css',
     [],
     $this->version,
     'all'
    );

    wp_enqueue_style(
     $this->plugin_name . '-runtime',
     $styles_url . 'tkt-plugin-generator-public.css',
     [ $this->plugin_name . '-tailwind' ],
     $this->version,
     'all'
    );

 }

 /**
  * Register the JavaScript used by the generator form.
  *
  * @since 2.1.0
  */
 public function enqueue_scripts()
 {

  wp_enqueue_script(
   $this->plugin_name,
   plugin_dir_url(__FILE__) . 'js/tkt-plugin-generator-public.js',
   [  ],
   $this->version,
   true
  );

  wp_localize_script(
   $this->plugin_name,
   'tktPluginGenerator',
   [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'action'  => 'tkt_generate_plugin',
    'messages' => [
    'genericError' => __('The plugin could not be generated. Please try again.', 'tkt-plugin-generator'),
    'networkError' => __('The request failed or took too long. Check your connection and try again.', 'tkt-plugin-generator'),
    'copySuccess'  => __('Command copied.', 'tkt-plugin-generator'),
      'selectedDependencies' => __('Selected dependencies:', 'tkt-plugin-generator'),
      'composerInstruction'  => __('After extracting the ZIP, run this command before activating the plugin:', 'tkt-plugin-generator'),
      'copyCommand'          => __('Copy command', 'tkt-plugin-generator'),
      'downloadZip'          => __('Download ZIP', 'tkt-plugin-generator'),
      'playgroundTest'        => __('Test in WordPress Playground', 'tkt-plugin-generator'),
      'playgroundTestLocally' => __('Or test locally with WordPress Playground:', 'tkt-plugin-generator'),
      'playgroundCopyCommand' => __('Copy command', 'tkt-plugin-generator'),
      'playgroundLabel'       => __('Playground CLI command', 'tkt-plugin-generator'),
    'loader' => [
        [
          'title' => __('Generating %s for WordPress', 'tkt-plugin-generator'),
          'hint'  => __('Checking the details required by WordPress.', 'tkt-plugin-generator'),
        ],
        [
         'title' => __('Preparing your WordPress plugin', 'tkt-plugin-generator'),
         'hint'  => __('Applying your plugin name, slug, prefix, and metadata.', 'tkt-plugin-generator'),
        ],
        [
         'title' => __('Assembling WordPress modules', 'tkt-plugin-generator'),
         'hint'  => __('Organizing the selected administration, public, and lifecycle files.', 'tkt-plugin-generator'),
        ],
        [
         'title' => __('Checking Composer dependencies', 'tkt-plugin-generator'),
         'hint'  => __('Preparing composer.json without bundling the vendor directory.', 'tkt-plugin-generator'),
        ],
        [
         'title' => __('Creating your ZIP archive', 'tkt-plugin-generator'),
         'hint'  => __('Packaging the generated WordPress plugin for download.', 'tkt-plugin-generator'),
        ],
        [
         'title' => __('Almost ready', 'tkt-plugin-generator'),
         'hint'  => __('Your WordPress plugin download will be available shortly.', 'tkt-plugin-generator'),
        ],
    ],
    ],
    'autoFill' => [
     'siteUrl'             => untrailingslashit(home_url('/')),
     'pluginVersion'       => '1.0.0',
     'requiresWordPress'   => '6.0',
     'requiresPhp'         => '7.4',
     'testedWordPress'     => get_bloginfo('version'),
    'descriptionTemplate' => __('%s is a WordPress plugin.', 'tkt-plugin-generator'),
    ],
    'wizard' => [
     'steps' => [
    __('Basic information', 'tkt-plugin-generator'),
    __('Author and compatibility', 'tkt-plugin-generator'),
    __('Architecture', 'tkt-plugin-generator'),
    __('Dependencies', 'tkt-plugin-generator'),
    __('Review and download', 'tkt-plugin-generator'),
     ],
    'previous'     => __('Previous', 'tkt-plugin-generator'),
    'next'         => __('Continue', 'tkt-plugin-generator'),
    'edit'         => __('Edit', 'tkt-plugin-generator'),
    'stepProgress' => __('Step %1$d of %2$d', 'tkt-plugin-generator'),
     'reviewTitles' => [
    'basic'        => __('Basic information', 'tkt-plugin-generator'),
    'author'       => __('Author and compatibility', 'tkt-plugin-generator'),
    'architecture' => __('Architecture', 'tkt-plugin-generator'),
    'dependencies' => __('Dependencies', 'tkt-plugin-generator'),
     ],
    'noneSelected' => __('None', 'tkt-plugin-generator'),
    ],
   ]
  );

 }

 /**
  * Register the ShortCode.
  *
  * @since 1.0.0
  */
 public function add_shortcode()
 {

  add_shortcode('generate_plugin', [ $this, 'generate_plugin_form' ]);

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
  * @since 2.0.1 Added error feedback with redirect + transient messages.
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
  $filename  = $new_data[ 'plugin_file_name' ];
  $zip_name  = $new_data[ 'plugin_file_name' ] . '.zip';
  $orig_path = plugin_dir_path(__DIR__) . 'builds/' . $filename;
  $zip_path  = plugin_dir_path(__DIR__) . 'builds/' . $zip_name;

  // Ensure source directory exists.
  if ( ! is_dir( $source ) ) {
   $this->redirect_with_error(
    __( 'Error: The source/ directory could not be found.', 'tkt-plugin-generator' )
   );
   return;
  }

  // Create a copy of the source files to the new source.
  $copied = $this->create_source_copy( $source, $orig_path, 0755 );
  if ( false === $copied ) {
   $this->redirect_with_error(
    __( 'Error: The plugin template could not be copied. Check the write permissions.', 'tkt-plugin-generator' )
   );
   return;
  }

  // Find all files in the new source.
  $files = $this->find_all_files( $orig_path );

  if ( empty( $files ) ) {
   $this->redirect_with_error(
    __( 'Error: No files were found in the copied template.', 'tkt-plugin-generator' )
   );
   return;
  }

  foreach ( $files as $file ) {

   // Replace all strings (including filenames) in the new source.
   $this->replace_names( $file, $new_data );

  }

  // Modify composer.json based on selected options.
  $this->process_composer_dependencies( $orig_path, $new_data );

  // Build a new zip with the new source.
  $zip = $this->zip_up_folder_recursive( $orig_path, $zip_path );

  if ( true === $zip ) {

   // Download the new Zip.
   $this->download_zip( $zip_path, $zip_name );

  } else {

   $this->redirect_with_error(
    __( 'Error: The ZIP file could not be generated. Check that the PHP ZIP extension is enabled and that the builds/ directory is writable.', 'tkt-plugin-generator' )
   );

  }

 }

 /**
  * Handle plugin generation through WordPress AJAX.
  *
  * @since 2.1.0
  */
 public function ajax_generate_plugin()
 {
  check_ajax_referer('generate_plugin_submit', 'generate_plugin_nonce');

  $result = $this->generate_plugin_package($this->get_new_data());

  if (is_wp_error($result)) {
   wp_send_json_error(['message' => $result->get_error_message()], 422);
  }

  wp_send_json_success($result);
 }

 /**
  * Generate a plugin ZIP without running Composer on the server.
  *
  * @since 2.1.0
  * @param array $new_data Sanitized generator data.
  * @return array|WP_Error
  */
 private function generate_plugin_package($new_data)
 {
  $source   = plugin_dir_path(__DIR__) . 'source';
  $builds   = plugin_dir_path(__DIR__) . 'builds';
  $filename = $new_data['plugin_file_name'];

  if (empty($filename)) {
    return new WP_Error('invalid_plugin_slug', __('You must provide a valid plugin slug.', 'tkt-plugin-generator'));
  }

  if (! is_dir($source)) {
    return new WP_Error('source_not_found', __('The source/ directory could not be found.', 'tkt-plugin-generator'));
  }

  if (! is_dir($builds) && ! wp_mkdir_p($builds)) {
    return new WP_Error('builds_not_writable', __('The builds/ directory could not be created. Check the write permissions.', 'tkt-plugin-generator'));
  }

  $job_id    = str_replace('-', '', wp_generate_uuid4());
  $job_path  = trailingslashit($builds) . $job_id;
  $orig_path = trailingslashit($job_path) . $filename;
  $zip_name  = $filename . '.zip';
  $zip_path  = trailingslashit($job_path) . $zip_name;

  $copied = $this->create_source_copy(
   $source,
   $orig_path,
   0755,
   [wp_normalize_path(trailingslashit($source) . 'vendor')]
  );

  if (false === $copied) {
   $this->delete_path($job_path);
    return new WP_Error('source_copy_failed', __('The plugin template could not be copied. Check the write permissions.', 'tkt-plugin-generator'));
  }

  $files = $this->find_all_files($orig_path);

  if (empty($files)) {
   $this->delete_path($job_path);
  return new WP_Error('empty_source', __('No files were found in the copied template.', 'tkt-plugin-generator'));
  }

  foreach ($files as $file) {
   $this->replace_names($file, $new_data);
  }

  $this->process_architecture_files($orig_path, $new_data);
  $dependencies = $this->process_composer_dependencies($orig_path, $new_data);

  if (! empty($dependencies)) {
   $this->write_composer_instructions($orig_path, $dependencies);
  }

  if (true !== $this->zip_up_folder_recursive($orig_path, $zip_path)) {
   $this->delete_path($job_path);
    return new WP_Error('zip_failed', __('The ZIP file could not be generated. Check that the PHP ZIP extension is enabled.', 'tkt-plugin-generator'));
  }

  $this->delete_path($orig_path);

  $playground_token = str_replace('-', '', wp_generate_uuid4());

  $download_token = str_replace('-', '', wp_generate_uuid4());
  set_transient(
   'tkt_gen_download_' . $download_token,
   [
    'path'     => $zip_path,
    'name'     => $zip_name,
    'job_path' => $job_path,
   ],
   15 * MINUTE_IN_SECONDS
  );

  $playground_blueprint_url = rest_url('tkt-generator/v1/playground/' . $playground_token);
  set_transient(
   'tkt_gen_playground_' . $playground_token,
   [
    'download_url' => add_query_arg(
     [
      'action' => 'tkt_download_plugin',
      'token'  => $download_token,
     ],
     admin_url('admin-ajax.php')
    ),
    'plugin_slug'  => $filename,
   ],
   HOUR_IN_SECONDS
  );

  return [
  'message'      => __('Plugin generated successfully.', 'tkt-plugin-generator'),
   'download_url' => add_query_arg(
    [
     'action' => 'tkt_download_plugin',
     'token'  => $download_token,
    ],
    admin_url('admin-ajax.php')
   ),
   'dependencies' => array_values($dependencies),
   'command'      => empty($dependencies) ? '' : 'composer install --no-dev --prefer-dist --optimize-autoloader',
   'playground_url'      => 'https://playground.wordpress.net/?blueprint-url=' . urlencode($playground_blueprint_url),
   'playground_command'  => sprintf(
    'npx @wp-playground/cli@latest server --mount="./%1$s":"/wordpress/wp-content/plugins/%1$s"',
    $filename
   ),
  ];
 }

 /**
  * Download a generated ZIP using a short-lived token.
  *
  * @since 2.1.0
  */
 public function download_generated_plugin()
 {
  $token = isset($_GET['token']) ? sanitize_key(wp_unslash($_GET['token'])) : '';
  $data  = get_transient('tkt_gen_download_' . $token);

  if (empty($token) || ! is_array($data) || empty($data['path']) || ! is_file($data['path'])) {
   wp_die(
    esc_html__('The download does not exist or has expired. Generate the plugin again.', 'tkt-plugin-generator'),
    esc_html__('Download unavailable', 'tkt-plugin-generator'),
    ['response' => 404]
   );
  }

  $builds_real = realpath(plugin_dir_path(__DIR__) . 'builds');
  $zip_real    = realpath($data['path']);

  if (
   false === $builds_real
   || false === $zip_real
   || 0 !== strpos(wp_normalize_path($zip_real), trailingslashit(wp_normalize_path($builds_real)))
  ) {
   wp_die(
    esc_html__('The download path is not valid.', 'tkt-plugin-generator'),
    esc_html__('Download unavailable', 'tkt-plugin-generator'),
    ['response' => 403]
   );
  }

  delete_transient('tkt_gen_download_' . $token);
  nocache_headers();
  header('Content-Type: application/zip');
  header('Content-Disposition: attachment; filename="' . sanitize_file_name($data['name']) . '"');
  header('Content-Length: ' . filesize($zip_real));
  readfile($zip_real);

  $this->delete_path($data['job_path']);
  exit;
 }

 /**
  * Register the REST API endpoint for Playground blueprints.
  *
  * @since 2.5.0
  */
 public function register_playground_endpoint()
 {
  register_rest_route(
   'tkt-generator/v1',
   '/playground/(?P<token>[a-f0-9]+)',
   [
    'methods'             => 'GET',
    'callback'            => [$this, 'serve_playground_blueprint'],
    'permission_callback' => '__return_true',
    'args'                => [
     'token' => [
      'required'          => true,
      'validate_callback' => function ($value) {
       return (bool) preg_match('/^[a-f0-9]+$/', $value);
      },
      'sanitize_callback' => 'sanitize_key',
     ],
    ],
   ]
  );
 }

 /**
  * Serve a Playground blueprint JSON that installs the generated plugin.
  *
  * @since 2.5.0
  * @param WP_REST_Request $request The request object.
  * @return WP_REST_Response|WP_Error
  */
 public function serve_playground_blueprint($request)
 {
  $token = $request->get_param('token');
  $data  = get_transient('tkt_gen_playground_' . $token);

  if (empty($token) || ! is_array($data) || empty($data['download_url']) || empty($data['plugin_slug'])) {
   return new WP_Error(
    'playground_expired',
    __('The Playground session has expired. Generate the plugin again.', 'tkt-plugin-generator'),
    ['status' => 404]
   );
  }

  $blueprint = [
   '$schema'           => 'https://playground.wordpress.net/blueprint-schema.json',
   'meta'              => [
    'title'       => sprintf('%s - WordPress Playground', $data['plugin_slug']),
    'author'      => get_bloginfo('name'),
    'description' => sprintf(__('Test %s in WordPress Playground.', 'tkt-plugin-generator'), $data['plugin_slug']),
   ],
   'landingPage'       => '/wp-admin/plugins.php',
   'preferredVersions' => [
    'php' => '8.3',
    'wp'  => 'latest',
   ],
   'features'          => [
    'networking' => true,
   ],
   'steps'             => [
    [
     'step'           => 'installPlugin',
     'pluginZipFile'  => [
      'resource' => 'url',
      'url'      => $data['download_url'],
     ],
     'options'        => [
      'activate' => true,
     ],
    ],
    [
     'step' => 'login',
    ],
   ],
  ];

  return new WP_REST_Response($blueprint, 200, [
   'Content-Type'  => 'application/json',
   'Cache-Control' => 'no-cache, no-store, must-revalidate',
  ]);
 }

 private function redirect_with_error( $message ) {

  set_transient( 'tkt_gen_feedback', $message, 60 );
  wp_safe_redirect( remove_query_arg( 'tkt_gen_status', wp_get_referer() ) );
  exit;

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
 private function create_source_copy($source, $dest, $permissions = 0755, $excluded_paths = [])
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

   $entry_path = wp_normalize_path("$source/$entry");

   if (in_array($entry_path, $excluded_paths, true)) {
    continue;
   }

   // Deep copy directories.
   if ($source_hash != $this->hash_directory($source . '/' . $entry)) {
    $this->create_source_copy("$source/$entry", "$dest/$entry", $permissions, $excluded_paths);
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

  $files = [  ];
  $dir   = dir($directory);

  while (false !== ($file = $dir->read())) {

   if ('.' != $file && '..' != $file) {

    if (is_dir($directory . '/' . $file)) {

     $files[  ] = $this->hash_directory($directory . '/' . $file);

    } else {

     $files[  ] = md5_file($directory . '/' . $file);

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
  $result = [  ];

  foreach ($root as $value) {

   if ('.' === $value || '..' === $value) {
    continue;
   }

   if (is_file("$source/$value")) {
    $result[  ] = "$source/$value";
    continue;
   }

   $sub_files = is_array($this->find_all_files("$source/$value")) ? $this->find_all_files("$source/$value") : [  ];

   foreach ($sub_files as $value) {

    $result[  ] = $value;

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
 private function replace_names($file, $new_data = [  ])
 {

  if (empty($new_data)) {
   return;
  }

  $file_contents = file_get_contents($file);
  $file_contents = str_replace('pfx_', $new_data[ 'plugin_prefix' ], $file_contents);
  $file_contents = str_replace('My Plugin Name', $new_data[ 'plugin_full_name' ], $file_contents);
  $file_contents = str_replace('plugin-name', $new_data[ 'plugin_file_name' ], $file_contents);
  $file_contents = str_replace('Plugin Human Name', $new_data[ 'plugin_human_name' ], $file_contents);
  $file_contents = str_replace('Plugin_Name', $new_data[ 'plugin_class_name' ], $file_contents);
  $file_contents = str_replace('https://plugin.com/plugin-uri/', $new_data[ 'plugin_uri' ], $file_contents);
  $file_contents = str_replace('1.0.0', $new_data[ 'plugin_version' ], $file_contents);
  $file_contents = str_replace('This is a short description of what the plugin does. It\'s displayed in the WordPress admin area.', $new_data[ 'plugin_description' ], $file_contents);
  $file_contents = str_replace('https://example.com', $new_data[ 'author_uri' ], $file_contents);
  $file_contents = str_replace('Requires at least: X.X', 'Requires at least: ' . $new_data[ 'plugin_requires' ], $file_contents);
  $file_contents = str_replace('Tested up to:      X.X', 'Tested up to:      ' . $new_data[ 'plugin_tested' ], $file_contents);
  $file_contents = str_replace('Tested up to: X.X', 'Tested up to: ' . $new_data[ 'plugin_tested' ], $file_contents);
  $file_contents = str_replace('Stable tag: 1.0.0', 'Stable tag: ' . $new_data[ 'plugin_stable' ], $file_contents);
  $file_contents = str_replace('comments, spam', $new_data[ 'plugin_tags' ], $file_contents);
  $file_contents = str_replace('https://donate.tld/', $new_data[ 'donate_link' ], $file_contents);
  $file_contents = str_replace('PLUGIN_NAME_', $new_data[ 'plugin_const_name' ], $file_contents);
  $file_contents = str_replace('Your Name or Your Company Name', $new_data[ 'author' ], $file_contents);
  $file_contents = str_replace('Your Name', $new_data[ 'author' ], $file_contents);
  $file_contents = str_replace('<email@example.com>', $new_data[ 'author_email' ], $file_contents);
  $file_contents = str_replace('Requires PHP:      X.X', 'Requires PHP:      ' . $new_data[ 'plugin_requires_php' ], $file_contents);
  $file_contents = str_replace('{{plugin_download_url}}', $new_data['plugin_download_url'], $file_contents);

  // Optional Plugins Logic
  $optional_plugins = [
      'include_composer' => 'COMPOSER',
      'include_admin' => 'ADMIN',
      'include_public' => 'PUBLIC',
      'include_shortcode' => 'SHORTCODE',
      'include_i18n' => 'I18N',
      'include_lifecycle' => 'LIFECYCLE',
      'include_acf' => 'ACF',
      'include_qm'  => 'QM',
      'include_wpc' => 'WPC',
      'include_us'  => 'US',
      'include_pc'  => 'PC',
      'include_tm'  => 'TM',
      'include_jwt' => 'JWT',
  ];

  foreach ($optional_plugins as $key => $marker) {
      $start_marker = "/* TKT_GEN_{$marker}_START */";
      $end_marker   = "/* TKT_GEN_{$marker}_END */";

      if (empty($new_data[$key])) {
          // Remove the block entirely
          $pattern = '/' . preg_quote($start_marker, '/') . '.*?' . preg_quote($end_marker, '/') . '/s';
          $file_contents = preg_replace($pattern, '', $file_contents);
      } else {
          // Remove only the markers
          $file_contents = str_replace([$start_marker, $end_marker], '', $file_contents);
      }
  }

  $new_file      = str_replace('plugin-name', $new_data[ 'plugin_file_name' ], $file);

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
    if (in_array(substr($file, strrpos($file, '/') + 1), [ '.', '..' ])) {
     continue;
    }

    $file          = wp_normalize_path(realpath($file));
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
  * @since 2.0.1 Added exit after readfile() to cleanly terminate the response.
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

  exit;

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

  if (empty($_POST) || (! empty($_POST) && ! isset($_POST[ 'tkt_plugin_generator_submit' ]))) {

   return false;

  }

  if (! empty($_POST)
   && isset($_POST[ 'tkt_plugin_generator_submit' ])
   && ! isset($_POST[ 'generate_plugin_nonce' ])
   || (isset($_POST[ 'generate_plugin_nonce' ])
    && ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[ 'generate_plugin_nonce' ])), 'generate_plugin_submit')
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
   'plugin_requires_php' => isset($_POST[ 'plugin_requires_php' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_requires_php' ])) : '7.0.0',
   'plugin_prefix'       => isset($_POST[ 'plugin_prefix' ]) ? sanitize_title(wp_unslash($_POST[ 'plugin_prefix' ]), '', 'save') . '_' : 'pfx_',
   'plugin_full_name'    => isset($_POST[ 'plugin_name' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_name' ])) : 'My Plugin Name',
   'plugin_file_name'    => isset($_POST[ 'plugin_slug' ]) ? sanitize_title(wp_unslash($_POST[ 'plugin_slug' ]), '', 'save') : 'plugin-slug',
   'plugin_human_name'   => isset($_POST[ 'plugin_human' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_human' ]), '', 'save') : sanitize_text_field(wp_unslash($_POST[ 'plugin_name' ])),
   'plugin_class_name'   => str_replace('-', '_', ucwords(isset($_POST[ 'plugin_slug' ]) ? sanitize_title(wp_unslash($_POST[ 'plugin_slug' ]), '', 'save') : 'plugin-slug', '-')),
   'plugin_uri'          => isset($_POST[ 'plugin_uri' ]) ? esc_url_raw(wp_unslash($_POST[ 'plugin_uri' ])) : 'https://plugin.com/plugin-name-uri/',
   'plugin_description'  => isset($_POST[ 'plugin_description' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_description' ])) : 'This is a short description of what the plugin does. It\'s displayed in the WordPress admin area.',
   'plugin_version'      => isset($_POST[ 'plugin_version' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_version' ])) : '1.0.0',
   'author_uri'          => isset($_POST[ 'author_uri' ]) ? esc_url_raw(wp_unslash($_POST[ 'author_uri' ])) : 'https://author.com/',
   'plugin_requires'     => isset($_POST[ 'plugin_requires' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_requires' ])) : '4.9',
   'plugin_tested'       => isset($_POST[ 'plugin_tested' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_tested' ])) : '5.8',
   'plugin_stable'       => isset($_POST[ 'plugin_stable' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_stable' ])) : '1.0.0',
   'plugin_tags'         => isset($_POST[ 'plugin_tags' ]) ? sanitize_text_field(wp_unslash($_POST[ 'plugin_tags' ])) : 'comments, spam',
   'donate_link'         => isset($_POST[ 'donate_link' ]) ? esc_url_raw(wp_unslash($_POST[ 'donate_link' ])) : 'https://donate.tld/',
   'plugin_const_name'   => strtoupper(str_replace('-', '_', ucwords(isset($_POST[ 'plugin_slug' ]) ? sanitize_title(wp_unslash($_POST[ 'plugin_slug' ]), '', 'save') : 'plugin-slug'))) . '_',
   'author'              => isset($_POST[ 'author' ]) ? sanitize_text_field(wp_unslash($_POST[ 'author' ])) : 'Your Name or Your Company Name',
   'author_email'        => isset($_POST[ 'author_email' ]) ? '<' . sanitize_email(wp_unslash($_POST[ 'author_email' ])) . '>' : '<your@email.com>',
   'include_acf'         => isset($_POST['include_acf']) ? (bool) $_POST['include_acf'] : false,
   'include_qm'          => isset($_POST['include_qm']) ? (bool) $_POST['include_qm'] : false,
   'include_wpc'         => isset($_POST['include_wpc']) ? (bool) $_POST['include_wpc'] : false,
   'include_us'          => isset($_POST['include_us']) ? (bool) $_POST['include_us'] : false,
   'include_pc'          => isset($_POST['include_pc']) ? (bool) $_POST['include_pc'] : false,
   'include_tm'          => isset($_POST['include_tm']) ? (bool) $_POST['include_tm'] : false,
   'include_jwt'         => isset($_POST['include_jwt']) ? (bool) $_POST['include_jwt'] : false,
   'architecture_type'   => isset($_POST['architecture_type']) ? sanitize_key(wp_unslash($_POST['architecture_type'])) : 'classic',
   'include_admin'       => isset($_POST['include_admin']) ? (bool) $_POST['include_admin'] : false,
   'include_public'      => isset($_POST['include_public']) ? (bool) $_POST['include_public'] : false,
   'include_shortcode'   => isset($_POST['include_shortcode']) ? (bool) $_POST['include_shortcode'] : false,
   'include_i18n'        => isset($_POST['include_i18n']) ? (bool) $_POST['include_i18n'] : false,
   'include_lifecycle'   => isset($_POST['include_lifecycle']) ? (bool) $_POST['include_lifecycle'] : false,
   'include_uninstall'   => isset($_POST['include_uninstall']) ? (bool) $_POST['include_uninstall'] : false,
   'custom_dependencies' => isset($_POST['custom_dependencies']) ? sanitize_text_field(wp_unslash($_POST['custom_dependencies'])) : '[]',
   ];

  if ('classic' !== $new_data['architecture_type']) {
   $new_data['architecture_type'] = 'classic';
  }

  if (! $new_data['include_public']) {
   $new_data['include_shortcode'] = false;
  }

  $new_data['include_composer'] = (
   $new_data['include_acf']
   || $new_data['include_qm']
   || $new_data['include_wpc']
   || $new_data['include_us']
   || $new_data['include_pc']
   || $new_data['include_tm']
   || $new_data['include_jwt']
   || ! empty(json_decode($new_data['custom_dependencies'], true))
  );

  return $new_data;

 }

 /**
  * Remove files that belong to architecture modules not selected by the user.
  *
  * @since 2.2.0
  * @param string $target_path Generated plugin directory.
  * @param array  $new_data Generator configuration.
  */
 private function process_architecture_files($target_path, $new_data)
 {
  $slug = $new_data['plugin_file_name'];

  if (empty($new_data['include_admin'])) {
   $this->delete_path(trailingslashit($target_path) . 'admin');
  }

  if (empty($new_data['include_public'])) {
   $this->delete_path(trailingslashit($target_path) . 'public');
  }

  if (empty($new_data['include_i18n'])) {
   $this->delete_path(trailingslashit($target_path) . 'languages');
   $this->delete_path(trailingslashit($target_path) . 'includes/class-' . $slug . '-i18n.php');
  }

  if (empty($new_data['include_lifecycle'])) {
   $this->delete_path(trailingslashit($target_path) . 'includes/class-' . $slug . '-activator.php');
   $this->delete_path(trailingslashit($target_path) . 'includes/class-' . $slug . '-deactivator.php');
  }

  if (empty($new_data['include_uninstall'])) {
   $this->delete_path(trailingslashit($target_path) . 'uninstall.php');
  }
 }

 /**
  * Add Composer setup instructions to a generated plugin.
  *
  * @since 2.1.0
  * @param string $target_path Plugin directory.
  * @param array  $dependencies Selected dependency labels.
  */
 private function write_composer_instructions($target_path, $dependencies)
 {
  $dependency_list = '';

  foreach ($dependencies as $dependency) {
   $dependency_list .= '- ' . $dependency . "\n";
  }

  $contents = "# Dependency installation\n\n";
  $contents .= "This plugin was generated without the `vendor/` directory.\n\n";
  $contents .= "Selected dependencies:\n\n" . $dependency_list . "\n";
  $contents .= "Before activating the plugin, open a terminal in this directory and run:\n\n";
  $contents .= "```bash\ncomposer install --no-dev --prefer-dist --optimize-autoloader\n```\n\n";
  $contents .= "After Composer finishes successfully, the plugin will be ready to activate.\n";

  file_put_contents(trailingslashit($target_path) . 'COMPOSER-INSTALL.md', $contents);
 }

 /**
  * Recursively delete a generated path, restricted to builds/.
  *
  * @since 2.1.0
  * @param string $path Path to delete.
  * @return bool
  */
 private function delete_path($path)
 {
  $builds_path = trailingslashit(wp_normalize_path(plugin_dir_path(__DIR__) . 'builds'));
  $target_path = wp_normalize_path($path);

  if ($target_path === untrailingslashit($builds_path) || 0 !== strpos($target_path, $builds_path)) {
   return false;
  }

  if (is_link($path) || is_file($path)) {
   return unlink($path);
  }

  if (! is_dir($path)) {
   return true;
  }

  $iterator = new RecursiveIteratorIterator(
   new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
   RecursiveIteratorIterator::CHILD_FIRST
  );

  foreach ($iterator as $item) {
   if ($item->isLink() || $item->isFile()) {
    unlink($item->getPathname());
   } else {
    rmdir($item->getPathname());
   }
  }

  return rmdir($path);
 }

	/**
	 * Process Composer Dependencies based on selection.
	 *
	 * @since 2.0.0
	 * @param string $target_path The path to the new plugin folder.
	 * @param array  $new_data    The data from the form.
	 * @return array Selected dependency labels keyed by form field.
	 */
	private function process_composer_dependencies($target_path, $new_data)
	{
		$composer_file = $target_path . '/composer.json';

		if (! file_exists($composer_file)) {
			return;
		}

		$composer_content = file_get_contents($composer_file);
		$composer_json    = json_decode($composer_content, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return;
		}

		// Map form fields to composer packages
		$dependency_map = [
			'include_acf' => [
				'package' => 'wp-plugin/secure-custom-fields',
				'label'   => 'Secure Custom Fields',
			],
			'include_qm' => [
				'package' => 'wp-plugin/query-monitor',
				'label'   => 'Query Monitor',
			],
			'include_us' => [
				'package' => 'wp-plugin/user-switching',
				'label'   => 'User Switching',
			],
			'include_wpc' => [
				'package' => 'wp-plugin/wp-crontrol',
				'label'   => 'WP Crontrol',
			],
			'include_pc' => [
				'package' => 'wp-plugin/plugin-check',
				'label'   => 'Plugin Check',
			],
			'include_jwt' => [
				'package' => 'wp-plugin/jwt-authentication-for-wp-rest-api',
				'label'   => 'JWT Authentication for WP REST API',
			],
			'include_tm' => [
				'package' => 'wp-plugin/transients-manager',
				'label'   => 'Transients Manager',
			],
		];

		$selected_dependencies = [];

		foreach ($dependency_map as $field_key => $dependency) {
			$package_name = $dependency['package'];

			// If the checkbox was NOT checked (false in $new_data), remove the dependency
			if (empty($new_data[ $field_key ])) {
				if (isset($composer_json['require'][ $package_name ])) {
					unset($composer_json['require'][ $package_name ]);
				}
			} else {
				$selected_dependencies[ $field_key ] = $dependency['label'];
			}
		}

		// Decode custom (user-searched) dependencies.
		$custom_slugs = json_decode($new_data['custom_dependencies'] ?? '[]', true);
		$custom_slugs = is_array($custom_slugs) ? $custom_slugs : [];

		if (empty($selected_dependencies) && empty($custom_slugs)) {
			unlink($composer_file);
			return [];
		}

		// Add custom dependencies to composer.json.
		foreach ($custom_slugs as $slug) {
			$slug = sanitize_title($slug, '', 'save');
			if (empty($slug)) {
				continue;
			}
			$composer_json['require']["wp-plugin/{$slug}"] = '*';
			$selected_dependencies['custom_' . $slug] = sprintf(
				/* translators: %s: plugin slug */
				__('Custom: %s', 'tkt-plugin-generator'),
				$slug
			);
		}

		// Save the modified composer.json
		file_put_contents(
			$composer_file,
			json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);

		return $selected_dependencies;
	}

	/**
	 * Handle AJAX search for WP Packages plugins.
	 *
	 * Searches WordPress.org Plugin API and cross-references with
	 * the WP Packages Composer repository to verify availability.
	 *
	 * @since 2.5.0
	 */
	public function ajax_search_wp_packages()
	{
		$term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';

		if (empty($term) || strlen($term) < 2) {
			wp_send_json_success(['plugins' => [], 'total' => 0]);
		}

		$cache_key = 'tkt_pkg_search_' . md5($term);
		$cached    = get_transient($cache_key);

		if (false !== $cached) {
			wp_send_json_success($cached);
		}

		$per_page = 10;
		$page     = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

		$request = [
			'search'   => $term,
			'per_page' => $per_page,
			'page'     => $page,
			'fields'   => [
				'icons'             => true,
				'short_description' => true,
				'active_installs'   => true,
				'rating'            => true,
				'ratings'           => false,
				'downloaded'        => false,
				'banners'           => false,
				'tags'              => false,
				'sections'          => false,
				'contributors'      => false,
			],
		];

		$response = wp_remote_get(
			'https://api.wordpress.org/plugins/info/1.2/?' . http_build_query([
				'action'  => 'query_plugins',
				'request' => $request,
			]),
			['timeout' => 10]
		);

		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			wp_send_json_error([
				'message' => __('Search temporarily unavailable. Please try again.', 'tkt-plugin-generator'),
			], 503);
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (empty($body['plugins'])) {
			$empty_result = ['plugins' => [], 'total' => 0];
			set_transient($cache_key, $empty_result, HOUR_IN_SECONDS);
			wp_send_json_success($empty_result);
		}

		$plugins = [];

		foreach ($body['plugins'] as $plugin) {
			$slug = sanitize_title($plugin['slug'], '', 'save');

			if (empty($slug)) {
				continue;
			}

			$wp_pkg_info = $this->fetch_wp_package_info($slug);

			$plugins[] = [
				'slug'              => $slug,
				'name'              => $plugin['name'],
				'version'           => $plugin['version'] ?? '',
				'author'            => wp_strip_all_tags($plugin['author'] ?? ''),
				'rating'            => (int) ($plugin['rating'] ?? 0),
				'num_ratings'       => (int) ($plugin['num_ratings'] ?? 0),
				'active_installs'   => (int) ($plugin['active_installs'] ?? 0),
				'short_description' => wp_strip_all_tags($plugin['short_description'] ?? ''),
				'icons'             => $plugin['icons'] ?? [],
				'homepage'          => $plugin['homepage'] ?? '',
				'wp_packages'       => [
					'available'       => $wp_pkg_info['available'],
					'latest_version'  => $wp_pkg_info['latest_version'],
				],
			];
		}

		$result = [
			'plugins' => $plugins,
			'total'   => (int) ($body['info']['results'] ?? count($plugins)),
			'page'    => $page,
			'pages'   => (int) ($body['info']['pages'] ?? 1),
		];

		set_transient($cache_key, $result, HOUR_IN_SECONDS);

		wp_send_json_success($result);
	}

	/**
	 * Query WP Packages Composer repository for package availability.
	 *
	 * @since 2.5.0
	 * @param string $slug Plugin slug.
	 * @return array{available: bool, latest_version: string}
	 */
	private function fetch_wp_package_info($slug)
	{
		$cache_key = 'tkt_pkg_info_' . $slug;
		$cached    = get_transient($cache_key);

		if (false !== $cached) {
			return $cached;
		}

		$response = wp_remote_get(
			'https://repo.wp-packages.org/p2/wp-plugin/' . $slug . '.json',
			['timeout' => 5]
		);

		$result = ['available' => false, 'latest_version' => ''];

		if (is_wp_error($response)) {
			set_transient($cache_key, $result, 30 * MINUTE_IN_SECONDS);
			return $result;
		}

		$code = wp_remote_retrieve_response_code($response);
		if ($code !== 200) {
			set_transient($cache_key, $result, 30 * MINUTE_IN_SECONDS);
			return $result;
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (empty($body['packages']['wp-plugin/' . $slug])) {
			set_transient($cache_key, $result, 30 * MINUTE_IN_SECONDS);
			return $result;
		}

		$versions = $body['packages']['wp-plugin/' . $slug];
		$latest   = array_key_last($versions);

		$result = [
			'available'      => true,
			'latest_version' => $latest,
		];

		set_transient($cache_key, $result, 30 * MINUTE_IN_SECONDS);

		return $result;
	}

}
