<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header();
$layout = get_post_meta(get_the_ID(), 'single_post_page_layout', true);
if (!$layout) {
    $layout = ot_get_option('single_post_page_layout');
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
remove_filter('the_content', 'add_br', 11);
?>

<?php if (have_posts()) the_post(); ?>

    <section class="page-content single-post-wrap single-post-gen-content" role="main">

        <!-- START Single CPT -->
        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="container">
                <div class="row">

                    <!-- Content -->
                    <div class="<?php echo $main_class?>">
                        <?php if (has_post_thumbnail() && ot_get_option('show_blog_thumb') === 'on') { ?>
                            <div class="mb-5 mr-md-5">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('slider_featured', array('class' => 'featured-image img-fluid')); ?>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="single-therapy-content">
                            <header class="cpt-title">
                                <?php the_title('<h2 class="textheading2 oversized">', '</h2>'); ?>
                                <?php get_template_part('template-parts/post', 'meta');?>
                            </header>
                            <!-- CPT Text -->
                            <div class="post-content mr-md-5" role="main">
                                <?php add_filter('the_content', 'wpautop') ?>
                                <div class="textnormal"><?php the_content(); ?></div>
                            </div>
                            <div class="comment-wrap mr-md-5">
                                <?php
                                if (comments_open() || get_comments_number()) {
                                    comments_template();
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="gen-sidebar <?php echo $sidebar_class?> sidebar primary">
                        <?php get_sidebar(); ?>
                    </div>

                </div>
            </div>
        </article>
        <!-- END Single CPT -->
    </section>


    <!-- END Page Content -->

<?php get_footer(); ?>