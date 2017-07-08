<?php
/**
 * Template Name: Blank
 */

//get_header();
wp_head();

if( have_posts() ){
	while( have_posts() ){
		the_post();
		// using the WordPress loop, we'll display the post content here
		// inorder for page builder to work, you need the page builder's shortcode
		// right into the textarea wherein you compose your blog
		?>
	<?php do_action( 'thrive_before_page_content' ); ?>

		<div class="row">
			<div id="blank-template-content" class="col-md-12">

				<div id="primary" class="content-area thrive-page-document">
					<main id="main" class="site-main" role="main">
						<?php the_content(); ?>
					</main>
				</div>

				<div id="dashboard-widgets"></div>

			</div>
		</div>

		<?php
	}
}

wp_footer();
//get_footer();
?>
