<?php

get_header();

//START banner container 
get_template_part('template-parts/home/home', 'banner');
// END banner container

// Therapies section
get_template_part('template-parts/home/section', 'services');
?>
<section class="page-content single-post-wrap single-post-gen-content" role="main">

    <?php
	if ( have_posts() ):
		while ( have_posts() ):
			the_post();
			$subtitle      = get_post_meta( $post->ID, 'therapy_subtitle', true );
			$slider_images = get_post_meta( $post->ID, 'slider_images', true );
			?>
    <article id="lesson-<?php $post->ID; ?>" <?php post_class( 'cpt-single' ); ?> role="article">
        <div class="container">
            <div class="row">
                <div class="<?php echo $main_class?>">
                    <div class="single-therapy-content">
                        <header class="cpt-title <?php echo $content_class?> mb-5">
                            <?php the_title( '<h2 class="textheading2 d-md-inline-block oversized">', '</h2>' ); ?>
                            <?php if ($layout === 'one-column-no-sidebar'):?>
                            <?php if ($book_link && $booking_title): ?>
                            <a class="d-none d-md-block button-alt text-white <?php echo $sidebar_button_class?>"
                                href="<?php echo $book_link; ?>"><?php echo $booking_title ?></a>
                            <?php endif; ?>
                            <?php endif;?>
                        </header>
                        <!-- CPT Text -->

                        <div class="post-content test" role="main">
                            <?php add_filter( 'the_content', 'wpautop' ) ?>
                            <div class="textnormal test <?php echo $content_class?>"><?php the_content(); ?></div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
        </div>
    </article>
    <?php
		endwhile;
	endif;
	get_template_part( 'template-parts/home/section', 'gallery' );
	?>
</section>

<?php

//Benefits section
get_template_part('template-parts/home/section', 'facilities');
get_template_part('template-parts/home/section', 'about-us');
get_template_part('template-parts/home/section', 'pricing_plans');
get_template_part('template-parts/home/section', 'gallery');


get_footer();
