# Frakt WP Generator — Handoff para otro agente

Fecha del handoff: 2026-07-24  
Repositorio local: `C:\laragon\www\developer-plugins\wp-content\plugins\frakt-wp-framework`  
Sitio WordPress local: `http://developer-plugins.test`  
Idioma de trabajo con el usuario: español

## Objetivo general

El usuario está modernizando Frakt WP Generator, un plugin de WordPress que genera otros plugins a partir de la plantilla ubicada en `source/`.

Durante esta conversación se trabajó en:

1. Migrar la generación tradicional por POST a AJAX.
2. Eliminar la ejecución de Composer en el servidor.
3. Entregar los plugins generados sin `vendor/`.
4. Informar al usuario que debe ejecutar Composer localmente.
5. Autocompletar metadatos mientras se escribe el nombre del plugin.
6. Transformar el formulario largo en un asistente de cinco pasos.
7. Permitir incluir o excluir módulos de arquitectura del plugin generado.

## Estado de Git

Último commit observado:

```text
2d1f363 ✨ Update plugin generator to version 2.1.0: enhance AJAX functionality, improve user feedback, and add new styling for results and notifications.
```

La migración AJAX correspondiente a la versión `2.1.0` ya está incluida en ese commit.

Al crear este documento existen cambios sin commit correspondientes principalmente a la versión `2.2.0`:

```text
M README.txt
M public/class-tkt-plugin-generator-public.php
M public/css/tkt-plugin-generator-public.css
M public/js/tkt-plugin-generator-public.js
M public/partials/tkt-plugin-generator-public-display.php
M readme.md
M source/includes/class-plugin-name.php
M source/plugin-name.php
M tkt-plugin-generator.php
```

No descartar estos cambios ni ejecutar `git reset --hard`.

## Funcionalidad AJAX implementada en 2.1.0

El flujo anterior utilizaba un POST tradicional en `template_redirect`, ejecutaba Composer y devolvía el ZIP en la misma petición.

El flujo nuevo es:

```text
Formulario
  → POST AJAX: tkt_generate_plugin
  → validación de nonce
  → generación en directorio único
  → creación de ZIP
  → respuesta JSON con URL temporal
  → GET AJAX: tkt_download_plugin
  → descarga y limpieza del paquete
```

Hooks registrados:

```php
wp_ajax_tkt_generate_plugin
wp_ajax_nopriv_tkt_generate_plugin
wp_ajax_tkt_download_plugin
wp_ajax_nopriv_tkt_download_plugin
```

El shortcode `[generate_plugin]` se registra en `init`.

### Decisiones sobre Composer

- El servidor no ejecuta `composer install`.
- Ya no existe uso activo de `shell_exec()`.
- El ZIP generado excluye completamente `vendor/`.
- Si el usuario selecciona dependencias, se conserva un `composer.json` filtrado.
- Se incluye `COMPOSER-INSTALL.md` dentro del plugin generado.
- La interfaz muestra este comando:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
```

- Si no se seleccionan dependencias:
  - se elimina `composer.json`;
  - se eliminan los bloques que cargan Composer;
  - el plugin puede activarse sin ejecutar Composer.
- Si existen dependencias, el archivo principal del plugin generado verifica primero que exista `vendor/autoload.php` y muestra un aviso en lugar de provocar un fatal.

### Dependencias disponibles

- Secure Custom Fields.
- Query Monitor.
- WP Crontrol.
- User Switching.
- Plugin Check.
- Transients Manager.
- JWT Authentication for WP REST API.

JWT recibió un cargador real en la plantilla:

```php
vendor/jwt-authentication-for-wp-rest-api/jwt-auth.php
```

### Descargas

- Cada generación utiliza un UUID para evitar colisiones.
- La descarga utiliza un token almacenado en un transient.
- El token dura 15 minutos.
- La ruta se verifica para asegurar que permanece dentro de `builds/`.
- El directorio del trabajo se elimina después de descargar.

## Autocompletado implementado

El JavaScript está en:

```text
public/js/tkt-plugin-generator-public.js
```

Mientras se escribe `Plugin Human Name`, se completan:

- Plugin Name.
- Slug sin acentos.
- Prefijo basado en consonantes.
- Versión.
- Stable tag.
- WordPress mínimo.
- PHP mínimo.
- Tested up to.
- Tags.
- Descripción.
- URL del plugin.

Ejemplo:

```text
Human Name: Mi Súper Plugin 2026
Slug: mi-super-plugin-2026
Prefix: m_spr_plgn_2026
Description: Mi Súper Plugin 2026 es un plugin para WordPress.
```

Reglas importantes:

- Un campo deja de autocompletarse cuando el usuario lo modifica manualmente.
- Cambiar el slug actualiza prefijo y URL mientras esos campos sigan automáticos.
- Cambiar la versión actualiza el stable tag mientras no haya sido editado.
- Cambiar la URL del autor actualiza la URL del plugin mientras siga automática.

## Asistente de cinco pasos implementado en 2.2.0

La versión actual del plugin fue elevada a:

```text
2.2.0
```

Pasos:

1. Información básica.
2. Autor y compatibilidad.
3. Arquitectura.
4. Dependencias.
5. Revisión y descarga.

### Comportamiento del asistente

- El formulario continúa siendo un único `<form>`.
- JavaScript agrupa los nodos con `data-wizard-step` en cinco `fieldset`.
- Se utiliza un stepper superior.
- Los botones futuros permanecen deshabilitados hasta alcanzarlos.
- “Continuar” valida solamente el paso actual.
- Se usa `form.noValidate = true` y validación manual para evitar errores con campos requeridos dentro de pasos ocultos.
- El foco se mueve al `legend` del nuevo paso.
- El primer campo inválido recibe foco y ejecuta `reportValidity()`.
- “Anterior” conserva todos los valores.
- El quinto paso genera un resumen editable.
- Cada sección del resumen tiene un botón “Editar”.
- El botón final continúa enviando el mismo AJAX de generación.

### Persistencia

El estado del formulario se guarda en:

```text
sessionStorage["tktPluginGeneratorDraft"]
```

No se guarda el nonce.

El borrador se elimina después de una generación exitosa.

## Arquitectura seleccionable

Por ahora solo está implementada:

```text
Frakt clásica
```

“Modular” y “Namespaces + PSR-4” quedan como ampliaciones futuras.

Módulos disponibles:

- Administración.
- Público.
- Shortcode de ejemplo.
- Internacionalización.
- Hooks de activación y desactivación.
- Archivo de desinstalación.

Si se desactiva el módulo público, JavaScript desactiva también el shortcode y PHP fuerza:

```php
$new_data['include_shortcode'] = false;
```

### Efecto real sobre el ZIP

No son solamente opciones visuales. El generador elimina archivos y referencias:

| Opción | Resultado al desactivarla |
|---|---|
| `include_admin` | Elimina `admin/` y las referencias/clases administrativas |
| `include_public` | Elimina `public/` y las referencias/clases públicas |
| `include_shortcode` | Elimina el registro del shortcode |
| `include_i18n` | Elimina el cargador i18n, su clase y `languages/` |
| `include_lifecycle` | Elimina hooks y clases de activación/desactivación |
| `include_uninstall` | Elimina `uninstall.php` |

Los bloques condicionales utilizan marcadores:

```php
/* TKT_GEN_ADMIN_START */
/* TKT_GEN_ADMIN_END */

/* TKT_GEN_PUBLIC_START */
/* TKT_GEN_PUBLIC_END */

/* TKT_GEN_SHORTCODE_START */
/* TKT_GEN_SHORTCODE_END */

/* TKT_GEN_I18N_START */
/* TKT_GEN_I18N_END */

/* TKT_GEN_LIFECYCLE_START */
/* TKT_GEN_LIFECYCLE_END */
```

El método `process_architecture_files()` elimina los archivos físicos que no corresponden.

## Archivos principales

### Generador y endpoints

```text
public/class-tkt-plugin-generator-public.php
```

Contiene:

- endpoint AJAX;
- generación del paquete;
- descarga temporal;
- exclusión de `vendor/`;
- procesamiento de `composer.json`;
- procesamiento de módulos;
- escritura de instrucciones;
- limpieza segura.

### Formulario

```text
public/partials/tkt-plugin-generator-public-display.php
```

Contiene:

- campos originales;
- atributos `data-wizard-step`;
- selector de arquitectura;
- checkboxes de módulos;
- contenedor de revisión;
- contenedor de resultado.

### JavaScript

```text
public/js/tkt-plugin-generator-public.js
```

Contiene:

- autocompletado;
- preservación de campos manuales;
- construcción de fieldsets;
- navegación;
- validación;
- stepper;
- resumen;
- persistencia;
- AJAX;
- descarga;
- instrucciones de Composer.

### CSS

```text
public/css/tkt-plugin-generator-public.css
```

Contiene:

- overlay;
- resultados;
- stepper;
- fieldsets;
- navegación;
- revisión;
- estilos responsive.

### Plantilla generada

```text
source/plugin-name.php
source/includes/class-plugin-name.php
```

Contienen los marcadores de módulos, guardas de Composer y cargadores opcionales.

## Pruebas realizadas

### Sintaxis

Pasaron sin errores:

```bash
php -l tkt-plugin-generator.php
php -l includes/class-tkt-plugin-generator.php
php -l public/class-tkt-plugin-generator-public.php
php -l public/partials/tkt-plugin-generator-public-display.php
php -l source/plugin-name.php
php -l source/includes/class-plugin-name.php
node --check public/js/tkt-plugin-generator-public.js
git diff --check
```

### WordPress

WordPress reconoce:

```json
{
  "shortcode": true,
  "ajax": true,
  "version": "2.2.0"
}
```

El plugin está activo.

### Generación con Composer

Se probaron dos paquetes:

#### Sin dependencias

- Sin `composer.json`.
- Sin `COMPOSER-INSTALL.md`.
- Sin `vendor/`.
- Sin guardas de Composer.
- Sin marcadores pendientes.

#### Con SCF y JWT

- Con `composer.json`.
- Con `COMPOSER-INSTALL.md`.
- Sin `vendor/`.
- Con guarda de Composer.
- Con cargador JWT.
- Sin marcadores pendientes.

### Arquitectura

Se probaron dos ZIP:

#### Completo

- `admin/`: presente.
- `public/`: presente.
- i18n: presente.
- activador/desactivador: presentes.
- `uninstall.php`: presente.
- shortcode: presente.
- sin marcadores.

#### Mínimo

- `admin/`: ausente.
- `public/`: ausente.
- i18n: ausente.
- activador/desactivador: ausentes.
- `uninstall.php`: ausente.
- shortcode: ausente.
- sin referencias ejecutables a clases eliminadas.
- sin marcadores.

### Interfaz

Como no había un navegador conectado en la sesión, no se pudo realizar una inspección visual real.

Se ejecutó una prueba DOM aislada con JSDOM y pasó:

```text
wizard-dom-tests-passed
```

La prueba cubrió:

- creación de cinco fieldsets;
- cinco botones del stepper;
- navegación;
- validación;
- autocompletado;
- preservación manual;
- relación público/shortcode;
- resumen final;
- persistencia en `sessionStorage`.

Las dependencias y scripts temporales de prueba fueron eliminados.

## Particularidades del entorno

El comando global `wp` utiliza PHP 8.3.16 y en esa configuración la extensión ZIP aparece deshabilitada.

El comando:

```bash
php C:\wp-cli\wp-cli.phar ...
```

utiliza PHP 8.1.10, donde ZIP sí está habilitado. Las generaciones reales se validaron con esa versión.

Si el servidor web utiliza la configuración de PHP 8.3, debe habilitarse `php_zip`.

También aparece esta advertencia del entorno:

```text
Deprecated: Directive 'allow_url_include' is deprecated
```

No proviene de los cambios realizados.

## Limitaciones y deuda técnica conocida

1. Los endpoints AJAX públicos continúan registrados con `wp_ajax_nopriv_*` para conservar el comportamiento público anterior.
2. Un nonce público no es autenticación.
3. Aún no hay rate limiting, CAPTCHA ni restricción por capability.
4. Un ZIP descargado se elimina, pero un ZIP cuyo token expira sin descargarse puede permanecer hasta que exista una rutina de limpieza.
5. Conviene añadir limpieza programada de trabajos expirados.
6. `source/vendor/` continúa dentro del repositorio y pesa aproximadamente 31.6 MiB, aunque ya no se copia al ZIP.
7. En `public/class-tkt-plugin-generator-public.php` todavía existen métodos heredados del flujo tradicional, como `replace_zip_and_download()`, `redirect_with_error()`, `download_zip()`, `delete_file()` y `validate_post_and_nonce()`. Ya no contienen ejecución de Composer y no están conectados a los hooks actuales, pero deberían eliminarse en una refactorización.
8. La arquitectura modular y PSR-4 todavía no están implementadas.
9. Falta una inspección visual en navegador real.
10. Falta ejecutar PHPCS/WordPress Coding Standards si se desea homologar el estilo del archivo heredado.

## Próximos pasos recomendados

Orden sugerido:

1. Abrir una página con `[generate_plugin]` y hacer una revisión visual real.
2. Probar navegación móvil y teclado.
3. Probar una generación desde el formulario completo, no solo mediante invocación controlada.
4. Eliminar el código heredado del flujo POST tradicional.
5. Implementar limpieza programada de ZIP expirados.
6. Decidir si el generador será:
   - solo para administradores;
   - para usuarios autenticados;
   - o público con rate limiting/CAPTCHA.
7. Añadir validación visual específica para slug, prefijo y SemVer.
8. Añadir exportación/importación de configuración.
9. Implementar presets.
10. Diseñar plantillas reales para arquitectura modular y PSR-4.
11. Ejecutar PHPCS y corregir solamente los archivos en alcance.
12. Revisar el diff y crear el commit de la versión `2.2.0`.

## Comandos útiles para retomar

Estado:

```powershell
git status --short
git diff --stat
git diff --check
```

Sintaxis:

```powershell
php -l public/class-tkt-plugin-generator-public.php
php -l public/partials/tkt-plugin-generator-public-display.php
php -l source/plugin-name.php
php -l source/includes/class-plugin-name.php
node --check public/js/tkt-plugin-generator-public.js
```

WordPress:

```powershell
php C:\wp-cli\wp-cli.phar plugin status frakt-wp-framework --path=C:\laragon\www\developer-plugins
```

Buscar Composer ejecutado en servidor:

```powershell
rg -n -S "shell_exec|composer install omitted|composer\.log" . -g "!source/vendor/**"
```

Debe regresar sin coincidencias activas.

## Resultado esperado para el usuario

El usuario debe poder:

1. Abrir una página con `[generate_plugin]`.
2. Completar el asistente de cinco pasos.
3. Obtener campos derivados automáticamente.
4. Elegir módulos de arquitectura.
5. Elegir dependencias.
6. Revisar el resumen.
7. Generar mediante AJAX.
8. Descargar el ZIP.
9. Si eligió dependencias, descomprimir y ejecutar:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
```

10. Activar el plugin generado.

