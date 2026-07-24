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
		var descriptionTemplate = autoFill.descriptionTemplate || '%s es un plugin para WordPress.';

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
			explanation.textContent = 'Dependencias seleccionadas:';
			result.appendChild(explanation);

			var list = document.createElement('ul');
			data.dependencies.forEach(function (dependency) {
				var item = document.createElement('li');
				item.textContent = dependency;
				list.appendChild(item);
			});
			result.appendChild(list);

			var instruction = document.createElement('p');
			instruction.textContent = 'Después de descomprimir el ZIP, ejecuta este comando antes de activar el plugin:';
			result.appendChild(instruction);

			var command = document.createElement('code');
			command.className = 'tkt-generator-command';
			command.textContent = data.command;
			result.appendChild(command);

			var copyButton = document.createElement('button');
			copyButton.type = 'button';
			copyButton.className = 'tkt-generator-copy';
			copyButton.textContent = 'Copiar comando';
			copyButton.addEventListener('click', function () {
				copyCommand(data.command, copyButton);
			});
			result.appendChild(copyButton);
		}

		var download = document.createElement('a');
		download.className = 'tkt-generator-download';
		download.href = data.download_url;
		download.textContent = 'Descargar ZIP';
		result.appendChild(download);
		result.scrollIntoView({behavior: 'smooth', block: 'nearest'});
	}

	if (humanName && humanName.value) {
		updateFromHumanName();
	}

	form.addEventListener('submit', function (event) {
		event.preventDefault();
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
