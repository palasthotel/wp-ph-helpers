<?php
namespace PhHelpers\Field;

use PhHelpers\Field\AbstractField;

class Textarea extends AbstractField
{
	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html()
	{
		$renderer = new \PhHelpers\View\Renderer();
		return $renderer->render('ph-helpers/textarea.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'value' => $this->value
		));
	}
}
