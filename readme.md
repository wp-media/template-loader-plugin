# WP Media Template Loader Plugin

This plugin enables loading of specific templates regardless of the currently active
WordPress theme.

## Requirements

* PHP 5.6+
* WordPress (should work with nearly any version)

## Installation & Use

1. Clone into `/wp-content/plugins`.
2. Create a `/wp-content/qa-templates` directory.
3. Add a default `template.php` file into the `qa-templates` directory.
(See the sample below for a quick one you can use for this.)
4. Add any other templates you want to load into the `qa-templates` directory.
5. Create a new page with the slug `qa-template`.
6. Choose the template you want to load from the "Choose Test Template" metabox.
7. Navigate to the `qa-template` page to see your template in action.

### Sample `template.php` file

```
<?php
wp_head();
echo 'This is the default custom template.';
wp_footer();
```