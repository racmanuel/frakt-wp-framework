<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 * @since      2.0.0 Added PHP requirement field, changed order of fields, changed explanations.
 * @since      2.0.1 Added loading overlay, error/success messages, and JS feedback.
 *
 * @package    Tkt_Plugin_Generator
 * @subpackage Tkt_Plugin_Generator/public/partials
 */

?>

<!-- Loading Overlay -->
<div id="tkt-plugin-generator-overlay" aria-hidden="true">
	<div class="tkt-generator-spinner"></div>
	<div class="tkt-generator-overlay-text">
		<?php esc_html_e( 'Generating plugin...', 'tkt-plugin-generator' ); ?>
	</div>
	<div class="tkt-generator-overlay-hint">
		<?php esc_html_e( 'This may take a few seconds. Please do not reload the page.', 'tkt-plugin-generator' ); ?>
	</div>
</div>

<div class="tkt-generator-shell tkt-font-sans">
	<header class="tkt-generator-intro">
		<p class="tkt-generator-eyebrow"><?php esc_html_e( 'Frakt WP Generator', 'tkt-plugin-generator' ); ?></p>
		<h2><?php esc_html_e( 'Create your WordPress plugin', 'tkt-plugin-generator' ); ?></h2>
		<p><?php esc_html_e( 'Complete the wizard step by step. Key metadata will be filled automatically, and you can review everything before downloading the ZIP.', 'tkt-plugin-generator' ); ?></p>
	</header>

	<nav id="tkt-plugin-generator-steps" class="tkt-generator-steps" aria-label="<?php esc_attr_e( 'Generation progress', 'tkt-plugin-generator' ); ?>"></nav>

	<form id="tkt-plugin-generator-generator" method="post" data-wizard-form>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_human"> 
			<?php esc_html_e( 'Plugin Human Name', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The Human Readable plugin name (Used in Readme. Used only by WP Repo AND the codebase variable for $human_name, which is useful in menus and such. Different from Plugin Header Name, which MUST match the Text Domain).', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_human" name="plugin_human" placeholder="Plugin Human Name" required>
		<small class="tkt-generator-autofill-hint">
			<?php esc_html_e( 'The name, slug, prefix, and related metadata will be completed automatically as you type.', 'tkt-plugin-generator' ); ?>
		</small>
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_name"> 
			<?php esc_html_e( 'Plugin Name', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The plugin name. This has to match with your Text Domain. For WP repo and codebase variable $human_name, use the "Plugin Human Name" field above.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_name" name="plugin_name" placeholder="My Awesome Plugin" required >
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_slug"> <?php esc_html_e( 'Plugin Slug', 'tkt-plugin-generator' ); ?>
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The plugin slug. Used for main folder, main file. Must be all lower case, latin letters. Use hyphens between words. No spaces. Eg "plugin-slug". MUST match "Plugin Name" above (but spaces replaced by hyphens).', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_slug" name="plugin_slug" placeholder="my-awesome-plugin" required>
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_prefix"> 
			<?php esc_html_e( 'Plugin Prefix', 'tkt-plugin-generator' ); ?>
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The plugin Prefix. This will automatically receive an underscore(_) as suffix (end of value), so you do NOT need to add this. Example: "m_prfx" will become "m_prfx_". Usually, use Plugin slug minus vocals, and change hyphens for underscores.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_prefix" name="plugin_prefix" placeholder="my_wsm_plgn" required>
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_version"> 
			<?php esc_html_e( 'Plugin Version', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'Plugin Vesrsion. Usually starts at 1.0.0. Use SemVer.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_version" name="plugin_version" pattern="^[a-zA-Z0-9\.]*$" placeholder="1.0.0" required >
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_stable"> 
			<?php esc_html_e( 'Stable tag', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The stable tag version of this plugin. Usually equal to version above.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_stable" name="plugin_stable" placeholder="1.0.0" required >
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="plugin_requires"> 
			<?php esc_html_e( 'Requires at least', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'What CP or WP version this plugin requires at least.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_requires" name="plugin_requires" placeholder="1.0.0" required >
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="plugin_requires_php"> 
			<?php esc_html_e( 'Requires minimum PHP version', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'What PHP version this plugin requires at least.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_requires_php" name="plugin_requires_php" placeholder="7.0.0" required >
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="plugin_tested"> 
			<?php esc_html_e( 'Tested up to', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'This Plugin was tested up to CP or WP version ...', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_tested" name="plugin_tested" placeholder="4.9.99" required >
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_tags"> 
			<?php esc_html_e( 'Plugin Tags', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'Comma delimited, valid Plugin Tags', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="plugin_tags" name="plugin_tags" placeholder="comments, spam" required >
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_description" style="min-width: fit-content;"> 
			<?php esc_html_e( 'Plugin Description', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'A short Plugin Description (shown in the WP Plugins area).', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<textarea name="plugin_description" id="plugin_description" rows="3" cols="43" required>A Short Plugin description. Keep it to the point.</textarea>
	</div>
	<div class="form-input-container" data-wizard-step="1">
		<label for="plugin_uri"> 
			<?php esc_html_e( 'Plugin URL', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The plugin URL, i.g. where the Plugin is hosted, or its author\s domain.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="url" id="plugin_uri" name="plugin_uri" placeholder="https://www.domain.tld/plugin-name" required >
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="author"> 
			<?php esc_html_e( 'Author Name', 'tkt-plugin-generator' ); ?>
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The author or company name, example "wp_username"', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="text" id="author" name="author" placeholder="wp-or-cp-username" required>
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="author_uri"> 
			<?php esc_html_e( 'Author URL', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The author URL, i.g. where the Plugin is hosted, or its author\s domain.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="url" id="author_uri" name="author_uri" placeholder="https://www.domain.tld/" required >
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="author_email"> 
			<?php esc_html_e( 'Author Email', 'tkt-plugin-generator' ); ?>
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The author or company email.', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="email" id="author_email" name="author_email" placeholder="name@domain.tld" required>
	</div>
	<div class="form-input-container" data-wizard-step="2">
		<label for="donate_link"> 
			<?php esc_html_e( 'Donate Link', 'tkt-plugin-generator' ); ?> 
			<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
				<title><?php esc_html_e( 'The Donate link of this plugin if any', 'tkt-plugin-generator' ); ?></title>
			</svg>
		</label>
		<input type="url" id="donate_link" name="donate_link" placeholder="https://domain.tld/donate">
	</div>

	<div class="form-input-container tkt-generator-architecture" data-wizard-step="3">
		<p><strong><?php esc_html_e( 'Architecture', 'tkt-plugin-generator' ); ?></strong></p>
		<label for="architecture_type"><?php esc_html_e( 'Base structure', 'tkt-plugin-generator' ); ?></label>
		<select id="architecture_type" name="architecture_type">
			<option value="classic"><?php esc_html_e( 'Frakt classic', 'tkt-plugin-generator' ); ?></option>
		</select>
		<p class="tkt-generator-field-hint">
			<?php esc_html_e( 'Modular and PSR-4 structures will be available in a future version.', 'tkt-plugin-generator' ); ?>
		</p>

		<label for="include_admin">
			<input type="checkbox" id="include_admin" name="include_admin" value="1" checked>
			<?php esc_html_e( 'Administration module', 'tkt-plugin-generator' ); ?>
		</label>

		<label for="include_public">
			<input type="checkbox" id="include_public" name="include_public" value="1" checked>
			<?php esc_html_e( 'Public module', 'tkt-plugin-generator' ); ?>
		</label>

		<label for="include_shortcode">
			<input type="checkbox" id="include_shortcode" name="include_shortcode" value="1" checked>
			<?php esc_html_e( 'Example shortcode', 'tkt-plugin-generator' ); ?>
		</label>

		<label for="include_i18n">
			<input type="checkbox" id="include_i18n" name="include_i18n" value="1" checked>
			<?php esc_html_e( 'Internationalization loader', 'tkt-plugin-generator' ); ?>
		</label>

		<label for="include_lifecycle">
			<input type="checkbox" id="include_lifecycle" name="include_lifecycle" value="1" checked>
			<?php esc_html_e( 'Activation and deactivation hooks', 'tkt-plugin-generator' ); ?>
		</label>

		<label for="include_uninstall">
			<input type="checkbox" id="include_uninstall" name="include_uninstall" value="1" checked>
			<?php esc_html_e( 'Uninstall file', 'tkt-plugin-generator' ); ?>
		</label>
	</div>

	<div class="form-input-container tkt-generator-dependencies" data-wizard-step="4">
        <p><strong><?php esc_html_e( 'Include Plugins', 'tkt-plugin-generator' ); ?></strong></p>
        <p class="tkt-generator-composer-notice">
            <?php
            esc_html_e(
				'Selected dependencies will be added to composer.json, but will not be included in the ZIP. You must run Composer before activating the plugin.',
                'tkt-plugin-generator'
            );
            ?>
        </p>
        
        <label for="include_acf">
            <input type="checkbox" id="include_acf" name="include_acf" value="1" checked>
            <?php esc_html_e( 'Secure Custom Fields (ACF)', 'tkt-plugin-generator' ); ?>
		</label>

        <label for="include_qm">
            <input type="checkbox" id="include_qm" name="include_qm" value="1" checked>
            <?php esc_html_e( 'Query Monitor', 'tkt-plugin-generator' ); ?>
		</label>

        <label for="include_wpc">
            <input type="checkbox" id="include_wpc" name="include_wpc" value="1" checked>
            <?php esc_html_e( 'WP Crontrol', 'tkt-plugin-generator' ); ?>
		</label>

        <label for="include_us">
            <input type="checkbox" id="include_us" name="include_us" value="1" checked>
            <?php esc_html_e( 'User Switching', 'tkt-plugin-generator' ); ?>
		</label>

        <label for="include_pc">
            <input type="checkbox" id="include_pc" name="include_pc" value="1" checked>
            <?php esc_html_e( 'Plugin Check', 'tkt-plugin-generator' ); ?>
		</label>

        <label for="include_tm">
            <input type="checkbox" id="include_tm" name="include_tm" value="1" checked>
            <?php esc_html_e( 'Transients Manager', 'tkt-plugin-generator' ); ?>
		</label>

        <label for="include_jwt">
            <input type="checkbox" id="include_jwt" name="include_jwt" value="1" checked>
            <?php esc_html_e( 'JWT Authentication for WP REST API', 'tkt-plugin-generator' ); ?>
        </label>
    </div>
	<div class="form-input-container tkt-generator-review" data-wizard-step="5">
		<div id="tkt-plugin-generator-review" aria-live="polite"></div>
	</div>
	<div class="form-input-container" data-wizard-step="5">
		<button id="tkt-plugin-generator-generator-submit" type="submit" value="tkt_plugin_generator_submit" name="tkt_plugin_generator_submit"> <?php esc_html_e( 'Generate', 'tkt-plugin-generator' ); ?> </button>
	</div>
	<?php wp_nonce_field( 'generate_plugin_submit', 'generate_plugin_nonce' ); ?>
	</form>

	<div id="tkt-plugin-generator-result" class="tkt-generator-result" hidden aria-live="polite"></div>
</div>

