<?php
namespace PhHelpers\Field;

interface CustomSaveInterface{

	/**
	 * Save the field
	 * @param int $post_id
	 * @param mixed $value
	 * @return void
	 */
	public function save($post_id, $value);

	/**
	 * Returns the value of the field
	 * @param mixed $post
	 * @return mixed
	 */
	public function load($post_id);
}
