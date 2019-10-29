<form role="search" method="get" id="searchform" class="searchform" action="<?php echo home_url( '/' ); ?>">
    <div>
        <label class="screen-reader-text" for="s"><?php echo _x( 'Search for:', 'label' ) ?></label>
        <input type="text" class="form-control search-field" id="s"
               placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder' ); ?>"
               value="<?php echo get_search_query() ?>" name="s"
               title="<?php echo _x( 'Search for:', 'label' ); ?>" />

        <input type="submit" id="searchsubmit" class="button-alt float-right my-2 py-2 px-4 search-sub" value="<?php echo esc_attr_x( 'Search', 'oct-physiotherapy' ) ?>">
    </div>
</form>