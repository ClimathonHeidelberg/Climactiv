<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header();
$layout = get_post_meta(get_the_ID(), 'single_post_page_layout', true);
if (!$layout) {
    $layout = ot_get_option('single_page_layout');
}
$main_class = 'col-md-7 col-lg-8';
$sidebar_class = 'col-md-5 col-lg-4';

if ($layout === 'one-column-left-sidebar') {
    $main_class = 'col-md-7 col-lg-8 order-last mr-0';
    $sidebar_class = 'col-md-5 col-lg-4 order-first ml-0';
} elseif ($layout === 'one-column-no-sidebar') {
    $main_class = 'col-12';
    $sidebar_class = 'd-none';
}
get_template_part( 'template-parts/internal', 'banner' );
?>
    <section class="page-content" role="main">
        <!-- START Single CPT -->
        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="container">
                <div class="row">
                    <!-- Content -->
                    <div class="<?php echo $main_class ?>">
                        <?php if (have_posts()): while (have_posts()): the_post(); ?>

                            <div class="single-therapy-content mt-0">
                                <div class="row">
                                    <!-- Featured Image -->
                                    <?php if (has_post_thumbnail() && ot_get_option('show_blog_thumb') === 'on') { ?>
                                        <div class="col-12 mb-5">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('slider_featured', array('class' => 'featured-image img-fluid')); ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                                <header class="cpt-title">
                                    <?php the_title('<h2 class="textheading2 oversized">', '</h2>'); ?>
                                    <?php get_template_part('template-parts/post', 'meta');?>
                                </header>
                                <!-- CPT Text -->
                                <div class="post-content mr-md-5" role="main">
                                    <?php add_filter('the_content', 'wpautop') ?>
                                    <div class="textnormal"><?php the_content(); ?></div>

                                </div>
                            </div>
                        <?php endwhile;endif; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- CPT Pagination -->
                                <?php
                                the_posts_pagination(array(
                                    'mid_size' => '5',
                                    'prev_text' => __('Previous', 'oct-physiotherapy'),
                                    'next_text' => __('Next', 'oct-physiotherapy'),
                                    //'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'twentyseventeen') . ' </span>',
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="gen-sidebar <?php echo $sidebar_class ?> sidebar primary">
                        <?php get_sidebar(); ?>
                    </div>

                </div>

            </div>
        </article>

        <!-- END Single CPT -->


    </section>


    <!-- END Page Content -->

<?php get_footer(); ?>