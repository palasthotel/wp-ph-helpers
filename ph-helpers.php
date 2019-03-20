<?php
/*
Plugin Name: PALASTHOTEL Helpers
Plugin URI: http://jameslow.com/2008/01/28/private-files/
Description: Helper classes for faster wordpress developement
Author: Palasthotel <rezeption@palasthotel.de>
Version: 1.0
Author URI: http://palasthotel.de
*/
namespace PhHelpers;

require_once __DIR__ . "/vendor/autoload.php";

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die();
}

class Plugin
{
	private static $instance;

	protected $postTypeStore;

	/** @return Plugin */
	public static function instance()
	{
		if (Plugin::$instance === null) {
			Plugin::$instance = new Plugin();
		}
		return Plugin::$instance;
	}

	const DOMAIN = "ph-helpers";

	/**
	 * Constants for templates in theme
	 */
	const THEME_FOLDER = "plugin-parts";

	/**
	 * List of all action hooks of this plugin
	 */
	const ACTION_NAME_OF_ACTION = "ph_helpers_register_posttype";

	/**
	 * Constructor of the Plugin Class
	 */
	public function __construct()
	{
		$this->dir = plugin_dir_path(__FILE__);
		$this->url = plugin_dir_url(__FILE__);
		$this->contentStore = \PhHelpers\ContentStore::instance();

		// add actions
		add_action('init', array($this, 'init'));

		add_action( 'admin_init', array($this, 'admin_init') );

		add_action('wp_ajax_ph_helpers_autocomplete', array(
			$this,
			'autocomplete_callback'
		));
		add_action('wp_ajax_ph_helpers_postlist', array(
			$this,
			'postlist_callback'
		));

        add_action('ph_helpers_register_content_persist', array($this, 'ph_helpers_register_content_persist'));

		add_filter('content_relations_add_meta_box', array($this, 'contentRelationsMetaBoxFilter'), 10, 3);
	}

    /**
     * Finally persist post types and
     */
    public function ph_helpers_register_content_persist(){
        $this->contentStore->persistAll();
    }

	/**
	 * Implements filter_content_relations_add_meta_box
	 */
	public function contentRelationsMetaBoxFilter($doIt, $post_type, $post){

		if($this->postSlugRegistered($post_type)){
			return false;
		}
		return true;
	}

    protected function postSlugRegistered($slug){

        $posts = $this->contentStore->getPosts();

        if(\array_key_exists($slug, $posts)){
			return true;
		}
		return false;
    }

	/**
	 * Implenentation of hook_admin_init
	 */
	public function admin_init(){
		if(is_plugin_active('content-relations/ph-content-relations.php')){
			$this->admin_init_add_content_relations_field();
		}
	}

	function admin_init_add_content_relations_field(){
		register_setting(
            'writing',             // Options group
            'ph-helpers-use-content-relations'
        );

		add_settings_section(
            'ph_helpers_settings',                   // Section ID
            'PALASTHOTEL Helpers',  // Section title
            null, // Section callback function
            'writing'                          // Settings page slug
        );

		add_settings_field(
            'ph-helpers-use-content-relations',       // Field ID
            'Use content-relations plugin for storing relations',       // Field title
            array($this, 'use_content_relations_field_callback'), // Field callback function
            'writing',                    // Settings page slug
            'ph_helpers_settings'               // Section ID
        );
	}

	/**
	 * Callback for the ph-helpers-use-content-relations in the settings section
	 */
	public function use_content_relations_field_callback(){
    ?>
    <label for="droid-identification">
        <input id="droid-identification" type="checkbox" value="1" name="ph-helpers-use-content-relations" <?php checked( get_option( 'ph-helpers-use-content-relations', false ) ); ?>> "Use content-relations plugin for storing relations"
    </label>
    <?php
	}

	/**
	 * Autocomplete action to query for post types and search for them
	 * @return void
	 */
	public function autocomplete_callback()
	{
		$search = $_GET['term'];

		$args = array(
			'posts_per_page' => 10,
			'no_found_rows' => true,
			's' => $search,
			'sentence' => true
		);

		$args = apply_filters('ph_helpers_autocomplete', $args);

		if (isset($_GET['post_type'])) {
			$args['post_type'] = $_GET['post_type'];
		} else {
			$args['post_type'] = 'post';
		}

		$query = new \WP_Query($args);

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$results[] = [
					'value' => get_the_id(),
					'label' => html_entity_decode(get_the_title())
				];
			}

			wp_reset_postdata();
		}

		echo json_encode($results);
		wp_die();
	}

	/**
	 * Ajax callback for the post list
	 * @return void
	 */
	public function postlist_callback()
	{
		$ids = explode(',', $_GET['ids']);
		$post_type = $_GET['post_type'];

		$args = array(
			'post__in' => $ids,
			'post_type' => $post_type,
			'posts_per_page' => 9999
		);

		$posts = get_posts($args);

		echo json_encode($posts);
		wp_die();
	}

	/**
	 * Init action callback which registers an action for other plugins
	 * to register there post-types
	 */
	public function init()
	{
		do_action('ph_helpers_register_content');
        do_action('ph_helpers_register_content_persist');
	}
}

Plugin::instance();
require_once __DIR__ . '/public-functions.php';
