<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php
    // You can start editing here -- including this comment!
    if (have_comments()) :
        ?>
        <h5 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ('1' === $comments_number) {
                /* translators: %s: post title */
                printf(__('One Reply to &ldquo;%s&rdquo;', 'comments title', 'oct-physiotherapy'), get_the_title());
            } else {
                printf(
                        /* translators: 1: number of comments, 2: post title */
                        _nx(
                                '%1$s Reply to &ldquo;%2$s&rdquo;', '%1$s Replies to &ldquo;%2$s&rdquo;', $comments_number, 'comments title', ''
                        ), number_format_i18n($comments_number), get_the_title()
                );
            }
            ?>
        </h5>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                    array(
                        'avatar_size' => 100,
                        'style' => 'ol',
                        'short_ping' => true,
                        'reply_text' => __('Reply'),
                    )
            );
            ?>
        </ol>

        <?php
        the_comments_pagination(
                array(
                    'prev_text' => '<span class="screen-reader-text">' . __('Previous') . '</span>',
                    'next_text' => '<span class="screen-reader-text">' . __('Next') . '</span>',
                )
        );

    endif; // Check for have_comments().
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
        ?>

        <p class="no-comments"><?php _e('Comments are closed.', 'oct-physiotherapy'); ?></p>
        <?php
    endif;
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $fields = [
        'author' => '<fieldset><label for="author">' . __('Name', 'oct-physiotherapy') . ( $req ? '<span class="required">*</span>' : '' ) . '</label>' . '<input id="author" class="form-control" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '"' . $aria_req . ' /></fieldset>',
        'email' => '<fieldset><label for="email">' . __('Email', 'oct-physiotherapy') . ( $req ? '<span class="required">*</span>' : '' ) . '</label>' . '<input id="email" class="form-control" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></fieldset>',
    ];
    comment_form([
        'fields' => apply_filters('comment_form_default_fields', $fields),
        'class_submit' => 'button-alt float-right my-2 py-2 px-4',
        'comment_field' => '<label for="comment">' . _x('Comment', 'noun') . '</label><textarea id="comment" name="comment" rows="3" class="form-control" aria-required="true"></textarea></fieldset>'
    ]);
    ?>

</div><!-- #comments -->
