# Post To PDF

## Description

Adds a URL suffix which allows the site visitor to download the selected Post as a PDF.

PDF generation occurs using an HTML document formatted with CSS, using [Michael Härtl's PHP code](https://github.com/mikehaertl/phpwkhtmltopdf) and the [wkhtmltopdf](https://wkhtmltopdf.org/) command line tool.

### Licenses

* [phpwkhtmltopdf by Mike Härtl](https://github.com/mikehaertl/phpwkhtmltopdf/) is provided under the terms of the [MIT](https://github.com/mikehaertl/phpwkhtmltopdf/blob/master/LICENSE) license.
* The [wkhtmltopdf](https://github.com/wkhtmltopdf/wkhtmltopdf) command line tool is provided under the terms of the GPL v3 license.

## Usage

* Install the plugin. This will flush the rewrite hooks, so that the custom rewrite endpoint `shpdf` becomes available.
* Call your Post using the additional URL suffix `shpdf/` (e.g. https://example.com/2019/03/my-post/shpdf/).
* The plugin contains a default, very simple PDF template. You can override it simply by adding your own template to your Theme or by passing an array of possible template paths.

### Allowed post types

By default, only Posts of type `post` and `page` are permitted. To allow rendering of other Post Types, use the following hook in your Theme.

```php
add_filter('hello-post-to-pdf/allowed-post-types', function(array $allowed_post_types){
	$allowed_post_types[] = 'custom-post-type';
	return $allowed_post_types;
}, 10, 1);
```

### Template override: predefined file

If a file named _single-hello-post-to-pdf.php_ exists in the root folder of your Theme, this will be used in preference over the plugin's own template.

### Template override: hook

The best way to override the rendering with your own template is through the use of the following hook in your Theme.

You need to always return an array of file paths which are relative to the Theme root folder. Even if you want to use one specific template.

Example:

```php
add_filter('hello-post-to-pdf/theme-templates', function(array $template_paths){
	$template_paths = [
		'templates/' .get_post_type($post_id). 'post-to-pdf.php',
		'templates/post-to-pdf.php'
	]
	return $template_paths;
}, 10, 1);
```

### Modifying the PDF generation wrapper options

You can modify the standard set of [PDF wrapper options](https://github.com/mikehaertl/phpwkhtmltopdf#wrapper-options) by using the `hello-post-to-pdf/generation-settings` hook.

```php
add_filter('hello-post-to-pdf/generation-settings', function(array $settings){
	return array_merge($settings, [
		'margin-top' => 20,
		'margin-bottom' => 20
	]);
}, 10, 1);
```

## Changelog

### 2.0.0

* Improves previous code.
* Adds safety checks.
* Adds `hello-post-to-pdf/generation-settings` filter.
* Extends README and provides licensing information.

### 1.1.0

* Fixes for Coding Standards.
* More complete config example.
* Adds language suffix to filename if WPML is active.

### 1.0.1

* Fix allowed post types sequencing.

### 1.0.0

* Add simple template.
* Add hooks.
* Add PDF rendering.
* Add usage instructions.

### 0.0.1

* Initial development version.

## Contributors

* Say Hello GmbH - Mark Howells-Mead (mark@sayhello.ch)

## License

Use of this code provides and implies no guarantee. Please respect the GPL v3 licence, which is available via http://www.gnu.org/licenses/gpl-3.0.html
