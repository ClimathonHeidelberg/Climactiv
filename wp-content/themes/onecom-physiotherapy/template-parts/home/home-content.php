<section class="section solid white-bg text-center home-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-content">
                    <?php
                    add_filter('the_content', function () {
                        return wpautop(get_post()->post_content);
                    });
                    ?>
                    <?php
                    if (have_posts()) :
                        while (have_posts()) :
                            the_post();
                            the_content();
                        endwhile;
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>