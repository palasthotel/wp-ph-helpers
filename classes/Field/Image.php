<?php
namespace PhHelpers\Field;

use PhHelpers\Field\AbstractField;

class Image extends AbstractField
{
	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html()
	{
		wp_enqueue_media();
		$renderer = new \PhHelpers\View\Renderer();
		return $renderer->render('ph-helpers/image.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'value' => $this->value
		));
	}
}
