<section class="oct-head-bar d-md-block d-lg-none">
    <div class="container oct-head-bar-content">
        <div class="row d-md-block mobile-m">
            <div class="col-10 mobile-page-title">
                <?php the_title() ?>
            </div>
            <div class="col-2 menu-toggle"></div>
        </div>

        <div class="row mobile-title mt-1">
            <div class="col-12">
                <div class="oct-site-logo float-none text-center">
                    <?php if ('off' != ot_get_option('logo_switch')): ?>
                        <h1 class="site-title">
                            <a href="<?php echo home_url('/'); ?>" rel="home">
                                <?php
                                $logo = ot_get_option('logo_img');
                                if (strlen($logo)) {
                                    printf('<img src="%s" alt="%s" role="logo" />', $logo, get_bloginfo('name'));
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
        </div>
    </div>
</section>