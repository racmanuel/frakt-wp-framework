# Frakt WP Framework – Plugin Generator

Frakt Plugin Generator is a development tool built on top of the [Frakt WP Framework](https://github.com/TukuToi/better-wp-plugin-boilerplate), designed to help you quickly generate fully structured WordPress plugins ready for development or deployment.

## 🚀 Features

- 🔧 Generates plugins instantly using a boilerplate.
- 🧱 Based on **Frakt WP Framework**, optimized for modern development.
- 🗃️ Includes **Secure Custom Fields** by default.
- 🧩 Automatically generates a `scf-json` folder inside the plugin with your field and CPT definitions.
- ♻️ Easily reusable across installations – copy/paste the generated plugin without losing your structure.
- ⚙️ Loads development tools automatically when `WP_DEBUG` is enabled.
- 📦 Supports custom plugin boilerplates.
- 🧠 Clean, minimal structure ready for Git or deployment.

---

## 🧩 How It Works

1. Install and activate this plugin.
2. Insert the shortcode `[generate_plugin]` in any WordPress page.
3. Visit that page in the browser.
4. Fill out the plugin generation form and submit.
5. The new plugin will:
   - Be generated inside `/builds/`
   - Be downloaded automatically as a ZIP
   - Be removed from the server afterwards for cleanliness

---
## 🚀 About Frakt WP Framework

**Frakt WP Framework** is a modern WordPress plugin generator and development starter kit designed to streamline the process of building high-quality plugins using best practices.

It allows you to generate custom plugins based on a boilerplate structure with just a simple form submission. The generated plugins are instantly downloadable, well-structured, and ready for deployment or further development.

Frakt is opinionated in a good way — it comes pre-configured with powerful tools to help you focus on building features, not boilerplate code.

### ✨ Key Features

- 🔧 Custom plugin generator with form-based creation.
- 📂 Auto-creation of SCF JSON folders for field and post type persistence.
- ⚙️ Developer tools auto-loaded when `WP_DEBUG` is enabled.
- ♻️ Portable: move plugins across WordPress installations without losing structure.
- 🧩 Based on a customizable plugin boilerplate (use your own source).
- 🧼 Uses clear naming conventions for easy replacement logic.

### 🛠 Built for Developers Who Want To:

- Rapidly prototype or scaffold new plugins.
- Version control their custom post types and fields.
- Automate repetitive plugin setup.
- Maintain cleaner and more consistent plugin codebases.

Frakt WP Framework makes plugin development faster, more portable, and developer-friendly.

------------


## 🔐 [Secure Custom Fields (SCF)](https://wordpress.org/plugins/secure-custom-fields/)

This generator includes **Secure Custom Fields (SCF)** by default.

When a plugin is created:

- A `scf-json` folder is created inside the plugin.
- All custom fields and post type definitions are saved there in local JSON format.
- This allows you to **sync or migrate** the plugin between environments (e.g., dev to production) without losing field configurations.

Simply move the plugin folder to another WordPress install, activate it, and SCF will load your configurations automatically.

**Useful for developers who want to:**

- Manage and version control field definitions.
- Deploy plugins with reusable field structures.
- Avoid reliance on the WP database for structural data.

---

## 🧪 [Query Monitor](https://wordpress.org/plugins/query-monitor/)

Query Monitor is a powerful debugging plugin that shows:

- Database queries.
- PHP errors.
- HTTP requests.
- Scripts and styles enqueued.
- Hooks and actions.

**Useful for developers who want to:**

- Inspect performance issues.
- Debug queries and API calls.
- Get real-time feedback while building.

---

## 🔄 [User Switching](https://wordpress.org/plugins/user-switching/)

User Switching allows administrators to switch between user accounts **instantly** in WordPress.

**Useful for developers who want to:**

- Test permissions and user roles.
- Debug user-specific issues.
- Switch between test accounts quickly during development.

---

## ⏱️ [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/)

WP Crontrol lets you view and manage WordPress cron events and schedules via the admin panel.

**Useful for developers who want to:**

- Debug or manage scheduled events.
- Add or edit custom cron jobs.
- Monitor time-based plugin behavior.

---

## 📦 [Plugin Check](https://wordpress.org/plugins/plugin-check/)

Plugin Check runs a series of automated checks on your plugin and ensures it follows WordPress coding standards and best practices.

**Useful for developers who want to:**

- Ensure code quality before deployment.
- Detect potential security or performance issues.
- Pass WP.org plugin repository validation checks.

---

## 🔐 [JWT Authentication for WP REST API](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/)

Enables token-based (JWT) authentication for the WordPress REST API.

**Useful for developers who want to:**

- Secure REST API endpoints.
- Build decoupled or headless WordPress applications.
- Authenticate mobile apps or external systems with WP.

---

## ⚙️ Automatic Dev Tools Loader

When `WP_DEBUG` is enabled in your `wp-config.php`:

```php
define('WP_DEBUG', true);
```
Frakt automatically loads the following developer tools (via Composer or WP Packagist):
```json
"wpackagist-plugin/secure-custom-fields": "*",
"wpackagist-plugin/query-monitor": "*",
"wpackagist-plugin/user-switching": "*",
"wpackagist-plugin/wp-crontrol": "*",
"wpackagist-plugin/plugin-check": "*",
"wpackagist-plugin/jwt-authentication-for-wp-rest-api": "*"
```
No manual activation or setup is required — they're included for development only and excluded in production environments.

##📝 Placeholder Replacements
The plugin generator performs placeholder replacements within the source boilerplate.

Here’s what the generator looks for and replaces in the source files:

| Placeholder                               | Description |
|-------------------------------------------|-------------|
| `pfx_`                                    | Plugin prefix (used in functions, hooks, etc.). |
| `My Plugin Name`                          | Display name of the plugin. |
| `plugin-name`                             | Slugified version (used for folders, filenames, text-domain). |
| `Plugin Human Name`                       | Name in `readme.txt` under `=== Plugin Human Name ===`. |
| `Plugin_Name`                             | Used in class names and PHPDoc `@package`. |
| `https://plugin.com/plugin-name-uri/`     | Plugin URI. |
| `1.0.0`                                   | Plugin version. |
| `This is a short description...`          | Plugin description. |
| `https://example.com`                     | Author URI. |
| `Requires at least: 4.9`                  | Minimum WordPress version. |
| `Tested up to: 5.7`                       | Tested up to WordPress version. |
| `Stable tag: 1.0.0`                       | Stable release tag. |
| `comments, spam`                          | Plugin tags. |
| `https://donate.tld/`                     | Donation link. |
| `PLUGIN_NAME_`                            | Constant prefix. |
| `Your Name or Your Company Name`          | Plugin author. |
| `<email@example.com>`                     | Author email address. |

> ✅ You can customize the replacement logic if your boilerplate uses different placeholder keys.

## 🤝 Credits

This generator is based on the original idea from the [Better WP Plugin Boilerplate](https://github.com/TukuToi/better-wp-plugin-boilerplate).

---

Enjoy building awesome plugins with **Frakt WP Framework** 🛠️✨