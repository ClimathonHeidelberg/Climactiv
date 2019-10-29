<?php $nav_class = ""; ?>
<section class="site-header oct-header-menu d-none d-lg-block">
    <header>
        <div class="container">
            <div class="row align-items-center logo_nav_wrap">
                <div class="col-3 col-md-3">
                    <div class="oct-site-logo">
                        <h1 class="<?php if ('off' != ot_get_option('logo_switch')) {
                                        echo 'mb-0';
                                    } ?>">
                            <?php if ('off' != ot_get_option('logo_switch')) : ?>
                                <a href="<?php echo home_url('/'); ?>" rel="home">
                                    <?php
                                        $logo = ot_get_option('logo_img');
                                        if (strlen($logo)) {
                                            $nav_class = 'with-logo';
                                            printf('<img src="%s" alt="%s" class="py-1" role="logo" />', $logo, get_bloginfo('name'));
                                        } else {
                                            echo get_bloginfo('title');
                                        }
                                        ?>
                                </a>
                        </h1>
                        <!-- END logo container -->
                    <?php endif; ?>
                    </div>
                </div>
                <div class="col-8 col-md-9 float-right">
                    <!-- START nav container -->
                    <nav class="nav primary-nav float-right <?php echo $nav_class; ?>" id="primary-nav">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary_oct_physiotherapy',
                                'container' => '',
                                'fallback_cb' => 'onecom_add_nav_menu',
                            )
                        );
                        ?>
                    </nav>
                </div>
            </div>
        </div>
    </header>
</section>