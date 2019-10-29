<?php

/**
 * Extend Recent Posts Widget
 *
 * Adds different formatting to the default WordPress Recent Posts Widget
 */
Class My_Recent_Posts_Widget extends WP_Widget_Recent_Posts
{

    function widget($args, $instance)
    {

        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', OC_TEXT_DOMAIN) : $instance['title'], $instance, $this->id_base);

        if (empty($instance['number']) || !$number = absint($instance['number']))
            $number = 10;

        $r = new WP_Query(apply_filters('widget_posts_args', array('posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true)));
        if ($r->have_posts()) :

            echo $before_widget;
            if ($title)
                echo $before_title . $title . $after_title;
            ?>

            <?php foreach ($r->posts as $recent_post) : ?>
                <?php
                $post_title = get_the_title($recent_post->ID);
                $title = (!empty($post_title) ) ? $post_title : __('(no title)');
                $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
                ?>
                <section class="oct-recent-posts my-md-2">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-12 col-lg-6 oct-recent-post-thumb">
                            <?php
                            if (get_post_meta($recent_post->ID, 'featured_video_switch', true) == 'on') {
                                $featured_video_url = get_post_meta($recent_post->ID, 'featured_video_url', true);
                                ?>
                                <div class="oct-featured-media">
                                <?php echo do_shortcode("[video src='" . $featured_video_url . "']"); ?>
                                </div>
                                <?php } else if (has_post_thumbnail($recent_post->ID)) {
                                    ?>
                                <figure class="media-thumbnails" >
                                <?php echo get_the_post_thumbnail($recent_post->ID, 'small_featured', array('class' => 'img-fluid')); ?>
                                </figure>
                                <?php } else {
                                ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/image-not-found-240x160.png" alt="<?php the_title(); ?>" class='img-fluid'/>
                <?php }
                ?>
                        </div>
                        <div class="col-12 col-sm-6 col-md-12 col-lg-6 oct-recent-post-content">

                            <h6 class="mb-1">
                                <a href="<?php the_permalink($recent_post->ID); ?>" title="<?php echo $title; ?>">
                <?php echo $title; ?>
                                </a>
                            </h6>
                            <!-- Post meta data -->
                <?php if ($show_date) { ?>
                                <!-- Post Publish & Updated Date & Time -->
                                <span class="post-date">
                                    <i class="dashicons dashicons-clock"></i>
                                    <?php
                                    $time_string = '<time class="post-date entry-date published updated" datetime="%1$s">%2$s</time>';
                                    $time_string = sprintf($time_string, get_the_date(DATE_W3C), get_the_date(), get_the_modified_date(DATE_W3C), get_the_modified_date());
                                    echo $time_string;
                                    ?>
                                </span>
                <?php } ?>
                            <!-- End Post meta data -->

                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
            <?php
            echo $after_widget;
            wp_reset_postdata();
        endif;
    }

}

function my_recent_widget_registration()
{
    unregister_widget('WP_Widget_Recent_Posts');
    register_widget('My_Recent_Posts_Widget');
}

add_action('widgets_init', 'my_recent_widget_registration');


add_action('load-widgets.php', 'one_color_picker_load');

function one_color_picker_load()
{
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

/**
 *   ########### Social Icons widget ###########
 * */
if (!class_exists('one_social_widget')) {

    class one_social_widget extends WP_Widget
    {

        function __construct()
        {

            parent::__construct(
                    'one_social_widget', // Base ID
                    __('Social Icons', OC_TEXT_DOMAIN), // Name
                    array('description' => __('Displays social icons list.', OC_TEXT_DOMAIN),) // Args
            );
        }

        public function widget($args, $instance)
        {
            global $widget_default_color;
            $widget_default_color = true;

            $widget_id = $args['widget_id'];
            // Our variables from the widget settings
            $title = $instance['title'];

            ob_start();
            echo $args['before_widget'];

            if (!empty($instance['title'])) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            if (empty($instance['icon_default_color']) || $instance['icon_default_color'] === 'off') {
                if (!empty($instance['icon_color'])) {
                    echo '
                        <style>
                            html #' . $widget_id . ' .oct-social-icons ul li > a svg * {
                                fill : ' . $instance['icon_color'] . '
                            }
                        </style>';
                }
                if (!empty($instance['icon_hover_color'])) {
                    echo '
                        <style>
                        html #' . $widget_id . ' .oct-social-icons ul li:hover > a svg * {
                            fill : ' . $instance['icon_hover_color'] . '
                        }
                        </style>';
                }
                $widget_default_color = false;
            }

            /* Include social media links */
            include(THM_DIR_PATH . '/template-parts/social-icons.php');
            echo $args['after_widget'];
            ob_end_flush();
        }

        public function form($instance)
        {
            $title = !empty($instance['title']) ? $instance['title'] : '';

            $icon_color = $icon_hover_color = $icon_default_color = '';

            $skin_customize_on_off = ot_get_option('skin_customize_on_off');
            if ($skin_customize_on_off == 'on') {
                $icon_color = ot_get_option('skin_customize_social_color');
                $icon_hover_color = ot_get_option('skin_customize_social_hover_color');
            }

            $icon_color = !empty($instance['icon_color']) ? $instance['icon_color'] : $icon_color;
            $icon_hover_color = !empty($instance['icon_hover_color']) ? $instance['icon_hover_color'] : $icon_hover_color;
            $icon_default_color = !empty($instance['icon_default_color']) ? $instance['icon_default_color'] : $icon_default_color;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', OC_TEXT_DOMAIN); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            </p>
            <table class="form-table">
                <tr>
                    <td style="width:45%"><label for="<?php echo $this->get_field_id('icon_default_color'); ?>"><?php _e('Social Icon Default Colors', OC_TEXT_DOMAIN); ?>:</label></td>
                    <td><input class="one-social-default-checkbox" id="<?php echo $this->get_field_id('icon_default_color'); ?>" name="<?php echo $this->get_field_name('icon_default_color'); ?>" type="checkbox" <?php checked('on', esc_attr($icon_default_color)); ?> /></td>
                </tr>
                <tr class="toggle-tr">
                    <td style="vertical-align:top"><label for="<?php echo $this->get_field_id('icon_color'); ?>"><?php _e('Icon Color', OC_TEXT_DOMAIN); ?>:</label></td>
                    <td><input class="widefat colorpicker onecom_widget_colorpicker" id="<?php echo $this->get_field_id('icon_color'); ?>" name="<?php echo $this->get_field_name('icon_color'); ?>" type="text" value="<?php echo esc_attr($icon_color); ?>" /></td>
                </tr>
                <tr class="toggle-tr">
                    <td style="vertical-align:top"><label for="<?php echo $this->get_field_id('icon_hover_color'); ?>"><?php _e('Icon Hover Color', OC_TEXT_DOMAIN); ?>:</label></td>
                    <td><input class="widefat colorpicker onecom_widget_colorpicker" id="<?php echo $this->get_field_id('icon_hover_color'); ?>" name="<?php echo $this->get_field_name('icon_hover_color'); ?>" type="text" value="<?php echo esc_attr($icon_hover_color); ?>" /></td>
                </tr>
                <tr class="toggle-tr">
                    <td colspan="2">
                        <p class="description"><?php _e('If colors are not set, skin colors will be applied.', OC_TEXT_DOMAIN); ?></p>
                    </td>
                </tr>
            </table>

            <p><span class="dashicons dashicons-external"></span> <a href="<?php echo menu_page_url('octheme_settings', false) . '#section_social_links'; ?>" target="_blank"><?php _e('Manage Social Icons', OC_TEXT_DOMAIN) ?></a></p>
            <br/>
            <?php
        }

        public function update($new_instance, $old_instance)
        {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            $instance['icon_color'] = (!empty($new_instance['icon_color']) ) ? strip_tags($new_instance['icon_color']) : '';
            $instance['icon_hover_color'] = (!empty($new_instance['icon_hover_color']) ) ? strip_tags($new_instance['icon_hover_color']) : '';
            $instance['icon_default_color'] = (!empty($new_instance['icon_default_color']) ) ? strip_tags($new_instance['icon_default_color']) : '';
            return $instance;
        }

    }

}

/**
 * Register widgets
 * */
add_action('widgets_init', 'one_theme_register_widgets');

function one_theme_register_widgets()
{
    register_widget("one_social_widget");
}
?>