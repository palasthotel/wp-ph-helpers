<?php
namespace PhHelpers\Field;

use PhHelpers\Field\ReferenceMulti;

class Reference extends ReferenceMulti implements CustomSaveInterface
{
	public function html()
	{

		wp_enqueue_script(
			'ph-helpers-skripts',
			plugins_url('ph-helpers/ph-helpers.js')
		);
		wp_enqueue_style(
			'ph-helpers-skripts',
			plugins_url('ph-helpers/ph-helpers.css')
		);

		$renderer = new \PhHelpers\View\Renderer();
		return $renderer->render('ph-helpers/reference.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'target' => $this->target->getSlug(),
			'value' => $this->value
		));
	}
}
