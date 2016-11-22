<?php

namespace Tribe\Libs\ACF;

class Group extends ACF_Configuration implements ACF_Aggregate {
	protected $key_prefix = 'group';

	/** @var Field[] */
	protected $fields = [ ];
	protected $post_types = [ ];
	protected $taxonomies = [ ];
	protected $users = false;

	/**
	 * Group constructor.
	 *
	 * @param string     $key
	 * @param array|bool $object_types
	 */
	public function __construct( $key, $object_types = false ) {
		parent::__construct( $key );

		if ( $object_types ) {

			if ( ! array_key_exists( 'post_types', $object_types ) ) {
				$this->set_post_types( $object_types );
				return;
			}

			$this->set_post_types( $object_types['post_types'] );
			$this->set_taxonomies( $object_types['taxonomies'] );
			if ( $object_types['users'] ) {
				$this->enable_users();
			}
		}
	}

	public function add_field( Field $field ) {
		$this->fields[] = $field;
	}

	/**
	 * Get the attributes for this group.
	 *
	 * @return array
	 */
	public function get_attributes() {
		$attributes = parent::get_attributes();
		$attributes[ 'fields' ] = [ ];
		foreach ( $this->fields as $f ) {
			$attributes[ 'fields' ][] = $f->get_attributes();
		}

		$this->set_location_restrictions( $attributes );

		return $attributes;
	}

	/**
	 * Assign the location restrictions for this group.
	 *
	 * @param $attributes
	 *
	 * @return mixed
	 */
	protected function set_location_restrictions( &$attributes ) {

		foreach ( $this->post_types as $post_type ) {
			$attributes['location'][] = [
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type,
				],
			];
		}

		foreach ( $this->taxonomies as $taxonomy ) {
			$attributes['location'][] = [
				[
					'param'    => 'taxonomy',
					'operator' => '==',
					'value'    => $taxonomy,
				],
			];
		}

		if ( $this->users ) {
			$attributes['location'][] = [
				[
					'param'    => 'user_form',
					'operator' => '==',
					'value'    => 'edit',
				],
			];
		}
	}

	/**
	 * Enable this group to show on the user Add/Edit forms.
	 */
	public function enable_users() {
		$this->users = true;
	}

	/**
	 * Set the taxonomies for this group.
	 *
	 * @param array $taxonomies
	 */
	public function set_taxonomies( array $taxonomies ) {
		$this->taxonomies = $taxonomies;
	}

	/**
	 * Set the post types in which this group will be available
	 *
	 * @param array $post_types
	 * @return void
	 */
	public function set_post_types( array $post_types ) {
		$this->post_types = $post_types;
	}
}