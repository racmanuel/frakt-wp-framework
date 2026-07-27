(function () {
	'use strict';

	var form = document.getElementById('tkt-plugin-generator-generator');
	var overlay = document.getElementById('tkt-plugin-generator-overlay');
	var submitButton = document.getElementById('tkt-plugin-generator-generator-submit');
	var result = document.getElementById('tkt-plugin-generator-result');
	var overlayText = document.getElementById('tkt-generator-overlay-text');
	var overlayHint = document.getElementById('tkt-generator-overlay-hint');
	var loaderTimer = null;
	var loaderMessages = (typeof tktPluginGenerator !== 'undefined' && tktPluginGenerator.messages && tktPluginGenerator.messages.loader) || [
		{title: 'Generating %s for WordPress', hint: 'Checking the details required by WordPress.'},
		{title: 'Preparing your WordPress plugin', hint: 'Applying your plugin name, slug, prefix, and metadata.'},
		{title: 'Assembling WordPress modules', hint: 'Organizing the selected administration, public, and lifecycle files.'},
		{title: 'Checking Composer dependencies', hint: 'Preparing composer.json without bundling the vendor directory.'},
		{title: 'Creating your ZIP archive', hint: 'Packaging the generated WordPress plugin for download.'},
		{title: 'Almost ready', hint: 'Your WordPress plugin download will be available shortly.'}
	];

	function personalizeLoaderText(text) {
		var name = humanName ? humanName.value.trim() : '';
		return text.replace(/%s/g, name || 'your plugin');
	}

	if (!form || !overlay || !result || typeof tktPluginGenerator === 'undefined') {
		return;
	}

	var autoFill = tktPluginGenerator.autoFill || {};
	var humanName = document.getElementById('plugin_human');
	var pluginSlug = document.getElementById('plugin_slug');
	var pluginVersion = document.getElementById('plugin_version');
	var authorUri = document.getElementById('author_uri');
	var autoFields = {
		plugin_name: false,
		plugin_slug: false,
		plugin_prefix: false,
		plugin_version: false,
		plugin_stable: false,
		plugin_requires: false,
		plugin_requires_php: false,
		plugin_tested: false,
		plugin_tags: false,
		plugin_description: false,
		plugin_uri: false
	};

	function slugify(value) {
		var normalizedValue = typeof value.normalize === 'function'
			? value.normalize('NFD')
			: value;

		return normalizedValue
			.replace(/[\u0300-\u036f]/g, '')
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '');
	}

	function prefixFromSlug(slug) {
		return slug
			.split('-')
			.filter(Boolean)
			.map(function (word) {
				var consonants = word.replace(/[aeiou]/g, '');
				return consonants || word.charAt(0);
			})
			.join('_');
	}

	function setAutoValue(fieldId, value) {
		var field = document.getElementById(fieldId);

		if (field && !autoFields[fieldId]) {
			field.value = value;
		}
	}

	function pluginUrl(slug) {
		var baseUrl = authorUri && authorUri.value
			? authorUri.value
			: autoFill.siteUrl;

		if (!baseUrl || !slug) {
			return '';
		}

		return baseUrl.replace(/\/+$/, '') + '/' + slug + '/';
	}

	function updateFromHumanName() {
		var name = humanName ? humanName.value.trim() : '';
		var slug = slugify(name);
		var version = autoFill.pluginVersion || '1.0.0';
		var descriptionTemplate = autoFill.descriptionTemplate || '%s is a WordPress plugin.';

		setAutoValue('plugin_name', name);
		setAutoValue('plugin_slug', slug);
		setAutoValue('plugin_prefix', prefixFromSlug(slug));
		setAutoValue('plugin_version', name ? version : '');
		setAutoValue(
			'plugin_stable',
			name && pluginVersion ? pluginVersion.value : ''
		);
		setAutoValue('plugin_requires', name ? (autoFill.requiresWordPress || '6.0') : '');
		setAutoValue('plugin_requires_php', name ? (autoFill.requiresPhp || '7.4') : '');
		setAutoValue('plugin_tested', name ? (autoFill.testedWordPress || '') : '');
		setAutoValue('plugin_tags', slug ? slug.split('-').join(', ') : '');
		setAutoValue(
			'plugin_description',
			name ? descriptionTemplate.replace('%s', name) : ''
		);
		setAutoValue('plugin_uri', pluginUrl(slug));
	}

	Object.keys(autoFields).forEach(function (fieldId) {
		var field = document.getElementById(fieldId);

		if (field) {
			field.addEventListener('input', function () {
				autoFields[fieldId] = true;
			});
		}
	});

	if (humanName) {
		humanName.addEventListener('input', updateFromHumanName);
	}

	if (pluginVersion) {
		pluginVersion.addEventListener('input', function () {
			setAutoValue('plugin_stable', pluginVersion.value);
		});
	}

	if (pluginSlug) {
		pluginSlug.addEventListener('input', function () {
			var slug = slugify(pluginSlug.value);
			setAutoValue('plugin_prefix', prefixFromSlug(slug));
			setAutoValue('plugin_uri', pluginUrl(slug));
		});
	}

	if (authorUri) {
		authorUri.addEventListener('input', function () {
			setAutoValue('plugin_uri', pluginUrl(pluginSlug ? pluginSlug.value : ''));
		});
	}

	function setLoading(isLoading) {
		overlay.classList.toggle('show', isLoading);
		overlay.setAttribute('aria-hidden', isLoading ? 'false' : 'true');
		form.setAttribute('aria-busy', isLoading ? 'true' : 'false');

		if (submitButton) {
			submitButton.disabled = isLoading;
		}

		if (loaderTimer) {
			window.clearInterval(loaderTimer);
			loaderTimer = null;
		}

		if (isLoading) {
			var messageIndex = 0;

			function updateLoaderMessage() {
				var message = loaderMessages[messageIndex] || {};

				if (overlayText && message.title) {
					overlayText.textContent = personalizeLoaderText(message.title);
				}

				if (overlayHint && message.hint) {
					overlayHint.textContent = message.hint;
				}
			}

			updateLoaderMessage();
			loaderTimer = window.setInterval(function () {
				messageIndex = Math.min(messageIndex + 1, loaderMessages.length - 1);
				updateLoaderMessage();
			}, 1800);
		}
	}

	function setLoaderMessage(index) {
		var message = loaderMessages[index] || {};

		if (overlayText && message.title) {
			overlayText.textContent = personalizeLoaderText(message.title);
		}

		if (overlayHint && message.hint) {
			overlayHint.textContent = message.hint;
		}
	}

	function showError(message) {
		result.hidden = false;
		result.className = 'tkt-generator-result error';
		result.replaceChildren();

		var paragraph = document.createElement('p');
		paragraph.textContent = message;
		result.appendChild(paragraph);
	}

	function copyCommand(command, button) {
		if (!navigator.clipboard) {
			return;
		}

		navigator.clipboard.writeText(command).then(function () {
			button.textContent = tktPluginGenerator.messages.copySuccess;
		});
	}

	function showSuccess(data) {
		result.hidden = false;
		result.className = 'tkt-generator-result success';
		result.replaceChildren();

		var title = document.createElement('p');
		title.className = 'tkt-generator-result-title';
		var strong = document.createElement('strong');
		strong.textContent = data.message;
		title.appendChild(strong);
		result.appendChild(title);

		if (Array.isArray(data.dependencies) && data.dependencies.length > 0) {
			var explanation = document.createElement('p');
			explanation.textContent = tktPluginGenerator.messages.selectedDependencies || 'Selected dependencies:';
			result.appendChild(explanation);

			var list = document.createElement('ul');
			data.dependencies.forEach(function (dependency) {
				var item = document.createElement('li');
				item.textContent = dependency;
				list.appendChild(item);
			});
			result.appendChild(list);

			var instruction = document.createElement('p');
			instruction.textContent = tktPluginGenerator.messages.composerInstruction || 'After extracting the ZIP, run this command before activating the plugin:';
			result.appendChild(instruction);

			var command = document.createElement('code');
			command.className = 'tkt-generator-command';
			command.textContent = data.command;
			result.appendChild(command);

			var copyButton = document.createElement('button');
			copyButton.type = 'button';
			copyButton.className = 'tkt-generator-copy';
			copyButton.textContent = tktPluginGenerator.messages.copyCommand || 'Copy command';
			copyButton.addEventListener('click', function () {
				copyCommand(data.command, copyButton);
			});
			result.appendChild(copyButton);
		}

		// Actions row
		var actionsRow = document.createElement('div');
		actionsRow.className = 'tkt-generator-result-actions';

		var download = document.createElement('a');
		download.className = 'tkt-generator-download';
		download.href = data.download_url;
		download.textContent = tktPluginGenerator.messages.downloadZip || 'Download ZIP';
		actionsRow.appendChild(download);

		if (data.playground_url) {
			var playgroundButton = document.createElement('a');
			playgroundButton.className = 'tkt-generator-playground';
			playgroundButton.href = data.playground_url;
			playgroundButton.target = '_blank';
			playgroundButton.rel = 'noopener noreferrer';
			playgroundButton.textContent = tktPluginGenerator.messages.playgroundTest || 'Test in WordPress Playground';
			actionsRow.appendChild(playgroundButton);
		}

		result.appendChild(actionsRow);

		// CLI command section
		if (data.playground_command) {
			var cliSection = document.createElement('div');
			cliSection.className = 'tkt-generator-cli-section';

			var cliLabel = document.createElement('p');
			cliLabel.textContent = tktPluginGenerator.messages.playgroundTestLocally || 'Or test locally with WordPress Playground:';
			cliSection.appendChild(cliLabel);

			var cliCommand = document.createElement('code');
			cliCommand.className = 'tkt-generator-command tkt-generator-cli-command';
			cliCommand.textContent = data.playground_command;
			cliSection.appendChild(cliCommand);

			var cliCopyButton = document.createElement('button');
			cliCopyButton.type = 'button';
			cliCopyButton.className = 'tkt-generator-copy';
			cliCopyButton.textContent = tktPluginGenerator.messages.playgroundCopyCommand || 'Copy command';
			cliCopyButton.addEventListener('click', function () {
				copyCommand(data.playground_command, cliCopyButton);
			});
			cliSection.appendChild(cliCopyButton);

			result.appendChild(cliSection);
		}

		result.scrollIntoView({behavior: 'smooth', block: 'nearest'});

		try {
			sessionStorage.removeItem(draftKey);
		} catch (error) {
			// Storage can be unavailable in privacy-focused browser modes.
		}

		try {
			sessionStorage.removeItem(draftKey);
		} catch (error) {
			// Storage can be unavailable in privacy-focused browser modes.
		}
	}

	var wizardConfig = tktPluginGenerator.wizard || {};
	var wizardSteps = wizardConfig.steps || [
		'Basic information',
		'Author and compatibility',
		'Architecture',
		'Dependencies',
		'Review and download'
	];
	var stepper = document.getElementById('tkt-plugin-generator-steps');
	var review = document.getElementById('tkt-plugin-generator-review');
	var previewTree = document.getElementById('tkt-generator-preview-tree');
	var previewCount = document.getElementById('tkt-generator-preview-count');
	var previewNote = document.getElementById('tkt-generator-preview-note');
	var fieldsets = [];
	var stepButtons = [];
	var currentStep = 1;
	var furthestStep = 1;
	var draftKey = 'tktPluginGeneratorDraftV3';
	var publicModule = document.getElementById('include_public');
	var shortcodeModule = document.getElementById('include_shortcode');

	function syncArchitectureControls() {
		if (!publicModule || !shortcodeModule) {
			return;
		}

		if (!publicModule.checked) {
			shortcodeModule.checked = false;
			shortcodeModule.disabled = true;
		} else {
			shortcodeModule.disabled = false;
		}
	}

	function fieldValue(fieldId) {
		var field = document.getElementById(fieldId);
		return field ? field.value : '';
	}

	function checkboxValue(fieldId) {
		var field = document.getElementById(fieldId);
		return Boolean(field && field.checked);
	}

	function customDependencySlugs() {
		var field = document.getElementById('tkt-custom-deps');
		var slugs;

		if (!field) {
			return [];
		}

		try {
			slugs = JSON.parse(field.value || '[]');
		} catch (error) {
			return [];
		}

		if (!Array.isArray(slugs)) {
			return [];
		}

		return slugs.filter(function (slug, index) {
			return typeof slug === 'string'
				&& slug.length > 0
				&& slugs.indexOf(slug) === index;
		});
	}

	function previewFile(label) {
		return {type: 'file', label: label};
	}

	function previewDirectory(label, children) {
		return {type: 'directory', label: label, children: children || []};
	}

	function getPreviewTree() {
		var slug = fieldValue('plugin_slug') || 'plugin-slug';
		var hasBuiltInDependencies = [
			'include_acf',
			'include_qm',
			'include_wpc',
			'include_us',
			'include_pc',
			'include_tm',
			'include_jwt'
		].some(checkboxValue);
		var hasCustomDependencies = customDependencySlugs().length > 0;
		var dependencies = hasBuiltInDependencies || hasCustomDependencies;
		var includeI18n = checkboxValue('include_i18n');
		var includeLifecycle = checkboxValue('include_lifecycle');
		var includeUninstall = checkboxValue('include_uninstall');
		var includeAdmin = checkboxValue('include_admin');
		var includePublic = checkboxValue('include_public');
		var includesFiles = [
			previewFile('class-' + slug + '.php'),
			previewFile('class-' + slug + '-loader.php'),
			previewFile('index.php')
		];

		if (includeI18n) {
			includesFiles.splice(2, 0, previewFile('class-' + slug + '-i18n.php'));
		}

		if (includeLifecycle) {
			includesFiles.splice(2, 0,
				previewFile('class-' + slug + '-activator.php'),
				previewFile('class-' + slug + '-deactivator.php')
			);
		}

		var root = [
			previewFile(slug + '.php'),
			previewFile('index.php'),
			previewFile('README.txt'),
			previewFile('LICENSE.txt'),
			previewFile('.gitignore'),
			checkboxValue('include_acf') ? previewDirectory('scf-json/', [previewFile('index.php')]) : null,
			previewDirectory('playground/', [previewFile('blueprint.json')]),
			previewDirectory('includes/', includesFiles)
		].filter(Boolean);

		if (includeAdmin) {
			root.push(previewDirectory('admin/', [
				previewFile('class-' + slug + '-admin.php'),
				previewFile('index.php'),
				previewDirectory('css/', [previewFile(slug + '-admin.css')]),
				previewDirectory('js/', [previewFile(slug + '-admin.js')]),
				previewDirectory('partials/', [previewFile(slug + '-admin-display.php')])
			]));
		}

		if (includePublic) {
			root.push(previewDirectory('public/', [
				previewFile('class-' + slug + '-public.php'),
				previewFile('index.php'),
				previewDirectory('css/', [previewFile(slug + '-public.css')]),
				previewDirectory('js/', [previewFile(slug + '-public.js')]),
				previewDirectory('partials/', [previewFile(slug + '-public-display.php')])
			]));
		}

		if (includeI18n) {
			root.push(previewDirectory('languages/', [previewFile(slug + '.pot')]));
		}

		if (includeUninstall) {
			root.push(previewFile('uninstall.php'));
		}

		if (dependencies) {
			root.push(previewFile('composer.json'), previewFile('COMPOSER-INSTALL.md'));
		}

		return previewDirectory(slug + '/', root);
	}

	function countPreviewItems(node, counts) {
		if (node.type === 'directory') {
			counts.directories += 1;
			node.children.forEach(function (child) {
				countPreviewItems(child, counts);
			});
		} else {
			counts.files += 1;
		}
	}

	function createPreviewList(nodes) {
		var list = document.createElement('ul');
		list.className = 'tkt-generator-preview-list';

		nodes.forEach(function (node) {
			var item = document.createElement('li');
			item.className = 'tkt-generator-preview-item ' + node.type;

			var label = document.createElement('span');
			label.className = 'tkt-generator-preview-label';
			label.textContent = node.type === 'directory' ? '▾ ' + node.label : '· ' + node.label;
			item.appendChild(label);

			if (node.type === 'directory' && node.children.length) {
				item.appendChild(createPreviewList(node.children));
			}

			list.appendChild(item);
		});

		return list;
	}

	function renderPreview() {
		if (!previewTree) {
			return;
		}

		var tree = getPreviewTree();
		var counts = {files: 0, directories: 0};
		countPreviewItems(tree, counts);
		previewTree.replaceChildren(createPreviewList([tree]));

		if (previewCount) {
			previewCount.textContent = counts.files + ' files · ' + counts.directories + ' folders';
		}

		if (previewNote) {
			previewNote.textContent = checkboxValue('include_acf') || checkboxValue('include_qm') || checkboxValue('include_wpc') || checkboxValue('include_us') || checkboxValue('include_pc') || checkboxValue('include_tm') || checkboxValue('include_jwt')
				? 'Composer dependencies are listed, but vendor/ is never included. Run Composer after extracting the ZIP.'
				: 'This is an estimate based on your selections. The vendor directory is never included in the generated ZIP.';
		}
	}

	function createButton(label, className, handler) {
		var button = document.createElement('button');
		button.type = 'button';
		button.className = className;
		button.textContent = label;
		button.addEventListener('click', handler);
		return button;
	}

	function appendReviewSection(title, step, rows) {
		var section = document.createElement('section');
		section.className = 'tkt-generator-review-section';

		var heading = document.createElement('h3');
		heading.textContent = title;
		section.appendChild(heading);

		var edit = createButton(
			wizardConfig.edit || 'Edit',
			'tkt-generator-edit',
			function () {
				showStep(step);
			}
		);
		section.appendChild(edit);

		var list = document.createElement('dl');
		rows.forEach(function (row) {
			var term = document.createElement('dt');
			var description = document.createElement('dd');
			term.textContent = row[0];
			description.textContent = row[1] || '—';
			list.appendChild(term);
			list.appendChild(description);
		});
		section.appendChild(list);
		review.appendChild(section);
	}

	function renderReview() {
		if (!review) {
			return;
		}

		review.replaceChildren();
		renderPreview();

		var reviewTitles = wizardConfig.reviewTitles || {};
		appendReviewSection(
			reviewTitles.basic || 'Basic information',
			1,
			[
				['Name', fieldValue('plugin_human')],
				['Slug', fieldValue('plugin_slug')],
				['Prefix', fieldValue('plugin_prefix') + '_'],
				['Version', fieldValue('plugin_version')],
				['Description', fieldValue('plugin_description')]
			]
		);
		appendReviewSection(
			reviewTitles.author || 'Author and compatibility',
			2,
			[
				['Author', fieldValue('author')],
				['Email', fieldValue('author_email')],
				['WordPress', fieldValue('plugin_requires') + '+'],
				['PHP', fieldValue('plugin_requires_php') + '+'],
				['Tested up to', fieldValue('plugin_tested')]
			]
		);

		var architecture = [
			checkboxValue('include_admin') ? 'Administration' : '',
			checkboxValue('include_public') ? 'Public' : '',
			checkboxValue('include_shortcode') ? 'Shortcode' : '',
			checkboxValue('include_i18n') ? 'Translations' : '',
			checkboxValue('include_lifecycle') ? 'Lifecycle' : '',
			checkboxValue('include_uninstall') ? 'Uninstall' : ''
		].filter(Boolean);
		appendReviewSection(
			reviewTitles.architecture || 'Architecture',
			3,
			[
				['Structure', 'Frakt Classic'],
				['Modules', architecture.join(', ') || (wizardConfig.noneSelected || 'None')]
			]
		);

		var dependencyPackages = [];
		var dependencies = [];
		var builtInDependencies = [
			['include_acf', 'wp-plugin/secure-custom-fields', 'Secure Custom Fields'],
			['include_qm', 'wp-plugin/query-monitor', 'Query Monitor'],
			['include_wpc', 'wp-plugin/wp-crontrol', 'WP Crontrol'],
			['include_us', 'wp-plugin/user-switching', 'User Switching'],
			['include_pc', 'wp-plugin/plugin-check', 'Plugin Check'],
			['include_tm', 'wp-plugin/transients-manager', 'Transients Manager'],
			['include_jwt', 'wp-plugin/jwt-authentication-for-wp-rest-api', 'JWT Authentication']
		];

		builtInDependencies.forEach(function (dependency) {
			if (checkboxValue(dependency[0]) && dependencyPackages.indexOf(dependency[1]) === -1) {
				dependencyPackages.push(dependency[1]);
				dependencies.push(dependency[2]);
			}
		});

		customDependencySlugs().forEach(function (slug) {
			var packageName = 'wp-plugin/' + slug;

			if (dependencyPackages.indexOf(packageName) === -1) {
				dependencyPackages.push(packageName);
				dependencies.push(packageName);
			}
		});

		appendReviewSection(
			reviewTitles.dependencies || 'Dependencies',
			4,
			[
				['Selected', dependencies.join(', ') || (wizardConfig.noneSelected || 'None')],
				['Composer', dependencies.length ? 'Required before activation' : 'Not required']
			]
		);
	}

	function updateStepper() {
		stepButtons.forEach(function (button, index) {
			var step = index + 1;
			button.classList.toggle('is-active', step === currentStep);
			button.classList.toggle('is-complete', step < currentStep || step < furthestStep);
			button.setAttribute('aria-current', step === currentStep ? 'step' : 'false');
			button.disabled = step > furthestStep;
		});
	}

	function showStep(step) {
		if (step < 1 || step > fieldsets.length) {
			return;
		}

		currentStep = step;
		furthestStep = Math.max(furthestStep, step);
		fieldsets.forEach(function (fieldset, index) {
			fieldset.hidden = index + 1 !== step;
		});

		if (step === 5) {
			renderReview();
		}

		updateStepper();

		var legend = fieldsets[step - 1].querySelector('legend');
		if (legend) {
			legend.setAttribute('tabindex', '-1');
			legend.focus();
		}
	}

	function validateStep(step) {
		var fieldset = fieldsets[step - 1];

		if (!fieldset) {
			return true;
		}

		var fields = Array.from(fieldset.querySelectorAll('input, textarea, select'));
		var invalid = fields.find(function (field) {
			return !field.disabled && typeof field.checkValidity === 'function' && !field.checkValidity();
		});

		if (invalid) {
			if (fieldset.hidden) {
				showStep(step);
			}
			invalid.reportValidity();
			invalid.focus();
			return false;
		}

		return true;
	}

	function saveDraft() {
		var draft = {};

		Array.from(form.querySelectorAll('[name]')).forEach(function (field) {
			if (
				field.name === 'generate_plugin_nonce'
				|| field.name === 'tkt_plugin_generator_submit'
			) {
				return;
			}

			draft[field.name] = field.type === 'checkbox'
				? Boolean(field.checked)
				: field.value;
		});

		try {
			sessionStorage.setItem(draftKey, JSON.stringify(draft));
		} catch (error) {
			// The wizard remains functional when browser storage is unavailable.
		}
	}

	function restoreDraft() {
		var draft;

		try {
			draft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
		} catch (error) {
			draft = {};
		}

		Object.keys(draft).forEach(function (name) {
			var field = form.querySelector('[name="' + name + '"]');

			if (!field) {
				return;
			}

			if (field.type === 'checkbox') {
				field.checked = Boolean(draft[name]);
			} else {
				field.value = draft[name];
			}

			if (Object.prototype.hasOwnProperty.call(autoFields, name)) {
				autoFields[name] = true;
			}
		});
	}

	function buildWizard() {
		var stepNodes = Array.from(form.querySelectorAll('[data-wizard-step]'));

		if (!stepNodes.length || !stepper) {
			return;
		}

		restoreDraft();
		form.noValidate = true;

		wizardSteps.forEach(function (title, index) {
			var step = index + 1;
			var fieldset = document.createElement('fieldset');
			var legend = document.createElement('legend');
			var navigation = document.createElement('div');
			fieldset.className = 'tkt-generator-step';
			fieldset.dataset.step = String(step);
			legend.textContent = title;
			navigation.className = 'tkt-generator-navigation';
			fieldset.appendChild(legend);

			stepNodes
				.filter(function (node) {
					return Number(node.dataset.wizardStep) === step;
				})
				.forEach(function (node) {
					fieldset.appendChild(node);
				});

			if (step > 1) {
				navigation.appendChild(
					createButton(
						wizardConfig.previous || 'Previous',
						'tkt-generator-previous',
						function () {
							showStep(step - 1);
						}
					)
				);
			}

			if (step < wizardSteps.length) {
				navigation.appendChild(
					createButton(
						wizardConfig.next || 'Continue',
						'tkt-generator-next',
						function () {
							if (validateStep(step)) {
								showStep(step + 1);
							}
						}
					)
				);
			}

			fieldset.appendChild(navigation);
			form.insertBefore(fieldset, form.querySelector('[name="generate_plugin_nonce"]'));
			fieldsets.push(fieldset);

			var stepButton = createButton(
				String(step) + '. ' + title,
				'tkt-generator-step-button',
				function () {
					if (step <= furthestStep) {
						showStep(step);
					}
				}
			);
			stepper.appendChild(stepButton);
			stepButtons.push(stepButton);
		});

		form.addEventListener('input', saveDraft);
		form.addEventListener('change', saveDraft);
		form.addEventListener('change', renderPreview);
		if (publicModule) {
			publicModule.addEventListener('change', syncArchitectureControls);
		}
		syncArchitectureControls();
		showStep(1);
	}

	buildWizard();

	if (humanName && humanName.value) {
		updateFromHumanName();
	}

	// ── Package Search ──────────────────────────────────────────────

	var PackageSearch = (function () {
		var searchInput = document.getElementById('tkt-package-search');
		var resultsContainer = document.getElementById('tkt-package-search-results');
		var selectedContainer = document.getElementById('tkt-package-selected');
		var hiddenInput = document.getElementById('tkt-custom-deps');
		var statusEl = document.getElementById('tkt-package-search-status');
		var spinner = document.getElementById('tkt-package-search-spinner');
		var selectedSlugs = [];
		var debounceTimer = null;
		var DEBOUNCE_MS = 300;
		var MIN_CHARS = 2;

		function init() {
			if (!searchInput || !resultsContainer || !hiddenInput) {
				return;
			}

			searchInput.addEventListener('input', onSearchInput);
			searchInput.addEventListener('keydown', function (e) {
				if (e.key === 'Escape') {
					searchInput.value = '';
					clearResults();
				}
			});
		}

		function onSearchInput() {
			clearTimeout(debounceTimer);
			var term = searchInput.value.trim();

			if (term.length < MIN_CHARS) {
				clearResults();
				return;
			}

			debounceTimer = setTimeout(function () {
				doSearch(term);
			}, DEBOUNCE_MS);
		}

		function doSearch(term) {
			setStatus('loading', 'Searching…');
			showSpinner(true);

			var url = tktPluginGenerator.ajaxUrl
				+ '?action=tkt_search_wp_packages&term='
				+ encodeURIComponent(term);

			fetch(url, { method: 'GET', credentials: 'same-origin' })
				.then(function (resp) {
					return resp.json().catch(function () {
						throw new Error('Invalid response');
					});
				})
				.then(function (json) {
					if (!json.success || !json.data) {
						throw new Error(json.data && json.data.message ? json.data.message : 'Search failed');
					}
					renderResults(json.data.plugins, json.data.total);
				})
				.catch(function (err) {
					setStatus('error', err.message || 'Search temporarily unavailable.');
					clearResults();
				})
				.finally(function () {
					showSpinner(false);
				});
		}

		function renderResults(plugins, total) {
			resultsContainer.innerHTML = '';

			if (!plugins.length) {
				statusEl.hidden = false;
				statusEl.textContent = 'No plugins found for "' + searchInput.value.trim() + '".';
				statusEl.className = 'tkt-package-search-status tkt-package-search-empty';
				return;
			}

			statusEl.hidden = true;

			plugins.forEach(function (plugin) {
				var card = buildCard(plugin);
				resultsContainer.appendChild(card);
			});
		}

		function buildCard(plugin) {
			var card = document.createElement('div');
			card.className = 'tkt-package-card';

			var iconUrl = (plugin.icons && plugin.icons['2x'])
				|| (plugin.icons && plugin.icons['1x'])
				|| (plugin.icons && plugin.icons['default'])
				|| '';

			var isAdded = selectedSlugs.indexOf(plugin.slug) !== -1;
			var wpAvailable = plugin.wp_packages && plugin.wp_packages.available;

			var starsHtml = buildStars(plugin.rating);

			card.innerHTML =
				'<div class="tkt-package-card__header">'
				+ (iconUrl
					? '<img class="tkt-package-card__icon" src="' + escapeHtml(iconUrl) + '" alt="" width="48" height="48" loading="lazy">'
					: '<div class="tkt-package-card__icon tkt-package-card__icon--fallback"></div>')
				+ '<div class="tkt-package-card__title">'
				+ '<strong>' + escapeHtml(plugin.name) + '</strong>'
				+ '<span class="tkt-package-card__slug">wp-plugin/' + escapeHtml(plugin.slug) + '</span>'
				+ '</div>'
				+ '</div>'
				+ '<p class="tkt-package-card__desc">' + escapeHtml(plugin.short_description || '') + '</p>'
				+ '<div class="tkt-package-card__meta">'
				+ '<span class="tkt-package-card__author">By ' + escapeHtml(plugin.author || 'Unknown') + '</span>'
				+ '<span class="tkt-package-card__rating">' + starsHtml + ' <small>(' + plugin.num_ratings + ')</small></span>'
				+ '</div>'
				+ '<div class="tkt-package-card__footer">'
				+ '<span class="tkt-package-card__installs">' + formatInstalls(plugin.active_installs) + ' active installs</span>'
				+ (wpAvailable
					? '<span class="tkt-package-badge tkt-package-badge--ok">WP Packages v' + escapeHtml(plugin.wp_packages.latest_version) + '</span>'
					: '<span class="tkt-package-badge tkt-package-badge--warn">Not in WP Packages</span>')
				+ '<button type="button" class="tkt-package-card__btn ' + (isAdded ? 'tkt-package-card__btn--added' : '') + '"'
				+ (wpAvailable ? '' : ' disabled')
				+ ' data-slug="' + escapeHtml(plugin.slug) + '"'
				+ ' data-name="' + escapeHtml(plugin.name) + '">'
				+ (isAdded ? 'Added ✓' : 'Add')
				+ '</button>'
				+ '</div>';

			var btn = card.querySelector('.tkt-package-card__btn');
			btn.addEventListener('click', function () {
				togglePlugin(plugin.slug, plugin.name, btn, wpAvailable);
			});

			return card;
		}

		function togglePlugin(slug, name, btn, wpAvailable) {
			if (!wpAvailable) {
				return;
			}

			var idx = selectedSlugs.indexOf(slug);

			if (idx === -1) {
				selectedSlugs.push(slug);
				btn.textContent = 'Added ✓';
				btn.classList.add('tkt-package-card__btn--added');
				addChip(slug, name);
			} else {
				selectedSlugs.splice(idx, 1);
				btn.textContent = 'Add';
				btn.classList.remove('tkt-package-card__btn--added');
				removeChip(slug);
			}

			updateHiddenInput();
			saveDraft();
		}

		function addChip(slug, name) {
			if (selectedContainer.querySelector('[data-slug="' + slug + '"]')) {
				return;
			}

			var chip = document.createElement('span');
			chip.className = 'tkt-package-chip';
			chip.setAttribute('data-slug', slug);
			chip.innerHTML = '<span>' + escapeHtml(name) + '</span>'
				+ '<small>wp-plugin/' + escapeHtml(slug) + '</small>'
				+ '<button type="button" aria-label="Remove ' + escapeHtml(name) + '">&times;</button>';

			chip.querySelector('button').addEventListener('click', function () {
				selectedSlugs = selectedSlugs.filter(function (s) { return s !== slug; });
				chip.remove();
				updateHiddenInput();
				refreshCardButtons();
				saveDraft();
			});

			selectedContainer.appendChild(chip);
		}

		function removeChip(slug) {
			var chip = selectedContainer.querySelector('[data-slug="' + slug + '"]');
			if (chip) {
				chip.remove();
			}
		}

		function refreshCardButtons() {
			resultsContainer.querySelectorAll('.tkt-package-card__btn').forEach(function (btn) {
				var btnSlug = btn.getAttribute('data-slug');
				var isAdded = selectedSlugs.indexOf(btnSlug) !== -1;
				btn.textContent = isAdded ? 'Added ✓' : 'Add';
				btn.classList.toggle('tkt-package-card__btn--added', isAdded);
			});
		}

		function updateHiddenInput() {
			if (hiddenInput) {
				hiddenInput.value = JSON.stringify(selectedSlugs);
			}
		}

		function clearResults() {
			resultsContainer.innerHTML = '';
			statusEl.hidden = true;
		}

		function setStatus(type, message) {
			statusEl.hidden = false;
			statusEl.textContent = message;
			statusEl.className = 'tkt-package-search-status tkt-package-search-' + type;
		}

		function showSpinner(show) {
			if (spinner) {
				spinner.hidden = !show;
			}
		}

		function buildStars(rating) {
			var pct = Math.min(100, Math.max(0, (rating / 100) * 100));
			var filled = Math.round(pct / 20);
			var html = '';
			for (var i = 1; i <= 5; i++) {
				html += i <= filled
					? '<span class="tkt-star tkt-star--filled">★</span>'
					: '<span class="tkt-star">☆</span>';
			}
			return html;
		}

		function formatInstalls(count) {
			if (count >= 1000000) {
				return (count / 1000000).toFixed(1) + 'M';
			}
			if (count >= 1000) {
				return (count / 1000).toFixed(0) + 'K';
			}
			return String(count);
		}

		function escapeHtml(str) {
			var div = document.createElement('div');
			div.appendChild(document.createTextNode(str));
			return div.innerHTML;
		}

		// Restore from draft on init
		function restoreFromDraft() {
			try {
				var draft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
				var customStr = draft.custom_dependencies || '[]';
				var slugs = JSON.parse(customStr);
				if (Array.isArray(slugs) && slugs.length) {
					slugs.forEach(function (slug) {
						if (typeof slug !== 'string' || !slug || selectedSlugs.indexOf(slug) !== -1) {
							return;
						}

						selectedSlugs.push(slug);
						addChip(slug, slug);
					});
					updateHiddenInput();
				}
			} catch (e) {
				// ignore
			}
		}

		init();
		restoreFromDraft();

		return {
			getSelectedSlugs: function () { return selectedSlugs.slice(); }
		};
	})();

	form.addEventListener('submit', function (event) {
		event.preventDefault();

		for (var step = 1; step < fieldsets.length; step++) {
			if (!validateStep(step)) {
				showStep(step);
				return;
			}
		}

		if (currentStep !== fieldsets.length) {
			showStep(fieldsets.length);
			return;
		}

		setLoading(true);
		result.hidden = true;

		var formData = new FormData(form);
		formData.append('action', tktPluginGenerator.action);

		fetch(tktPluginGenerator.ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
			.then(function (response) {
				return response.json().catch(function () {
					throw new Error(tktPluginGenerator.messages.genericError);
				});
			})
			.then(function (response) {
				if (!response.success || !response.data) {
					throw new Error(
						response.data && response.data.message
							? response.data.message
							: tktPluginGenerator.messages.genericError
					);
				}

				setLoaderMessage(loaderMessages.length - 1);
				showSuccess(response.data);
			})
			.catch(function (error) {
				showError(error.message || tktPluginGenerator.messages.networkError);
			})
			.finally(function () {
				setLoading(false);
			});
	});
})();
