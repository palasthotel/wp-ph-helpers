WordPress Helper Classes
==

Adds an object oriented way to programmatically create post types and terms.

Description
--

The plugin adds a bunch of helper classes for more straight forward plugin
developement and the creation of custom post types. Keep in mind, that this
plugin is still under active developement and can contain a bunch of bugs.

Installation
--

1. Upload `wp-ph-helpers.zip` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You’re done!

**ATTENTION** this plugin is still under active developement and may come with some bugs. Use it only if you know what you are doing.

How to use it?
--

To create new post-types or taxonomy-terms you have to register to the ```ph_helpers_register_content```-action inside of your theme or plugin. New post-types are created using the ```\PhHelpers\Post```-class, for new terms you can use the ```\PhHelpers\Term```-class.

```php
<?php
add_action('ph_helpers_register_content', 'my_plugin_register_content');
function my_plugin_register_content(){

    	// create a location post type
    	$location = new \PhHelpers\Post('Location', 'location');

    	// add a field "about" which is required (third param is true)
    	$location->addField(new \PhHelpers\Field\Textarea('About', 'location-about', true));

    	// create a new event content type
    	$event = new \PhHelpers\Post('Event', 'event');

        // create a new Term
        $term = new \PhHelpers\Term("Category", "my_term");

        // add a field to the term
        $term->addField(new \PhHelpers\Field\Text("Second title", "subtitle"));

        // add the term to wordpress
        $term->persist();

    	// add the new term to some post types
    	$event->addTerm($term);
        $location->addTerm($term);

    	// add a from field which is required and a optional to field
    	$event->addField(new \PhHelpers\Field\Date('From', 'event-from', true));
    	$event->addField(new \PhHelpers\Field\Date('To', 'event-to'));

    	// add a reference field to the location of the event
    	$eventLocation = new \PhHelpers\Field\Reference('Location', 'event-location', true);
    	$eventLocation->setTarget($location);
    	$event->addField($eventLocation);

    	// add the content types to wordpress
    	$event->persist();
    	$location->persist();
}
```

This will create two custom post types, the location post type with only the
title and an about field and a event post type with a date from, to and a
location field, where you can choose from existing location posts.

There some other fields which you can use for your custom post_type. At the
moment the following fields exist:

- \PhHelpers\Field\Text
- \PhHelpers\Field\Textarea
- \PhHelpers\Field\Wysiwyg
- \PhHelpers\Field\Email
- \PhHelpers\Field\Date
- \PhHelpers\Field\Reference
- \PhHelpers\Field\ReferenceMulti
- \PhHelpers\Field\Image
- \PhHelpers\Field\File

You can create your own custom fields by extending the class
\PhHelpers\Field\AbstractField and overwriting the html() and/or validate()
method. A good example of a custom field is the Email Field:

```php
<?php
namespace PhHelpers\Field;

use \PhHelpers\Field\AbstractField;

class Email extends AbstractField{
	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html(){
	 	return "<input type='email' placeholder='$this->label' class='regular-text' name='$this->slug' value='$this->value' />";
	}

	public function validate(){

		$this->value = trim($this->value);

		if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
		  return [
				__('This is not a valid email address.')
			];
		}

		return [];
	}
}
```

The Renderer
------------

If you want to render templates inside your own plugin you can use the renderer
provided in the `\PhHelpers\View\Renderer` class. The renderer takes care of
searching the template file in the order "Child-Theme", "Theme", "Plugins"
inside a given subfolder.

```php
<?php
$renderer = new \PhHelpers\View\Renderer();
$renderer->setThemeFolder('plugin-parts');
$html = $renderer->render('my-template.php', array(
	'var1' => 'hello',
	'var2' => 'world'
));
echo $html;
```

In the example above the template file *my-template.php* inside the subfolder
*plugin-parts* (if no themeFolder is provided, *"parts"* will be used) will be searched first in the active theme, after that in the
parent themes and at last inside one of the plugin folders. If the template is
found, it will be called, otherwise an error will be outputted in the frontend. In the template you can access the variables *var1*
and *var2* by simply calling *$var1* and *$var2*.
