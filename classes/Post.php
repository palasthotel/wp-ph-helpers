<?php

namespace PhHelpers;

use Cocur\Slugify\Slugify;

/**
 * Post class for creating new post-types inside wordpress
 *
 * Example usage:
 * $test = new \PhHelpers\Post("Test", "test");
 * $test->addField(new \PhHelpers\Field\Wysiwyg("Description", "description", true));
 * $test->persist();
 *
 * @package PhHelpers
 * @author PALASTHOTEL <rezeption@palasthotel.de> in person Maximilian Strehse
 * @version dev
 */
class Post {

    /**
     * label
     * @var string
     */
	protected $label;

    /**
     * plural label
     * @var string
     */
    protected $plural;

    /**
     * slug
     * @var string
     */
	protected $slug;

    /**
     * description
     * @var string
     */
	protected $description;

    /**
     * fields
     * @var \PhHelpers\Field\AbstractField[]
     */
	protected $fields = [];

    /**
     * is public
     * @var boolean
     */
	protected $public = true;

    /**
     * archive
     * @var boolean
     */
	protected $archive = true;

    /**
     * supports
     * @var string[]
     */
	protected $supports = [ 'title', 'revisions', 'thumbnail', 'author' ];

    /**
     * rewrite
     * @var array
     */
	protected $rewrite = [ 'slug' => '', 'with_front' => true ];

    /**
     * terms
     * @var \PhHelpers\Term[]
     */
    protected $terms = [];

    /**
     * post store
     * @var \PhHelpers\Post\PostTypeStore
     */
	protected $store;

    /**
     * persisted
     * @var boolean
     */
    protected $persisted = false;

    /**
     * Constructor
     * @param string label
     * @param string slug (optional)
     * @param string description (optional)
     */
	public function __construct(
		$label,
		$slug = null,
		$description = null
	) {
		$this->label = $label;
        $this->plural = $label.'s';

		if ( ! $slug ) {
			$slugify = new Slugify();
			$slug    = $slugify->slugify( $label );
		}

		$this->slug        = $slug;
		$this->description = $description;

		if ( empty( $this->rewrite['slug'] ) ) {
			$this->rewrite['slug'] = $this->slug;
		}

		$this->store = \PhHelpers\ContentStore::instance();

		add_action( 'save_post', array( $this, 'save_post_callback' ) );
	}

	/**
	 * Make the post-type available inside wordpress
	 * @return void
	 */
	public function persist() {

        if($this->persisted == true){
            return;
        }

        $this->persisted = true;

		$this->store->addPost( $this );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_callback' ) );
	}

    /**
     * Add metabox callback
     * Normally you dont have to call this function manually.
     * @return void
     */
	public function add_meta_box_callback() {
		if ( $this->fields ) {
			\add_meta_box(
				$this->slug . '-fields',
				'Eigenschaften',
				array( $this, 'render_fieldbox_callback' ),
				$this->slug,
				'normal',
				'high'
			);
		}
	}

    /**
     * Save post callback
     * Normally you dont have to call this function manually.
     * @param int $post_id
     * @return int
     */
	public function save_post_callback( $post_id ) {

		if ( ! isset( $_POST['post_type'] ) ) {
			return;
		}

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( $this->slug === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		if ( $this->fields && ! empty( $this->fields ) && $this->slug === $_POST['post_type'] ) {
			foreach ( $this->fields as $field ) {

				$value = null;

				if ( isset( $_POST[ $field->getSlug() ] ) ) {
					$value = $_POST[ $field->getSlug() ];
				}

				// if the field wants to save itself
				if ( $field instanceof \PhHelpers\Field\CustomSaveInterface ) {
					$value = $field->filter( $value );
					$field->save( $post_id, $value );
				} else {

					$old = get_post_meta( $post_id, $field->getSlug(), true );
					$new = $field->filter( $value );

					if ( $new == null ) {
						delete_post_meta( $post_id, $field->getSlug() );
					}

					if ( $new && $new !== $old ) {
						update_post_meta( $post_id, $field->getSlug(), $new );
					} elseif ( '' === $new && $old ) {
						delete_post_meta( $post_id, $field->getSlug(), $old );
					}
				}
			}
		}
	}

    /**
     * Render fieldbox callback
     * Normaly you dont need to call this function manually.
     * @return string
     */
	public function render_fieldbox_callback() {
		if ( $this->fields && ! empty( $this->fields ) ) {
			global $post;

			$renderer = new \PhHelpers\View\Renderer();
			$html     = $renderer->render( 'ph-helpers/post-field-list.php', array(
				'fields' => $this->fields,
				'post'   => $post
			) );

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
     * Get the value of plural label
     *
     * @return string
     */
    public function getPlural()
    {
        return $this->plural;
    }

    /**
     * Set the value of plural label
     *
     * @param string plural
     *
     * @return self
     */
    public function setPlural($plural)
    {
        $this->plural = $plural;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
     * Get the value of description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * Add a field to the post-type
     * @param \PhHelpers\Field\AbstractField $field
     * @return self
     */
    public function addField(\PhHelpers\Field\AbstractField $field){
        $this->fields[] = $field;
    }

    /**
     * Get the value of is public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set the value of is public
     *
     * @param boolean public
     *
     * @return self
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get the value of has archive
     *
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set the value of has archive
     *
     * @param boolean archive
     *
     * @return self
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get the value of supports
     *
     * @return string[]
     */
    public function getSupports()
    {
        return $this->supports;
    }

    /**
     * Set the value of supports
     *
     * @param string[] supports
     *
     * @return self
     */
    public function setSupports($supports)
    {
        $this->supports = $supports;

        return $this;
    }

    /**
     * Remove a supports entry
     * @param string supports
     * @return self
     */
    public function removeSupports($supports){
        $index = array_search($supports, $this->supports);
        if($index !== FALSE){
            unset($this->supports[$index]);
        }
        return $this;
    }

    /**
     * Add a supports value
     * @param string $supports
     * @return self
     */
    public function addSupports($supports)
    {
        if(in_array($supports, $this->supports)){
            throw new \Exception("$supports is allready set for post type $this->label");
        }

        $this->supports[] = $supports;

        return $this;
    }

    /**
     * Get the value of rewrite
     *
     * @return array
     */
    public function getRewrite()
    {
        return $this->rewrite;
    }

    /**
     * Set the value of rewrite
     *
     * @param array rewrite
     *
     * @return self
     */
    public function setRewrite(array $rewrite)
    {
        $this->rewrite = $rewrite;

        return $this;
    }

    /**
     * Get the value of terms
     *
     * @return \PhHelpers\Term[]
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set the value of terms
     *
     * @param \PhHelpers\Term[] terms
     *
     * @return self
     */
    public function setTerms(\PhHelpers\Term $terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Add a term to the post-type
     * @param \PhHelpers\Term $term
     * @return self
     */
    public function addTerm(\PhHelpers\Term $term)
    {
        $this->terms[] = $term;

        return $this;
    }

    /**
     * Get is persisted
     * @return boolean
     */
    public function getPersisted(){
        return $this->persisted;
    }
}
