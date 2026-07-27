import { createHash } from 'node:crypto';
import { execFileSync } from 'node:child_process';
import { mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const packageManager = process.platform === 'win32' ? 'npm.cmd' : 'npm';
const generatedCss = {
	bootstrap: {
		packageName: 'bootstrap',
		archivePath: 'package/dist/css/bootstrap.min.css',
		outputs: [
			'source/admin/css/plugin-name-bootstrap-admin.css',
			'source/public/css/plugin-name-bootstrap-public.css',
		],
	},
	bulma: {
		packageName: 'bulma',
		archivePath: 'package/css/bulma.min.css',
		outputs: [
			'source/admin/css/plugin-name-bulma-admin.css',
			'source/public/css/plugin-name-bulma-public.css',
		],
	},
};

function downloadPackage(packageName, destination) {
	const output = execFileSync(
		packageManager,
		['pack', `${packageName}@latest`, '--ignore-scripts', '--pack-destination', destination, '--json'],
		{ cwd: projectRoot, encoding: 'utf8', stdio: ['ignore', 'pipe', 'inherit'], shell: process.platform === 'win32' }
	);
	const result = JSON.parse(output);
	const archiveName = Array.isArray(result) && result[0] ? result[0].filename : '';

	if (!archiveName) {
		throw new Error(`npm no devolvió el archivo de ${packageName}.`);
	}

	return {
		archive: join(destination, archiveName),
		version: result[0].version || 'unknown',
		integrity: result[0].integrity || '',
	};
}

function extractFile(archive, archivePath, destination) {
	execFileSync('tar', ['-xzf', archive, '-C', destination, archivePath], {
		cwd: projectRoot,
		stdio: ['ignore', 'ignore', 'inherit'],
	});

	return join(destination, archivePath);
}

const temporaryDirectory = mkdtempSync(join(tmpdir(), 'frakt-css-frameworks-'));
const manifest = {
	updatedAt: new Date().toISOString(),
	assets: {},
	};

try {
	for (const [framework, config] of Object.entries(generatedCss)) {
		const packageInfo = downloadPackage(config.packageName, temporaryDirectory);
		const extractedFile = extractFile(packageInfo.archive, config.archivePath, temporaryDirectory);
		const css = readFileSync(extractedFile);

		if (!css.length) {
			throw new Error(`El CSS descargado de ${config.packageName} está vacío.`);
		}

		for (const output of config.outputs) {
			writeFileSync(join(projectRoot, output), css);
		}

		manifest.assets[framework] = {
			package: config.packageName,
			version: packageInfo.version,
			packageIntegrity: packageInfo.integrity,
			source: config.archivePath,
			outputs: config.outputs,
			sha512: createHash('sha512').update(css).digest('base64'),
		};
	}

	manifest.assets.tailwind = {
		package: 'tailwindcss',
		note: 'Asset precompilado localmente; requiere el workflow de Tailwind del generador.',
		outputs: [
			'source/admin/css/plugin-name-tailwind-admin.css',
			'source/public/css/plugin-name-tailwind-public.css',
		],
	};

	writeFileSync(
		join(projectRoot, 'css-framework-versions.json'),
		`${JSON.stringify(manifest, null, 2)}\n`
	);

	console.log('Bootstrap y Bulma se actualizaron en source/admin/css y source/public/css.');
	console.log('Tailwind permanece como asset precompilado local.');
} finally {
	rmSync(temporaryDirectory, { recursive: true, force: true });
}