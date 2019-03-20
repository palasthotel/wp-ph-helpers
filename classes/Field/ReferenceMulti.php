<?php
namespace PhHelpers\Field;

use PhHelpers\Field\AbstractField;

class ReferenceMulti extends AbstractField implements CustomSaveInterface
{
	protected $target;

	public function setTarget($target)
	{
		$this->target = $target;
	}

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
		return $renderer->render('ph-helpers/reference-multiple.php', array(
			'label' => $this->label,
			'slug' => $this->slug,
			'target' => $this->target->getSlug(),
			'value' => $this->value
		));
	}

	/**
	 * Implementation of the save function
	 * @param int $post_id
	 * @param string $value
	 * @param boolean $isTerm
	 * @return void
	 */
	public function save($entity_id, $value, $entity_type = 'post'){

		$value = explode(',', $value);

		if(is_plugin_active('content-relations/ph-content-relations.php')
			&& get_option( 'ph-helpers-use-content-relations', false ) == true
			&& $entity_type == 'post'){

			// save with the content-relations api
			$store = \content_relations_get_store($entity_id);
			$store->clearByType($this->slug);
			foreach($value as $target){
				$store->add_relation($entity_id, $target, $this->slug);
			}
		}else{

			foreach($value as $target){

				switch($entity_type){
					case 'post':
						delete_post_meta($entity_id, $this->getSlug());
						update_post_meta( $entity_id, $this->getSlug(), $target );
						break;
					case 'term':
						delete_term_meta($entity_id, $this->getSlug());
						add_term_meta( $entity_id, $this->getSlug(), $target );
						break;
					default:
						throw new \Exception("The entity type $entity_type is not supported by this field.");
				}
			}
		}
	}

	/**
	 * Implementation of the load function
	 * @param int $post_id
	 * @return void
	 */
	public function load($entity_id, $entity_type = 'post'){
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if(is_plugin_active('content-relations/ph-content-relations.php' && $entity_type == 'post')
			&& get_option( 'ph-helpers-use-content-relations', false ) == true){

			$store = \content_relations_get_store($entity_id);
			$data = $store->get_relations_by_type($this->slug, $source_only = true);
			$ids = [];
			foreach($data as $relation){
				$ids[] = $relation->target_id;
			}
			return implode(',', $ids);
		}else{

			$data = null;
			switch($entity_type){
				case 'post':
					$data = get_post_meta($entity_id, $this->getSlug(), false);
					break;
				case 'term':
					$data = get_term_meta($entity_id, $this->getSlug(), false);
					break;
				default:
					throw new \Exception("The entity type $entity_type is not supported by this field.");
			}

			if($data){
				if(is_array($data)){
					return implode(',', $data);
				}else{
					return $data;
				}
			}
		}
	}
}
