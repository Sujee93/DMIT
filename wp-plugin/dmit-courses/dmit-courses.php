<?php
/**
 * Plugin Name: DMIT Courses
 * Description: Registers the Course CPT, ACF field group, and shortcodes for DMIT Institute.
 *              Compatible with ACF Free (no repeater fields used).
 * Version: 1.2.0
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
	register_post_type( 'course', [
		'labels' => [
			'name'               => 'Courses',
			'singular_name'      => 'Course',
			'add_new_item'       => 'Add New Course',
			'edit_item'          => 'Edit Course',
			'view_item'          => 'View Course',
			'search_items'       => 'Search Courses',
			'not_found'          => 'No courses found',
			'not_found_in_trash' => 'No courses found in trash',
			'menu_name'          => 'Courses',
		],
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'rest_base'          => 'courses',
		'menu_icon'          => 'dashicons-welcome-learn-more',
		'menu_position'      => 5,
		'supports'           => [ 'title', 'thumbnail', 'excerpt', 'editor', 'revisions' ],
		'has_archive'        => true,
		'rewrite'            => [ 'slug' => 'courses', 'with_front' => false ],
		'capability_type'    => 'post',
	] );
}

// ---------------------------------------------------------------------------
// 2. ACF Field Group — ACF Free compatible (no repeater fields)
// ---------------------------------------------------------------------------

add_action( 'acf/init', 'dmit_register_course_fields' );

function dmit_register_course_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'    => 'group_dmit_course_65f1a2b3',
		'title'  => 'Course Details',
		'fields' => [

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
				'instructions' => 'Strike-through price. Leave blank if no discount.',
				'prepend'      => 'LKR',
			],
			[
				'key'         => 'field_dmit_course_badge',
				'label'       => 'Badge',
				'name'        => 'course_badge',
				'type'        => 'select',
				'allow_null'  => 1,
				'placeholder' => 'None',
				'choices'     => [
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
				'key'  => 'field_dmit_course_reviews',
				'label' => 'Review Count',
				'name'  => 'course_reviews',
				'type'  => 'number',
			],
			[
				'key'  => 'field_dmit_course_lessons',
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
			[
				'key'          => 'field_dmit_instructor_bio',
				'label'        => 'Instructor Bio',
				'name'         => 'instructor_bio',
				'type'         => 'textarea',
				'rows'         => 4,
				'instructions' => 'Short bio shown on the course page.',
			],

			// ── Tab: Course Content ──────────────────────────────────────────
			[ 'key' => 'field_dmit_tab_content', 'label' => 'Course Content', 'name' => '', 'type' => 'tab' ],

			[
				'key'          => 'field_dmit_course_learn_points',
				'label'        => 'What You Will Learn',
				'name'         => 'course_learn_points',
				'type'         => 'textarea',
				'rows'         => 8,
				'instructions' => 'One learning point per line. e.g.
SEO & keyword research strategies
Google & Meta Ads campaigns
Content marketing & copywriting',
			],
			[
				'key'          => 'field_dmit_course_includes',
				'label'        => 'Course Includes',
				'name'         => 'course_includes',
				'type'         => 'textarea',
				'rows'         => 6,
				'instructions' => 'One item per line. Start with an emoji. e.g.
🎥 15 Video Lessons
📅 Duration: 12 Weeks
♾️ Lifetime Access
🏆 Certificate of Completion',
			],
			[
				'key'          => 'field_dmit_course_curriculum',
				'label'        => 'Curriculum',
				'name'         => 'course_curriculum',
				'type'         => 'wysiwyg',
				'tabs'         => 'all',
				'toolbar'      => 'basic',
				'media_upload' => 0,
				'instructions' => 'Write each module as a heading (H3) and list the lessons underneath. The plugin renders this as an accordion on the frontend.',
			],

			// ── Tab: SEO & Meta ──────────────────────────────────────────────
			[ 'key' => 'field_dmit_tab_seo', 'label' => 'SEO & Meta', 'name' => '', 'type' => 'tab' ],

			[
				'key'          => 'field_dmit_course_meta_title',
				'label'        => 'Meta Title',
				'name'         => 'course_meta_title',
				'type'         => 'text',
				'instructions' => 'Leave blank to use post title. Max 60 chars.',
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
		'description'           => 'Course details for DMIT Institute — ACF Free compatible',
	] );
}

// ---------------------------------------------------------------------------
// 3. Helper: parse a textarea field into a trimmed array of non-empty lines
// ---------------------------------------------------------------------------

function dmit_parse_lines( $text ) {
	if ( ! $text ) {
		return [];
	}
	return array_values( array_filter( array_map( 'trim', explode( "\n", $text ) ) ) );
}

// ---------------------------------------------------------------------------
// 4. [dmit_courses] — course listing grid
//    Attrs: category, per_page, cols
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_courses', 'dmit_courses_shortcode' );

function dmit_courses_shortcode( $atts ) {
	$atts = shortcode_atts(
		[ 'category' => '', 'per_page' => 12, 'cols' => 2 ],
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
		$args['meta_query'] = [ [
			'key'     => 'course_category',
			'value'   => sanitize_text_field( $atts['category'] ),
			'compare' => '=',
		] ];
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p style="text-align:center;color:#6B7280;font-family:Outfit,sans-serif;padding:40px 0;">No courses found.</p>';
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
	.dmit-cc{background:#fff;border-radius:20px;overflow:hidden;border:1.5px solid #E5E7EB;transition:box-shadow .2s,transform .2s;}
	.dmit-cc:hover{box-shadow:0 12px 40px rgba(0,0,0,.1);transform:translateY(-4px);}
	.dmit-cc__thumb{position:relative;}
	.dmit-cc__thumb img{width:100%;height:200px;object-fit:cover;display:block;}
	.dmit-cc__badge{position:absolute;top:12px;left:12px;padding:4px 12px;border-radius:50px;font-size:11px;font-weight:700;}
	.dmit-cc__body{padding:22px;}
	.dmit-cc__cat{font-size:12px;font-weight:600;color:#2563EB;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;}
	.dmit-cc__title{font-size:17px;font-weight:700;color:#0F172A;margin:0 0 12px;line-height:1.3;}
	.dmit-cc__inst{display:flex;align-items:center;gap:8px;margin-bottom:12px;}
	.dmit-cc__inst img{width:28px;height:28px;border-radius:50%;object-fit:cover;}
	.dmit-cc__inst span{font-size:13px;color:#6B7280;}
	.dmit-cc__meta{display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:#9CA3AF;margin-bottom:16px;}
	.dmit-cc__footer{border-top:1px solid #F3F4F6;padding-top:16px;display:flex;align-items:center;justify-content:space-between;}
	.dmit-cc__price{font-size:18px;font-weight:800;color:#0F172A;}
	.dmit-cc__btn{background:#0F172A;color:#fff;font-size:13px;font-weight:600;padding:10px 20px;border-radius:50px;text-decoration:none;transition:background .2s;}
	.dmit-cc__btn:hover{background:#2563EB;color:#fff;}
	</style>
	<div class="dmit-courses-grid">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$pid       = get_the_ID();
		$price     = get_field( 'course_price', $pid );
		$badge     = get_field( 'course_badge', $pid );
		$cat       = get_field( 'course_category', $pid );
		$duration  = get_field( 'course_duration', $pid );
		$level     = get_field( 'course_level', $pid );
		$students  = get_field( 'course_students', $pid );
		$inst_name = get_field( 'instructor_name', $pid );
		$inst_photo = get_field( 'instructor_photo', $pid );
		$thumb     = get_the_post_thumbnail_url( $pid, 'medium_large' );

		$badge_style  = $badge_colors[ $badge ] ?? '';
		$price_fmt    = $price ? 'LKR ' . number_format( (float) $price ) : '';
		$students_fmt = $students ? number_format( (int) $students ) : '';
		?>
		<div class="dmit-cc">
			<div class="dmit-cc__thumb">
				<?php if ( $thumb ) : ?>
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
				<?php endif; ?>
				<?php if ( $badge && $badge_style ) : ?>
					<span class="dmit-cc__badge" style="<?php echo esc_attr( $badge_style ); ?>"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
			</div>
			<div class="dmit-cc__body">
				<?php if ( $cat ) : ?><div class="dmit-cc__cat"><?php echo esc_html( $cat ); ?></div><?php endif; ?>
				<h3 class="dmit-cc__title"><?php the_title(); ?></h3>
				<?php if ( $inst_name ) : ?>
					<div class="dmit-cc__inst">
						<?php if ( $inst_photo ) : ?>
							<img src="<?php echo esc_url( $inst_photo ); ?>" alt="<?php echo esc_attr( $inst_name ); ?>" />
						<?php endif; ?>
						<span><?php echo esc_html( $inst_name ); ?></span>
					</div>
				<?php endif; ?>
				<div class="dmit-cc__meta">
					<?php if ( $duration ) : ?><span>⏱ <?php echo esc_html( $duration ); ?></span><?php endif; ?>
					<?php if ( $level ) : ?><span>📌 <?php echo esc_html( $level ); ?></span><?php endif; ?>
					<?php if ( $students_fmt ) : ?><span>👥 <?php echo esc_html( $students_fmt ); ?></span><?php endif; ?>
				</div>
				<div class="dmit-cc__footer">
					<div class="dmit-cc__price"><?php echo esc_html( $price_fmt ); ?></div>
					<a href="<?php the_permalink(); ?>" class="dmit-cc__btn">Enroll →</a>
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
// 5. [dmit_course_meta_row] — hero stats bar
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_meta_row', 'dmit_course_meta_row_shortcode' );

function dmit_course_meta_row_shortcode() {
	$pid     = get_the_ID();
	$rating  = get_field( 'course_rating', $pid );
	$reviews = get_field( 'course_reviews', $pid );
	$stud    = get_field( 'course_students', $pid );
	$dur     = get_field( 'course_duration', $pid );
	$level   = get_field( 'course_level', $pid );

	$parts = [];
	if ( $rating ) {
		$rev     = $reviews ? ' (' . number_format( (int) $reviews ) . ' reviews)' : '';
		$parts[] = '<span style="color:#FCD34D;font-weight:700;">' . esc_html( $rating ) . ' ★</span><span style="color:rgba(255,255,255,0.7);font-size:13px;">' . esc_html( $rev ) . '</span>';
	}
	if ( $stud )  $parts[] = '<span>👥 ' . esc_html( number_format( (int) $stud ) ) . ' Students</span>';
	if ( $dur )   $parts[] = '<span>⏱ ' . esc_html( $dur ) . '</span>';
	if ( $level ) $parts[] = '<span>📌 ' . esc_html( $level ) . '</span>';

	if ( ! $parts ) return '';

	$items = implode( '', array_map(
		fn( $p ) => '<div style="display:flex;align-items:center;gap:6px;">' . $p . '</div>',
		$parts
	) );

	return '<div style="display:flex;flex-wrap:wrap;gap:20px;font-family:Outfit,sans-serif;font-size:14px;color:rgba(255,255,255,0.85);">' . $items . '</div>';
}

// ---------------------------------------------------------------------------
// 6. [dmit_course_instructor] — instructor card
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_instructor', 'dmit_course_instructor_shortcode' );

function dmit_course_instructor_shortcode() {
	$pid   = get_the_ID();
	$name  = get_field( 'instructor_name', $pid );
	$title = get_field( 'instructor_title', $pid );
	$photo = get_field( 'instructor_photo', $pid );
	$bio   = get_field( 'instructor_bio', $pid );

	if ( ! $name ) return '';

	ob_start(); ?>
	<div style="display:flex;gap:20px;align-items:flex-start;font-family:Outfit,sans-serif;flex-wrap:wrap;">
		<?php if ( $photo ) : ?>
			<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>"
				style="width:80px;height:80px;border-radius:50%;object-fit:cover;flex-shrink:0;" />
		<?php endif; ?>
		<div style="flex:1;min-width:200px;">
			<div style="font-size:18px;font-weight:700;color:#0F172A;margin-bottom:4px;"><?php echo esc_html( $name ); ?></div>
			<?php if ( $title ) : ?>
				<div style="font-size:14px;color:#6B7280;margin-bottom:10px;"><?php echo esc_html( $title ); ?></div>
			<?php endif; ?>
			<?php if ( $bio ) : ?>
				<div style="font-size:15px;color:#4B5563;line-height:1.6;"><?php echo esc_html( $bio ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// ---------------------------------------------------------------------------
// 7. [dmit_course_learn_points] — two-col checkmark grid from textarea
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_learn_points', 'dmit_course_learn_points_shortcode' );

function dmit_course_learn_points_shortcode() {
	$pid    = get_the_ID();
	$raw    = get_field( 'course_learn_points', $pid );
	$points = dmit_parse_lines( $raw );

	if ( ! $points ) return '';

	$items = '';
	foreach ( $points as $p ) {
		$items .= '<div style="display:flex;gap:10px;align-items:flex-start;">'
			. '<span style="color:#7C3AED;font-size:18px;flex-shrink:0;line-height:1.4;">&#10003;</span>'
			. '<span style="color:#374151;font-size:15px;line-height:1.4;">' . esc_html( $p ) . '</span>'
			. '</div>';
	}

	return '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-family:Outfit,sans-serif;'
		. '@media(max-width:600px){grid-template-columns:1fr;}">' . $items . '</div>';
}

// ---------------------------------------------------------------------------
// 8. [dmit_course_curriculum] — outputs the wysiwyg curriculum field
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_curriculum', 'dmit_course_curriculum_shortcode' );

function dmit_course_curriculum_shortcode() {
	$pid      = get_the_ID();
	$content  = get_field( 'course_curriculum', $pid );

	if ( ! $content ) return '';

	// Wrap in styled container; the user's H3/ul from wysiwyg get scoped styles.
	return '<div class="dmit-curriculum" style="font-family:Outfit,sans-serif;">'
		. '<style>'
		. '.dmit-curriculum h3{font-size:16px;font-weight:700;color:#0F172A;margin:0 0 8px;padding:16px 20px;'
		. 'background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:12px;cursor:default;}'
		. '.dmit-curriculum ul{list-style:none;margin:0 0 12px;padding:0 20px 12px;border:1.5px solid #E5E7EB;'
		. 'border-top:0;border-radius:0 0 12px 12px;display:flex;flex-direction:column;gap:8px;}'
		. '.dmit-curriculum li{display:flex;gap:10px;align-items:center;font-size:14px;color:#374151;padding-top:8px;}'
		. '.dmit-curriculum li::before{content:"▶";color:#2563EB;flex-shrink:0;}'
		. '</style>'
		. $content
		. '</div>';
}

// ---------------------------------------------------------------------------
// 9. [dmit_course_enroll_card] — sticky sidebar enroll card
// ---------------------------------------------------------------------------

add_shortcode( 'dmit_course_enroll_card', 'dmit_course_enroll_card_shortcode' );

function dmit_course_enroll_card_shortcode() {
	$pid      = get_the_ID();
	$price    = get_field( 'course_price', $pid );
	$orig     = get_field( 'course_original_price', $pid );
	$includes = get_field( 'course_includes', $pid );
	$thumb    = get_the_post_thumbnail_url( $pid, 'medium_large' );

	$price_fmt = $price ? 'LKR ' . number_format( (float) $price ) : '';
	$orig_fmt  = $orig  ? 'LKR ' . number_format( (float) $orig )  : '';
	$inc_lines = dmit_parse_lines( $includes );

	// Default includes if none set
	if ( ! $inc_lines ) {
		$inc_lines = [ '📅 Flexible Schedule', '🎥 Video Lessons', '📱 Mobile Friendly', '🏆 Certificate', '♾️ Lifetime Access' ];
	}

	ob_start(); ?>
	<div style="background:#fff;border-radius:20px;overflow:hidden;border:1.5px solid #E5E7EB;font-family:Outfit,sans-serif;position:sticky;top:100px;">
		<?php if ( $thumb ) : ?>
			<img src="<?php echo esc_url( $thumb ); ?>" alt="" style="width:100%;height:200px;object-fit:cover;display:block;" />
		<?php endif; ?>
		<div style="padding:24px;">
			<?php if ( $price_fmt ) : ?>
				<div style="font-size:32px;font-weight:800;color:#0F172A;margin-bottom:4px;"><?php echo esc_html( $price_fmt ); ?></div>
			<?php endif; ?>
			<?php if ( $orig_fmt ) : ?>
				<div style="font-size:13px;color:#9CA3AF;text-decoration:line-through;margin-bottom:16px;"><?php echo esc_html( $orig_fmt ); ?></div>
			<?php else : ?>
				<div style="margin-bottom:16px;"></div>
			<?php endif; ?>

			<a href="#enroll" style="display:block;text-align:center;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;font-size:16px;font-weight:700;padding:16px;border-radius:12px;text-decoration:none;margin-bottom:10px;">Enroll Now</a>
			<a href="/contact" style="display:block;text-align:center;border:1.5px solid #0F172A;color:#0F172A;font-size:15px;font-weight:600;padding:14px;border-radius:12px;text-decoration:none;margin-bottom:20px;">Request Free Demo</a>

			<div style="display:flex;flex-direction:column;gap:10px;border-top:1px solid #F3F4F6;padding-top:16px;">
				<?php foreach ( $inc_lines as $line ) :
					// Split on first space to separate emoji from text
					$parts = explode( ' ', $line, 2 );
					$icon  = count( $parts ) === 2 ? $parts[0] : '•';
					$text  = count( $parts ) === 2 ? $parts[1] : $line;
					?>
					<div style="display:flex;gap:10px;font-size:14px;color:#374151;">
						<span><?php echo esc_html( $icon ); ?></span>
						<span><?php echo esc_html( $text ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
