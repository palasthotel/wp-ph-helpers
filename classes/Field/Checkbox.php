<?php
namespace PhHelpers\Field;

use PhHelpers\Field\AbstractField;

class Checkbox extends AbstractField
{
	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html()
	{
		$renderer = new \PhHelpers\View\Renderer();
		return $renderer->render('ph-helpers/checkbox.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'value' => $this->value
		));
	}

	public function validate()
	{
		return [];
	}

	public function filter($value){
		if($value == 'on'){
			return true;
		}
		return false;
	}
}
