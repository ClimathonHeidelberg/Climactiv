<?php get_header(); ?>
<!-- START Page Content -->
<section class="page-content search-results" role="main">
    <div class="container">
        <?php
        $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
        if (have_posts()):
            ?>
            <div class="therapy-listing">
                <?php
                while (have_posts()):
                    the_post();
                    ?>
                    <!-- START Single CPT -->
                    <article id="therapy-<?php the_ID(); ?>" <?php post_class('therapy-wrapper'); ?> style="background-image:url(<?php echo get_the_post_thumbnail_url($post) ?>)">

                        <div class="row">
                            <!-- CPT Content -->
                            <div class="therapy-content  col-md-7">
                                <div class="text-center therapy-content-inner">
                                    <div class="cpt-title">
                                        <?php the_title('<h2 class="textheading2 oversized">', '</h2>'); ?>
                                    </div>

                                    <div class="cpt-excerpt">                                     
                                        <h3 class="textheading3"><?php the_excerpt() ?></h3>
                                    </div>                                    
                                </div>
                                <div class="row cpt-buttons">
                                    <div class="cpt-button m-auto">
                                        <a class="button-prime" href="<?php echo get_permalink($post->ID); ?>" class="button border"><?php echo ot_get_option('blog_button_label'); ?></a>
                                    </div>                                        
                                </div>
                            </div>
                        </div>
                    </article>
                    <!-- END Single CPT -->
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <?php get_template_part('template-parts/content/content', 'none'); ?>
        <?php endif; ?>
        <?php
        /* Restore original Post Data */
        wp_reset_postdata();
        ?>
    </div>
</section>
<!-- END Page Content -->
<?php get_footer(); ?>