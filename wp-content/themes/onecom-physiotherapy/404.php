<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

    <!-- START Page Content -->
    <section class="page-content" role="main">
        <div class="container">
	        <?php get_template_part('template-parts/content/content', 'none'); ?>
        </div>
    </section>

<?php get_footer(); ?>