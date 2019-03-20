<?php
namespace PhHelpers\Field;

use PhHelpers\Field\AbstractField;

class Email extends AbstractField
{
	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html()
	{
		$renderer = new \PhHelpers\View\Renderer();
		return $renderer->render('ph-helpers/email.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'value' => $this->value
		));
	}

	public function validate()
	{
		$this->value = trim($this->value);

		if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
			return [__('This is not a valid email address.')];
		}

		return [];
	}
}
