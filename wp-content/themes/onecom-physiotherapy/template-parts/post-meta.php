<?php if (ot_get_option('show_post_meta') === 'test'):?>
<div class="post-meta mb-md-4" role="contentinfo">
    <ul>
        <!-- Post Author -->
        <li>
            <a class="post-author" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
				<?php if ( strlen( get_the_author_meta( 'first_name' ) ) ) {
					echo get_the_author_meta( 'first_name' ) . ' ' . get_the_author_meta( 'last_name' );
				} else {
					echo get_the_author();
				}
				?>
            </a>
        </li>

        <!-- Post Publish & Updated Date & Time -->
        <li>
			<?php
			$time_string = '<time class="post-date entry-date published updated" datetime="%1$s">%2$s</time>';
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="post-date entry-date published" datetime="%1$s" title="">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
			}

			$time_string = sprintf( $time_string,
				get_the_date( DATE_W3C ),
				get_the_date(),
				get_the_modified_date( DATE_W3C ),
				get_the_modified_date()
			);
			echo $time_string;
			?>
        </li>

        <!-- Post Categories -->
		<?php if ( ! empty( wp_get_post_categories( get_the_ID() ) ) ): ?>

            <li class="post-categories">
				<?php the_category( ', ' ); ?>
            </li>

		<?php endif; ?>

    </ul>
</div>
<?php endif;?>