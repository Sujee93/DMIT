<?php
/**
 * Plugin Name: DMIT Courses
 * Description: Registers the Course CPT, ACF field group, and shortcodes for DMIT Institute.
 * Version: 1.1.0
 * Author: DMIT Institute
 * Text Domain: dmit-courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// 1. Custom Post Type: course
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
// 2. ACF Field Group
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
			[ 'key' => 'field_dmit_tab_basic', 'label' => 'Basic Info', 'name' => '', 'type' => 'tab' ],
			[
				'key'          => 'field_dmit_course_price',
				'label'        => 'Price (LKR)',
				'name'         => 'course_price',
				'type'         => 'number',
				'instructions' => 'e.g. 14990',
				'prepend'      => 'LKR',
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
				'key'           => 'field_dmit_course_badge',
				'label'         => 'Badge',
				'name'          => 'course_badge',
				'type'          => 'select',
				'allow_null'    => 1,
				'placeholder'   => 'None',
				'choices'       => [
					'Bestseller' => 'Bestseller',
					'Top Rated'  => 'Top Rated',
					'New'        => 'New',
					'Sale'       => 'Sale',
				],
			],
			[
				'key'        => 'field_dmit_course_category',
				'label'      => 'Category',
				'name'       => 'course_category',
				'type'       => 'select',
				'allow_null' => 1,
				'choices'    => [
					'Digital Marketing' => 'Digital Marketing',
					'SEO'               => 'SEO',
					'GEO'               => 'GEO',
					'Web Design'        => 'Web Design',
					'Google Ads'        => 'Google Ads',
					'Social Media'      => 'Social Media',
					'Analytics'         => 'Analytics',
					'Email Marketing'   => 'Email Marketing',
				],
			],
			[
				'key'          => 'field_dmit_course_duration',
				'label'        => 'Duration',
				'name'         => 'course_duration',
				'type'         => 'text',
				'instructions' => 'e.g. 12 Weeks',
			],
			[
				'key'        => 'field_dmit_course_level',
				'label'      => 'Level',
				'name'       => 'course_level',
				'type'       => 'select',
				'allow_null' => 1,
				'choices'    => [
					'Beginner'            => 'Beginner',
					'Intermediate'        => 'Intermediate',
					'Advanced'            => 'Advanced',
					'Beginner – Advanced' => 'Beginner – Advanced',
				],
			],
			[
				'key'          => 'field_dmit_course_students',
				'label'        => 'Student Count',
				'name'         => 'course_students',
				'type'         => 'number',
				'instructions' => 'Displayed as enrolled students',
			],
			[
				'key'  => 'field_dmit_course_rating',
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
			[ 'key' => 'field_dmit_tab_instructor', 'label' => 'Instructor', 'name' => '', 'type' => 'tab' ],
			[
				'key'   => 'field_dmit_instructor_name',
				'label' => 'Instructor Name',
				'name'  => 'instructor_name',
				'type'  => 'text',
			],
			[
				'key'          => 'field_dmit_instructor_title',
				'label'        => 'Instructor Title',
				'name'         => 'instructor_title',
				'type'         => 'text',
				'instructions' => 'e.g. Founder & CEO, DMIT',
			],
			[
				'key'           => 'field_dmit_instructor_photo',
				'label'         => 'Instructor Photo',
				'name'          => 'instructor_photo',
				'type'          => 'image',
				'return_format' => 'url',
				'preview_size'  => 'thumbnail',
			],

			// ── Tab: Course Content ──────────────────────────────────────────
			[ 'key' => 'field_dmit_tab_content', 'label' => 'Course Content', 'name' => '', 'type' => 'tab' ],
			[
				'key'          => 'field_dmit_course_learn_points',
				'label'        => 'What You Will Learn',
				'name'         => 'course_learn_points',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add Point',
				'sub_fields'   => [
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
				'key'          => 'field_dmit_course_includes',
				'label'        => 'Course Includes',
				'name'         => 'course_includes',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add Item',
				'sub_fields'   => [
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
				'key'          => 'field_dmit_course_curriculum',
				'label'        => 'Curriculum Modules',
				'name'         => 'course_curriculum',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add Module',
				'sub_fields'   => [
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
			[ 'key' => 'field_dmit_tab_seo', 'label' => 'SEO & Meta', 'name' => '', 'type' => 'tab' ],
			[
				'key'          => 'field_dmit_course_meta_title',
				'label'        => 'Meta Title',
				'name'         => 'course_meta_title',
				'type'         => 'text',
				'instructions' => 'Leave blank to use post title',
				'maxlength'    => 60,
			],
			[
				'key'       => 'field_dmit_course_meta_description',
				'label'     => 'Meta Description',
				'name'      => 'course_meta_description',
				'type'      => 'textarea',
				'rows'      => 3,
				'maxlength' => 160,
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
		'active'                => true,
		'description'           => 'Course details for DMIT Institute',
	] );
}

// ---------------------------------------------------------------------------
// 3. Shortcodes
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_courses', 'dmit_courses_shortcode' );

/**
 * [dmit_courses category="" per_page="12" cols="2"]
 * Outputs a responsive card grid of all Course CPT posts.
 */
function dmit_courses_shortcode( $atts ) {
	$atts = shortcode_atts(
		[
			'category' => '',
			'per_page' => 12,
			'cols'     => 2,
		],
		$atts,
		'dmit_courses'
	);

	$args = [
		'post_type'      => 'course',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $atts['per_page'],
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	if ( $atts['category'] ) {
		$args['meta_query'] = [
			[
				'key'     => 'course_category',
				'value'   => sanitize_text_field( $atts['category'] ),
				'compare' => '=',
			],
		];
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p style="text-align:center;color:#6B7280;font-family:Outfit,sans-serif;">No courses found.</p>';
	}

	$badge_colors = [
		'Bestseller' => 'background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;',
		'Top Rated'  => 'background:linear-gradient(135deg,#059669,#10B981);color:#fff;',
		'New'        => 'background:linear-gradient(135deg,#F59E0B,#EF4444);color:#fff;',
		'Sale'       => 'background:linear-gradient(135deg,#7C3AED,#EC4899);color:#fff;',
	];

	$cols        = max( 1, min( 4, (int) $atts['cols'] ) );
	$cols_tablet = $cols > 1 ? 2 : 1;

	ob_start();
	?>
	<style>
	.dmit-courses-grid{display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:28px;font-family:Outfit,sans-serif;}
	@media(max-width:1024px){.dmit-courses-grid{grid-template-columns:repeat(<?php echo $cols_tablet; ?>,1fr);gap:20px;}}
	@media(max-width:600px){.dmit-courses-grid{grid-template-columns:1fr;gap:16px;}}
	.dmit-course-card{background:#fff;border-radius:20px;overflow:hidden;border:1.5px solid #E5E7EB;transition:box-shadow .2s,transform .2s;}
	.dmit-course-card:hover{box-shadow:0 12px 40px rgba(0,0,0,.1);transform:translateY(-4px);}
	.dmit-course-card__thumb{position:relative;overflow:hidden;}
	.dmit-course-card__thumb img{width:100%;height:200px;object-fit:cover;display:block;}
	.dmit-course-card__badge{position:absolute;top:12px;left:12px;padding:4px 12px;border-radius:50px;font-size:11px;font-weight:700;font-family:Outfit,sans-serif;}
	.dmit-course-card__body{padding:22px;}
	.dmit-course-card__cat{font-size:12px;font-weight:600;color:#2563EB;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;}
	.dmit-course-card__title{font-size:17px;font-weight:700;color:#0F172A;margin:0 0 12px;line-height:1.3;}
	.dmit-course-card__instructor{display:flex;align-items:center;gap:8px;margin-bottom:12px;}
	.dmit-course-card__instructor img{width:28px;height:28px;border-radius:50%;object-fit:cover;}
	.dmit-course-card__instructor span{font-size:13px;color:#6B7280;}
	.dmit-course-card__meta{display:flex;gap:14px;font-size:12px;color:#9CA3AF;margin-bottom:16px;}
	.dmit-course-card__footer{border-top:1px solid #F3F4F6;padding-top:16px;display:flex;align-items:center;justify-content:space-between;}
	.dmit-course-card__price{font-size:18px;font-weight:800;color:#0F172A;}
	.dmit-course-card__btn{background:#0F172A;color:#fff;font-size:13px;font-weight:600;padding:10px 20px;border-radius:50px;text-decoration:none;font-family:Outfit,sans-serif;transition:background .2s;}
	.dmit-course-card__btn:hover{background:#2563EB;color:#fff;}
	</style>
	<div class="dmit-courses-grid">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$post_id   = get_the_ID();
		$price     = get_field( 'course_price', $post_id );
		$badge     = get_field( 'course_badge', $post_id );
		$category  = get_field( 'course_category', $post_id );
		$duration  = get_field( 'course_duration', $post_id );
		$level     = get_field( 'course_level', $post_id );
		$students  = get_field( 'course_students', $post_id );
		$inst_name = get_field( 'instructor_name', $post_id );
		$inst_photo = get_field( 'instructor_photo', $post_id );
		$thumb     = get_the_post_thumbnail_url( $post_id, 'medium_large' );

		$badge_style = isset( $badge_colors[ $badge ] ) ? $badge_colors[ $badge ] : '';
		$price_fmt   = $price ? 'LKR ' . number_format( (float) $price ) : '';
		$students_fmt = $students ? number_format( (int) $students ) : '';
		?>
		<div class="dmit-course-card">
			<div class="dmit-course-card__thumb">
				<?php if ( $thumb ) : ?>
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" />
				<?php endif; ?>
				<?php if ( $badge && $badge_style ) : ?>
					<span class="dmit-course-card__badge" style="<?php echo esc_attr( $badge_style ); ?>"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
			</div>
			<div class="dmit-course-card__body">
				<?php if ( $category ) : ?>
					<div class="dmit-course-card__cat"><?php echo esc_html( $category ); ?></div>
				<?php endif; ?>
				<h3 class="dmit-course-card__title"><?php the_title(); ?></h3>
				<?php if ( $inst_name ) : ?>
					<div class="dmit-course-card__instructor">
						<?php if ( $inst_photo ) : ?>
							<img src="<?php echo esc_url( $inst_photo ); ?>" alt="<?php echo esc_attr( $inst_name ); ?>" />
						<?php endif; ?>
						<span><?php echo esc_html( $inst_name ); ?></span>
					</div>
				<?php endif; ?>
				<div class="dmit-course-card__meta">
					<?php if ( $duration ) : ?><span>⏱ <?php echo esc_html( $duration ); ?></span><?php endif; ?>
					<?php if ( $level ) : ?><span>📌 <?php echo esc_html( $level ); ?></span><?php endif; ?>
					<?php if ( $students_fmt ) : ?><span>👥 <?php echo esc_html( $students_fmt ); ?></span><?php endif; ?>
				</div>
				<div class="dmit-course-card__footer">
					<div class="dmit-course-card__price"><?php echo esc_html( $price_fmt ); ?></div>
					<a href="<?php the_permalink(); ?>" class="dmit-course-card__btn">Enroll →</a>
				</div>
			</div>
		</div>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
	</div>
	<?php
	return ob_get_clean();
}

// ---------------------------------------------------------------------------
// 4. [dmit_course_enroll_card] — sticky sidebar card on single course
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_enroll_card', 'dmit_course_enroll_card_shortcode' );

function dmit_course_enroll_card_shortcode() {
	if ( ! is_singular( 'course' ) ) {
		return '';
	}

	$post_id       = get_the_ID();
	$price         = get_field( 'course_price', $post_id );
	$orig_price    = get_field( 'course_original_price', $post_id );
	$duration      = get_field( 'course_duration', $post_id );
	$lessons       = get_field( 'course_lessons', $post_id );
	$level         = get_field( 'course_level', $post_id );
	$includes      = get_field( 'course_includes', $post_id );
	$thumb         = get_the_post_thumbnail_url( $post_id, 'medium_large' );

	$price_fmt     = $price ? 'LKR ' . number_format( (float) $price ) : '';
	$orig_fmt      = $orig_price ? 'LKR ' . number_format( (float) $orig_price ) : '';

	ob_start(); ?>
	<div style="background:#fff;border-radius:20px;overflow:hidden;border:1.5px solid #E5E7EB;font-family:Outfit,sans-serif;">
		<?php if ( $thumb ) : ?>
			<img src="<?php echo esc_url( $thumb ); ?>" alt="" style="width:100%;height:200px;object-fit:cover;display:block;" />
		<?php endif; ?>
		<div style="padding:24px;">
			<?php if ( $price_fmt ) : ?>
				<div style="font-size:32px;font-weight:800;color:#0F172A;margin-bottom:4px;"><?php echo esc_html( $price_fmt ); ?></div>
			<?php endif; ?>
			<?php if ( $orig_fmt ) : ?>
				<div style="font-size:13px;color:#9CA3AF;text-decoration:line-through;margin-bottom:20px;"><?php echo esc_html( $orig_fmt ); ?></div>
			<?php endif; ?>
			<a href="#enroll" style="display:block;text-align:center;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;font-size:16px;font-weight:700;padding:16px;border-radius:12px;text-decoration:none;margin-bottom:12px;">Enroll Now</a>
			<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" style="display:block;text-align:center;border:1.5px solid #0F172A;color:#0F172A;font-size:15px;font-weight:600;padding:14px;border-radius:12px;text-decoration:none;margin-bottom:20px;">Request Free Demo</a>
			<div style="display:flex;flex-direction:column;gap:10px;border-top:1px solid #F3F4F6;padding-top:16px;">
				<?php if ( $duration ) : ?>
					<div style="display:flex;gap:10px;font-size:14px;color:#374151;"><span>📅</span><span>Duration: <?php echo esc_html( $duration ); ?></span></div>
				<?php endif; ?>
				<?php if ( $lessons ) : ?>
					<div style="display:flex;gap:10px;font-size:14px;color:#374151;"><span>🎥</span><span><?php echo esc_html( $lessons ); ?> Video Lessons</span></div>
				<?php endif; ?>
				<?php if ( $level ) : ?>
					<div style="display:flex;gap:10px;font-size:14px;color:#374151;"><span>📌</span><span>Level: <?php echo esc_html( $level ); ?></span></div>
				<?php endif; ?>
				<div style="display:flex;gap:10px;font-size:14px;color:#374151;"><span>📱</span><span>Mobile Friendly</span></div>
				<div style="display:flex;gap:10px;font-size:14px;color:#374151;"><span>🏆</span><span>Certificate of Completion</span></div>
				<div style="display:flex;gap:10px;font-size:14px;color:#374151;"><span>♾️</span><span>Lifetime Access</span></div>
				<?php if ( $includes && is_array( $includes ) ) :
					foreach ( $includes as $item ) : ?>
						<div style="display:flex;gap:10px;font-size:14px;color:#374151;">
							<span><?php echo esc_html( $item['icon'] ); ?></span>
							<span><?php echo esc_html( $item['text'] ); ?></span>
						</div>
					<?php endforeach;
				endif; ?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// ---------------------------------------------------------------------------
// 5. [dmit_course_curriculum] — accordion for curriculum modules
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_curriculum', 'dmit_course_curriculum_shortcode' );

function dmit_course_curriculum_shortcode() {
	if ( ! is_singular( 'course' ) ) {
		return '';
	}

	$post_id   = get_the_ID();
	$modules   = get_field( 'course_curriculum', $post_id );

	if ( ! $modules || ! is_array( $modules ) ) {
		return '';
	}

	ob_start(); ?>
	<div style="display:flex;flex-direction:column;gap:12px;font-family:Outfit,sans-serif;">
	<?php foreach ( $modules as $i => $mod ) :
		$lessons_raw = trim( $mod['module_lessons'] ?? '' );
		$lessons_arr = $lessons_raw ? array_filter( array_map( 'trim', explode( "\n", $lessons_raw ) ) ) : [];
		?>
		<details style="border:1.5px solid #E5E7EB;border-radius:12px;overflow:hidden;" <?php echo $i === 0 ? 'open' : ''; ?>>
			<summary style="padding:16px 20px;font-size:16px;font-weight:600;color:#0F172A;cursor:pointer;display:flex;justify-content:space-between;align-items:center;list-style:none;">
				<?php echo esc_html( $mod['module_title'] ); ?>
				<?php if ( $mod['module_duration'] ) : ?>
					<span style="font-size:12px;color:#6B7280;font-weight:400;"><?php echo esc_html( $mod['module_duration'] ); ?></span>
				<?php endif; ?>
			</summary>
			<?php if ( $lessons_arr ) : ?>
				<div style="padding:0 20px 16px;border-top:1px solid #F3F4F6;">
					<ul style="list-style:none;margin:12px 0 0;padding:0;display:flex;flex-direction:column;gap:8px;">
						<?php foreach ( $lessons_arr as $lesson ) : ?>
							<li style="display:flex;gap:10px;align-items:center;font-size:14px;color:#374151;">
								<span style="color:#2563EB;">▶</span>
								<?php echo esc_html( $lesson ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</details>
	<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

// ---------------------------------------------------------------------------
// 6. [dmit_course_learn_points] — what-you'll-learn two-column grid
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_learn_points', 'dmit_course_learn_points_shortcode' );

// ---------------------------------------------------------------------------
// 7. [dmit_course_meta_row] — hero stats bar (rating, students, duration)
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_meta_row', 'dmit_course_meta_row_shortcode' );

function dmit_course_meta_row_shortcode() {
	if ( ! is_singular( 'course' ) ) {
		return '';
	}

	$post_id  = get_the_ID();
	$rating   = get_field( 'course_rating', $post_id );
	$reviews  = get_field( 'course_reviews', $post_id );
	$students = get_field( 'course_students', $post_id );
	$duration = get_field( 'course_duration', $post_id );
	$level    = get_field( 'course_level', $post_id );

	$parts = [];
	if ( $rating ) {
		$stars = str_repeat( '★', (int) $rating ) . str_repeat( '☆', 5 - (int) $rating );
		$rev   = $reviews ? ' (' . number_format( (int) $reviews ) . ' reviews)' : '';
		$parts[] = '<span style="color:#FCD34D;">' . esc_html( $rating ) . ' ' . $stars . '</span><span style="color:rgba(255,255,255,0.7);font-size:13px;">' . esc_html( $rev ) . '</span>';
	}
	if ( $students ) {
		$parts[] = '<span>👥 ' . esc_html( number_format( (int) $students ) ) . ' Students</span>';
	}
	if ( $duration ) {
		$parts[] = '<span>⏱ ' . esc_html( $duration ) . '</span>';
	}
	if ( $level ) {
		$parts[] = '<span>📌 ' . esc_html( $level ) . '</span>';
	}

	if ( ! $parts ) {
		return '';
	}

	$items = implode( '', array_map( fn( $p ) => '<div style="display:flex;align-items:center;gap:6px;">' . $p . '</div>', $parts ) );

	return '<div style="display:flex;flex-wrap:wrap;gap:20px;font-family:Outfit,sans-serif;font-size:14px;color:rgba(255,255,255,0.85);">' . $items . '</div>';
}

// ---------------------------------------------------------------------------
// 8. [dmit_course_learn_points]
// ---------------------------------------------------------------------------

function dmit_course_learn_points_shortcode() {
	if ( ! is_singular( 'course' ) ) {
		return '';
	}

	$post_id = get_the_ID();
	$points  = get_field( 'course_learn_points', $post_id );

	if ( ! $points || ! is_array( $points ) ) {
		return '';
	}

	ob_start(); ?>
	<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-family:Outfit,sans-serif;">
	<?php foreach ( $points as $p ) : ?>
		<div style="display:flex;gap:10px;align-items:flex-start;">
			<span style="color:#7C3AED;font-size:18px;flex-shrink:0;">&#10003;</span>
			<span style="color:#374151;font-size:15px;line-height:1.4;"><?php echo esc_html( $p['point'] ); ?></span>
		</div>
	<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
