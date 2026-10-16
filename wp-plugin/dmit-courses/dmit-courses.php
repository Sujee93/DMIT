<?php
/**
 * Plugin Name: DMIT Courses
 * Description: Registers the Course custom post type and ACF field group for DMIT Institute.
 * Version: 1.0.0
 * Author: DMIT Institute
 * Text Domain: dmit-courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// 1. Register Custom Post Type: course
// ---------------------------------------------------------------------------

add_action( 'init', 'dmit_register_course_cpt' );

function dmit_register_course_cpt() {
	register_post_type(
		'course',
		[
			'label'               => 'Courses',
			'labels'              => [
				'name'               => 'Courses',
				'singular_name'      => 'Course',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Course',
				'edit_item'          => 'Edit Course',
				'new_item'           => 'New Course',
				'view_item'          => 'View Course',
				'search_items'       => 'Search Courses',
				'not_found'          => 'No courses found',
				'not_found_in_trash' => 'No courses found in trash',
				'menu_name'          => 'Courses',
			],
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'rest_base'           => 'courses',
			'menu_icon'           => 'dashicons-welcome-learn-more',
			'menu_position'       => 5,
			'hierarchical'        => false,
			'supports'            => [ 'title', 'thumbnail', 'excerpt', 'editor', 'revisions' ],
			'has_archive'         => true,
			'rewrite'             => [ 'slug' => 'courses', 'with_front' => false ],
			'query_var'           => true,
			'capability_type'     => 'post',
		]
	);
}

// ---------------------------------------------------------------------------
// 2. Register ACF Field Group (requires ACF Pro 5.0+)
// ---------------------------------------------------------------------------

add_action( 'acf/init', 'dmit_register_course_fields' );

function dmit_register_course_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'      => 'group_dmit_course_65f1a2b3',
		'title'    => 'Course Details',
		'fields'   => [

			// ── Tab: Basic Info ──────────────────────────────────────────────
			[
				'key'   => 'field_dmit_tab_basic',
				'label' => 'Basic Info',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'               => 'field_dmit_course_price',
				'label'             => 'Price (LKR)',
				'name'              => 'course_price',
				'type'              => 'number',
				'required'          => 0,
				'instructions'      => 'e.g. 14990',
				'min'               => '',
				'max'               => '',
				'step'              => '',
				'prepend'           => 'LKR',
				'append'            => '',
			],
			[
				'key'          => 'field_dmit_course_original_price',
				'label'        => 'Original Price (LKR)',
				'name'         => 'course_original_price',
				'type'         => 'number',
				'instructions' => 'Strike-through price shown on enroll card',
				'prepend'      => 'LKR',
			],
			[
				'key'          => 'field_dmit_course_badge',
				'label'        => 'Badge',
				'name'         => 'course_badge',
				'type'         => 'select',
				'allow_null'   => 1,
				'choices'      => [
					'Bestseller' => 'Bestseller',
					'Top Rated'  => 'Top Rated',
					'New'        => 'New',
					'Sale'       => 'Sale',
				],
				'default_value' => '',
				'placeholder'   => 'None',
			],
			[
				'key'     => 'field_dmit_course_category',
				'label'   => 'Category',
				'name'    => 'course_category',
				'type'    => 'select',
				'choices' => [
					'Digital Marketing' => 'Digital Marketing',
					'SEO'               => 'SEO',
					'GEO'               => 'GEO',
					'Web Design'        => 'Web Design',
					'Google Ads'        => 'Google Ads',
					'Social Media'      => 'Social Media',
					'Analytics'         => 'Analytics',
					'Email Marketing'   => 'Email Marketing',
				],
				'allow_null' => 1,
			],
			[
				'key'     => 'field_dmit_course_duration',
				'label'   => 'Duration',
				'name'    => 'course_duration',
				'type'    => 'text',
				'instructions' => 'e.g. 12 Weeks',
			],
			[
				'key'     => 'field_dmit_course_level',
				'label'   => 'Level',
				'name'    => 'course_level',
				'type'    => 'select',
				'choices' => [
					'Beginner'             => 'Beginner',
					'Intermediate'         => 'Intermediate',
					'Advanced'             => 'Advanced',
					'Beginner – Advanced'  => 'Beginner – Advanced',
				],
				'allow_null' => 1,
			],
			[
				'key'  => 'field_dmit_course_students',
				'label' => 'Student Count',
				'name'  => 'course_students',
				'type'  => 'number',
				'instructions' => 'Displayed as enrolled students',
			],
			[
				'key'   => 'field_dmit_course_rating',
				'label' => 'Rating',
				'name'  => 'course_rating',
				'type'  => 'number',
				'min'   => 0,
				'max'   => 5,
				'step'  => 0.1,
			],
			[
				'key'   => 'field_dmit_course_reviews',
				'label' => 'Review Count',
				'name'  => 'course_reviews',
				'type'  => 'number',
			],
			[
				'key'   => 'field_dmit_course_lessons',
				'label' => 'Number of Lessons',
				'name'  => 'course_lessons',
				'type'  => 'number',
			],

			// ── Tab: Instructor ──────────────────────────────────────────────
			[
				'key'   => 'field_dmit_tab_instructor',
				'label' => 'Instructor',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'   => 'field_dmit_instructor_name',
				'label' => 'Instructor Name',
				'name'  => 'instructor_name',
				'type'  => 'text',
			],
			[
				'key'   => 'field_dmit_instructor_title',
				'label' => 'Instructor Title',
				'name'  => 'instructor_title',
				'type'  => 'text',
				'instructions' => 'e.g. Founder & CEO, DMIT',
			],
			[
				'key'   => 'field_dmit_instructor_photo',
				'label' => 'Instructor Photo',
				'name'  => 'instructor_photo',
				'type'  => 'image',
				'return_format' => 'url',
				'preview_size'  => 'thumbnail',
			],

			// ── Tab: Course Content ──────────────────────────────────────────
			[
				'key'   => 'field_dmit_tab_content',
				'label' => 'Course Content',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'        => 'field_dmit_course_learn_points',
				'label'      => 'What You Will Learn',
				'name'       => 'course_learn_points',
				'type'       => 'repeater',
				'layout'     => 'table',
				'button_label' => 'Add Point',
				'sub_fields' => [
					[
						'key'             => 'field_dmit_learn_point_text',
						'label'           => 'Point',
						'name'            => 'point',
						'type'            => 'text',
						'parent_repeater' => 'field_dmit_course_learn_points',
					],
				],
			],
			[
				'key'        => 'field_dmit_course_includes',
				'label'      => 'Course Includes',
				'name'       => 'course_includes',
				'type'       => 'repeater',
				'layout'     => 'table',
				'button_label' => 'Add Item',
				'sub_fields' => [
					[
						'key'             => 'field_dmit_include_icon',
						'label'           => 'Icon (emoji)',
						'name'            => 'icon',
						'type'            => 'text',
						'parent_repeater' => 'field_dmit_course_includes',
					],
					[
						'key'             => 'field_dmit_include_text',
						'label'           => 'Text',
						'name'            => 'text',
						'type'            => 'text',
						'parent_repeater' => 'field_dmit_course_includes',
					],
				],
			],
			[
				'key'        => 'field_dmit_course_curriculum',
				'label'      => 'Curriculum Modules',
				'name'       => 'course_curriculum',
				'type'       => 'repeater',
				'layout'     => 'block',
				'button_label' => 'Add Module',
				'sub_fields' => [
					[
						'key'             => 'field_dmit_module_title',
						'label'           => 'Module Title',
						'name'            => 'module_title',
						'type'            => 'text',
						'parent_repeater' => 'field_dmit_course_curriculum',
					],
					[
						'key'             => 'field_dmit_module_duration',
						'label'           => 'Module Duration',
						'name'            => 'module_duration',
						'type'            => 'text',
						'instructions'    => 'e.g. 4 Lessons · 3h 20m',
						'parent_repeater' => 'field_dmit_course_curriculum',
					],
					[
						'key'             => 'field_dmit_module_lessons',
						'label'           => 'Lessons (one per line)',
						'name'            => 'module_lessons',
						'type'            => 'textarea',
						'rows'            => 4,
						'parent_repeater' => 'field_dmit_course_curriculum',
					],
				],
			],

			// ── Tab: SEO & Meta ──────────────────────────────────────────────
			[
				'key'   => 'field_dmit_tab_seo',
				'label' => 'SEO & Meta',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_dmit_course_meta_title',
				'label'        => 'Meta Title',
				'name'         => 'course_meta_title',
				'type'         => 'text',
				'instructions' => 'Leave blank to use post title',
				'maxlength'    => 60,
			],
			[
				'key'          => 'field_dmit_course_meta_description',
				'label'        => 'Meta Description',
				'name'         => 'course_meta_description',
				'type'         => 'textarea',
				'rows'         => 3,
				'maxlength'    => 160,
			],
		],

		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'course',
				],
			],
		],

		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => 'Course details for DMIT Institute',
	] );
}
