<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Elixar
 */
get_header(); ?>
<!-- Page Heading -->
<div class="e-breadcrumb-page-title">
  <div class="container">
	<div class="row">
	  <div class="col-sm-6">
		<?php /* translators: %s: Search Query */ ?>
		<h1 class="e-page-title text-center-xs"><?php printf( esc_html__( "Search Results For: %s", 'elixar' ), '<span>"' . esc_html( get_search_query() ) . '"</span>' ); ?></h1>
	  </div>
	  <div class="col-sm-6">
		<?php elixar_breadcrumbs(); ?>
	  </div>
	</div>
  </div>
</div>
<!-- Page Heading / End -->
<div class="elixar-blog">
	<div class="container">
		<div id="main-content" class="row">
			<?php $elixar_blog_layout = sanitize_text_field( get_theme_mod( 'elixar_blog_temp_layout', 'rightsidebar' ) );
			if( get_post_meta( get_the_ID(), 'elixar_blog_temp_layout', true ) !="" ) {
				$elixar_blog_layout = get_post_meta( $page_id, 'elixar_blog_temp_layout', true );
			} else if ( $elixar_blog_layout ) {
				$elixar_blog_layout = $elixar_blog_layout;
			}
			$elixar_imageSize = $elixar_blog_layout == "fullwidth" ? 'elixar_blog_full_thumb' : 'elixar_blog_sidebar_thumb';
			if ( $elixar_blog_layout == "leftsidebar" ) {
				get_sidebar();
				$elixar_float = 8;
			} elseif ( $elixar_blog_layout == "fullwidth" ) {
				$elixar_float = 12;
			} elseif ( $elixar_blog_layout == "rightsidebar" ) {
				$elixar_float = 8;
			} else {
				$elixar_blog_layout = "rightsidebar";
				$elixar_float = 8;
			} ?>
			<!-- GRID POSTS -->
			<div class="col-sm-<?php echo intval( $elixar_float ); ?> e-content-block">
				<!-- POST ITEM 1 -->
				<?php
				if ( have_posts() ) { 
				  while ( have_posts() ): the_post();
					get_template_part( 'template-parts/content', 'search' );
				  endwhile;
				 wp_link_pages();
				 the_posts_pagination( array(
					'prev_text'          => esc_html__( 'Prev', 'elixar' ),
					'next_text'          => esc_html__( 'Next', 'elixar' ),
					'screen_reader_text' => __( 'Post navigation', 'elixar' ),
				) );
				} else {
					get_template_part( 'template-parts/content', 'none' ); ?>
				<?php } ?>
			</div>
			<!-- GRID POSTS END -->
			<!-- GRID SIDEBAR -->
			<?php if ( $elixar_blog_layout == "rightsidebar" ) {
			get_sidebar(); } ?>
			<!-- GRID SIDEBAR END -->
		</div>
	</div>
</div>
<?php get_footer(); ?>