<?php
namespace PhHelpers\Field;

use Cocur\Slugify\Slugify;

class AbstractField
{
	protected $type = 'text';
	protected $label;
	protected $slug;
	protected $value;
	protected $required;
	protected $errors = [];

	public function __construct($label, $slug, $required = false)
	{
		$this->label = $label;
		$this->required = $required;

		if ($slug == null) {
			$slugify = new Slugify();
			$slug = $slugify->slugify($label);
		}

		$this->slug = $slug;
	}

	/**
	 * Set the field to required
	 * @return mixed
	 */
	public function required()
	{
		$this->required = true;
		return $this;
	}

	/**
	 * Set the value of the field
	 * @return mixed
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * Returns the html of the field
	 * @return string
	 */
	public function html()
	{
		$renderer = new \PhHelpers\View\Renderer();
		return $renderer->render('ph-helpers/input.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'value' => $this->value
		));
	}

	/**
	 * Returns the label of the field
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Check if the field is valid
	 * returns boolean
	 */
	public function getErrors()
	{
		$this->errors = [];
		$this->errors = $this->validate();

		if ($this->required == true && !trim($this->value)) {
			$this->errors = [
				sprintf(
					esc_html__('The field %s is required.', 'ph-helpers'),
					$this->getLabel()
				)
			];
			return $this->errors;
			\array_unshift(
				$this->errors,
				sprintf(
					esc_html__('The field %s is required.', 'ph-helpers'),
					$this->getLabel()
				)
			);
		}

		return $this->errors;
	}

	/**
	 * Check if the field has errors
	 * @return boolean
	 */
	public function hasErrors()
	{
		if (empty($this->getErrors())) {
			return false;
		}
		return true;
	}

	/**
	 * Validation of the input
	 * return boolean
	 */
	protected function validate()
	{
		return [];
	}

	public function filter($value){
		return $value;
	}
}
