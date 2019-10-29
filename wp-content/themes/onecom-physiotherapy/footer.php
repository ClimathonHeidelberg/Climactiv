<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package blog
 */
?>

<?php
$footer_widgets = wp_get_sidebars_widgets();
if (!empty($footer_widgets['oct-footer-1']) || !empty($footer_widgets['oct-footer-2']) || !empty($footer_widgets['oct-footer-3'])) :
    ?>
    <footer id="oct-site-footer" class="footer-section bg-with-black">
        <div class="container no-padding">
            <div class="row">
                <div class="col-md-4 flex-column">
                    <div class="v-center">
                        <?php dynamic_sidebar('oct-footer-1'); ?>
                    </div>

                </div>
               
                <div class="col-md-4 push-md-4 flex-column">
                    <div class="v-center">
                        <?php dynamic_sidebar('oct-footer-2'); ?>
                    </div>

                </div>

                <div class="col-md-4 pull-md-4 flex-column">
                    <div class="v-center">
                        <?php dynamic_sidebar('oct-footer-3'); ?>
                        <?php echo ot_get_option('copyright_text')?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

<?php endif; ?>
</div><!-- #wrapper -->
</div>
<?php wp_footer(); ?>
</body>
</html>
