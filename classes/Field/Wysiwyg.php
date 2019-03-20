<?php
namespace PhHelpers\Field;

use PhHelpers\Field\AbstractField;

class Wysiwyg extends AbstractField
{
	protected $teeny = false;

	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html()
	{
		$settings = array(
			'teeny' => $this->teeny,
			'textarea_rows' => 15,
			'tabindex' => 1
		);

		ob_start();
		wp_editor($this->value, $this->getSlug(), $settings);
		$editor = ob_get_clean();

		return $editor;
	}

	/**
	 * Change to a small version of the editor
	 * @return Wysiwyg
	 */
	public function teeny(){
		$this->teeny = true;
		return $this;
	}
}
