<div class="wrap onecom-varnish">
	<h2 class="one-logo"> 
		<div class="textleft">
			<span>
				<?php _e( 'One.com Performance Cache improves your website\'s performance', self::textDomain ); ?>
			</span>
			<p><?php _e( 'With One.com Performance Cache enabled your website loads a lot faster. We save a cached copy of your website on a Varnish server, that will then be served to your next visitors. <br/>This is especially useful if you have a lot of visitors. It may also help to improve your SEO ranking. If you would like to learn more, please read our help article: <a href="https://help.one.com/hc/en-us/articles/360000080458" target="_blank">How to use the One.com Performance Cache for WordPress</a>.', self::textDomain ); ?></p>
		</div>
		<div class="textright">
			<img src="<?php echo $this->OCVCURI.'/assets/images/one.com-logo.png' ?>" alt="One.com" srcset="<?php echo $this->OCVCURI.'/assets/images/one.com-logo@2x.png 2x' ?>" /> 
		</div>
	</h2>
	<div class="wrap_inner inner one_wrap">
		<?php if( 'true' === $this->OCVer->isVer() ) : ?>
			<form method="post" action="options.php">
		        <?php
		            settings_fields(self::defaultPrefix . 'oc_options');
		            do_settings_sections(self::defaultPrefix . 'oc_options');
		            submit_button();
		        ?>
		        <?php 
		        	$url = wp_nonce_url( add_query_arg( self::getOCParam, 1 ), self::textDomain );
		        ?>
		        <p class="submit">
		        	<button id="ocvc-settings-page-purge" data-url="<?php echo $url ?>" class="button button-secondary"><?php _e( 'Purge Performance Cache', self::textDomain ); ?></button>
		        </p>
		    </form>
		<?php else : ?>
			<!-- <p><?php //_e( "To access this plugin, you must upgrade your hosting package to a <a href='https://www.one.com/en/wordpress' target='_blank'>WordPress package</a>.<br/>Read more in this <a href='https://help.one.com/hc/en-us/sections/115001491649-WordPress' target='_blank'>guide</a>.", self::textDomain ); ?></p> -->
			<p><?php _e( "To access this plugin, you must upgrade your hosting package to a <a href='https://www.one.com/en/wordpress' target='_blank'>WordPress package</a>.", self::textDomain ); ?></p>
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
	( function( $ ) {
		function purgeButtonVerify() {
			var value = $( '#varnish_caching_ttl' ).val().trim();
			if( '' == value ) {
				$( '#ocvc-settings-page-purge' ).attr( 'disabled', true );
			} else {
				$( '#ocvc-settings-page-purge' ).attr( 'disabled', false );
			}
		}
		function enablePurgeButton() {
			$( '#ocvc-settings-page-purge' ).attr( 'disabled', false );
		}
		function disablePurgeButton() {
			$( '#ocvc-settings-page-purge' ).attr( 'disabled', true );
		}
		$(document).ready(function(){

			$( '#ocvc-settings-page-purge' ).on( 'click', function(event) {
				event.preventDefault();
				var url = $( this ).attr( 'data-url' );
				window.location.href = url;
			} );

			if( $( '#varnish_caching_ttl' ).length > 0 ) {
				purgeButtonVerify();
			}
			
			$( '#varnish_caching_ttl' ).on( 'input', function() {
				disablePurgeButton();
			} );
		});
	} )( jQuery );
</script>