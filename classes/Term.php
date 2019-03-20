<?php
namespace PhHelpers;

use Cocur\Slugify\Slugify;

/**
 * Term class for creating new terms inside wordpress
 *
 * Example usage:
 * $test = new \PhHelpers\Term("Test", "test");
 * $test->addField(new \PhHelpers\Field\Wysiwyg("Description", "description", true));
 * $test->persist();
 *
 * @package PhHelpers
 * @author PALASTHOTEL <rezeption@palasthotel.de> in person Maximilian Strehse
 * @version dev
 */
class Term
{
    /**
     * label
     * @var string
     */
	protected $label;

    /**
     * slug
     * @var string
     */
	protected $slug;

    /**
     * hierarchical
     * @var boolean
     */
	protected $hierarchical = true;

    /**
     * Prevent duble persist
     * @var boolean
     */
    protected $persisted = false;

    /**
     * Content Store
     * @var \PhHelpers\ContentStore
     */
    protected $store;

    /**
     * fields
     * @var \PhHelpers\Field\AbstractField[]
     */
    protected $fields;

	public function __construct(
		$label,
		$slug = null,
		$fields = null
	) {
		$this->label = $label;

		if (!$slug) {
			$slugify = new Slugify();
			$slug = $slugify->slugify($label);
		}

		$this->slug = $slug;
		$this->fields = $fields;

        $this->store = \PhHelpers\ContentStore::instance();

		add_action('created_' . $this->slug, array($this, 'save_term_callback'), 10, 2);
		add_action('edited_' . $this->slug, array($this, 'save_term_callback'), 10, 2);
	}

	/**
	 * Generate the post type and taxonomies
	 * @return void
	 */
	public function persist()
	{
        // only register once
        if($this->persisted == true){
            return;
        }

        $this->store->addTerm($this);

		add_action( $this->slug . '_add_form_fields', array($this, 'render_fieldbox_callback'), 10, 2 );
		add_action( $this->slug . '_edit_form_fields', array($this, 'render_fieldbox_callback'), 10, 2 );
	}

	public function save_term_callback($term_id, $taxonomy_term_id = null)
	{
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $term_id;
		}

		if ($this->fields && !empty($this->fields)) {
			foreach ($this->fields as $field) {

				$value = null;

				if(isset($_POST[$field->getSlug()])){
					$value = $_POST[$field->getSlug()];
				}

				// if the field wants to save itself
				if($field instanceof \PhHelpers\Field\CustomSaveInterface ){
					$value = $field->filter($value);
					$field->save($term_id, $value, 'term');
				}else{

					$old = get_term_meta($term_id, $field->getSlug(), true);
					$new = $field->filter($value);

					if($new == null){
						delete_term_meta($term_id, $field->getSlug());
					}

					if ($new && $new !== $old) {
						update_term_meta($term_id, $field->getSlug(), $new);
					} elseif ('' === $new && $old) {
						delete_term_meta($term_id, $field->getSlug(), $old);
					}
				}
			}
		}
	}

	/**
	 * Returns the slug of the content type
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	public function render_fieldbox_callback($term_id)
	{
		if ($this->fields && !empty($this->fields)) {
			$term = get_term($term_id);
			$renderer = new \PhHelpers\View\Renderer();
			$html = $renderer->render('ph-helpers/term-field-list.php', array(
				'fields' => $this->fields,
				'term' => $term
			));

			print $html;
		}
	}

    /**
     * Get the value of label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param string label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the value of slug
     *
     * @param string slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of hierarchical
     *
     * @return boolean
     */
    public function getHierarchical()
    {
        return $this->hierarchical;
    }

    /**
     * Set the value of hierarchical
     *
     * @param boolean hierarchical
     *
     * @return self
     */
    public function setHierarchical($hierarchical)
    {
        $this->hierarchical = $hierarchical;

        return $this;
    }

    /**
     * Get the value of fields
     *
     * @return \PhHelpers\Field\AbstractField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the value of fields
     *
     * @param \PhHelpers\Field\AbstractField[] fields
     *
     * @return self
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Add a field
     * @param \PhHelpers\Field\AbstractField field
     * @return self
     */
    public function addField(\PhHelpers\Field\AbstractField $field){
        $this->fields[] = $field;
        return $this;
    }
}
