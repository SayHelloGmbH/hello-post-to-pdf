# Post To PDF

## Description

Adds a URL suffix which allows the site visitor to download the selected Post as a PDF.

## Usage

* Install the plugin. This will flush the rewrite hooks, so that the custom rewrite endpoint `shpdf` becomes available.
* Call your Post using the additional URL suffix `shpdf/` (e.g. https://example.com/2019/03/my-post/shpdf/).

The plugin contains a default, very simple PDF template. You can override it with a template in your Theme.

### Allowed post types

By default, only Posts of type `post` are permitted. To allow rendering of other Post Types, use the following hook in your Theme.

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
add_filter('hello-post-to-pdf/theme-templates', function(array $paths){
	switch (get_post_type(get_the_ID())) {
		case 'rezepte':
			return ['single-rezepte-pdf.php'];
		break;
	}
	return $paths;
}, 10, 1);
```

## Changelog

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

* Mark Howells-Mead (mark@sayhello.ch)

## License

Use this code freely, widely and for free. Provision of this code provides and implies no guarantee.

Please respect the GPL v3 licence, which is available via http://www.gnu.org/licenses/gpl-3.0.html