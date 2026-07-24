(function () {
	'use strict';

	var form = document.getElementById('tkt-plugin-generator-generator');
	var overlay = document.getElementById('tkt-plugin-generator-overlay');
	var submitButton = document.getElementById('tkt-plugin-generator-generator-submit');
	var result = document.getElementById('tkt-plugin-generator-result');

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

		if (submitButton) {
			submitButton.disabled = isLoading;
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

		var download = document.createElement('a');
		download.className = 'tkt-generator-download';
		download.href = data.download_url;
		download.textContent = tktPluginGenerator.messages.downloadZip || 'Download ZIP';
		result.appendChild(download);
		result.scrollIntoView({behavior: 'smooth', block: 'nearest'});

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

		var dependencies = [
			checkboxValue('include_acf') ? 'Secure Custom Fields' : '',
			checkboxValue('include_qm') ? 'Query Monitor' : '',
			checkboxValue('include_wpc') ? 'WP Crontrol' : '',
			checkboxValue('include_us') ? 'User Switching' : '',
			checkboxValue('include_pc') ? 'Plugin Check' : '',
			checkboxValue('include_tm') ? 'Transients Manager' : '',
			checkboxValue('include_jwt') ? 'JWT Authentication' : ''
		].filter(Boolean);
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
