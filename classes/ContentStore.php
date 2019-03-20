<?php
namespace PhHelpers;

class ContentStore{

	private static $instance;

    /**
     * Array of post-types collected
     * @var array
     */
	protected $posts = [];

    /**
     * Array of terms collected
     * @var array
     */
    protected $terms = [];

	/** @return Plugin */
	public static function instance()
	{
		if (ContentStore::$instance === null) {
			ContentStore::$instance = new ContentStore();
		}
		return ContentStore::$instance;
	}

    /**
     * Add a post type to the map
     * @param \PhHelpers\Post post
     * @return self
     */
    public function addPost($post)
    {
        if(!$this->hasPost($post)){
            $this->posts[$post->getSlug()] = $post;
        }

        return $this;
    }

    /**
	 * Check if a post_type exists in the store
	 * @param \PhHelpers\Post $post
	 * @return boolean
	 */
	protected function hasPost($post)
    {
		if(\array_key_exists($post->getSlug(), $this->posts)){
			return true;
		}
		return false;
	}

    /**
     * Add a term to the map
     * @param \PhHelpers\Term term
     * @return self
     */
    public function addTerm($term)
    {
        if(!$this->hasTerm($term)){
            $this->terms[$term->getSlug()] = $term;
        }

        return $this;
    }

    /**
	 * Check if a term exists in the store
	 * @param \PhHelpers\Term $term
	 * @return boolean
	 */
	protected function hasTerm($term)
    {
		if(\array_key_exists($term->getSlug(), $this->terms)){
			return true;
		}
		return false;
	}

	/**
	 * Persist post types and terms collected
     * @return void
	 */
	public function persistAll(){

        // map to store association of terms with post types
        $map = [];

        // register all post types
        foreach($this->posts as $post){
            register_post_type($post->getSlug(), array(
    			'labels' => array(
    				'name' => $post->getPlural(),
    				'singular_name' => $post->getLabel()
    			),
    			'public' => $post->getPublic(),
    			'has_archive' => $post->getArchive(),
    			'supports' => $post->getSupports(),
    			'rewrite' => $post->getRewrite(),
    		));

            if(!empty($post->getTerms())){
                foreach($post->getTerms() as $term){
                    $slug = $term->getSlug();

                    if(!array_key_exists($slug, $map)){
                        $map[$slug] = [];
                    }

                    $map[$slug][] = $post->getSlug();
                }
            }
        }

        // register all terms and associate post types
        foreach($this->terms as $term){

            $objectTypes = null;
            if(array_key_exists($term->getSlug(), $map)){
                $objectTypes = $map[$term->getSlug()];
            }

            register_taxonomy($term->getSlug(), $objectTypes, [
                'label' => $term->getLabel(),
                'hierarchical' => $term->getHierarchical()
            ]);
        }
	}

    /**
     * get posts
     * @return array
     */
    public function getPosts(){
        return $this->posts;
    }

    /**
     * get terms
     * @return array
     */
    public function getTerms(){
        return $this->terms;
    }
}
