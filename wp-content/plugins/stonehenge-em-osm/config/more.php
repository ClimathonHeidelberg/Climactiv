<?php
if (!defined('ABSPATH')) exit;
include_once(ABSPATH.'wp-admin/includes/plugin.php');

if( class_exists('Stonehenge_Plugin_Base') ) {
	new Stonehenge_More_Plugins();
}

Class Stonehenge_More_Plugins {


	#===============================================
	public function __construct(){
		add_action('stonehenge_menu', array(__CLASS__, 'add_submenu_page'), 100);
	}


	#===============================================
	public static function add_submenu_page() {
		add_submenu_page(
			'stonehenge-creations',
			'More...',
			'<span style="color:#00FF00;">&xrArr; More</span>',
			'manage_options',
			'stonehenge-plugins',
			array('Stonehenge_More_Plugins', 'show_this_page')
		);
	}


	#===============================================
	public static function show_this_page() {
		$plugins = self::define_plugins();
		Stonehenge_Plugin::load_core_assets();
		?>
		<div class="wrap">
			<h1>More Plugins by Stonehenge Creations</h1>
			<div id="poststuff">
				<div class="inside">
					<div id="postbox-container" class="postbox-container stonehenge-settings">
						<div class="meta-box-sortables ui-sortable" id="normal-sortables">
							<div class="postbox" id="more">
								<div class="inside">
									<p>Here is an overview of other plugins and add-ons that I have created. Feel free to check them all out! :-)<br>
										Missing anything? Let me know!
									</p>
									<?php
									if( !$plugins || empty($plugins) ) {
										echo '<p>There was an error retrieving the Plugins Overview. Please try again later.</p>';
									} else {
										foreach($plugins as $plugin ) {
											$base		= $plugin['slug'];
											$file 		= "{$base}/{$base}.php";
											$image_path = plugin_dir_path( __FILE__). "/images/{$base}.jpg";
											$image_url 	= plugins_url("images/{$base}.jpg", __FILE__);
											$default 	= plugins_url('images/stonehenge.jpg', __FILE__);
											$image 		= empty($plugin['icon']) ? $default : ( file_exists($image_path) ? $image_url : $plugin['icon'] );
											$link 		= "a href='{$plugin['link']}' target='_blank' title='{$plugin['name']}'";
											$needs 		= !empty($plugin['needs']) ? "Requires: <font color='#0081bc'>{$plugin['needs']}</font>" : "";

											echo "<table class='addon-card'>";
											echo "<tr><td width='100px'><{$link}><img src='{$image}' class='icon' alt='{$plugin['name']}'></a></td>";
											echo "<td><h4><{$link}>{$plugin['name']}</a></h4>";
											echo "<p>{$plugin['info']}</p>";
											echo "</td></tr>";
											echo "<tr class='needs'><td colspan='2'>{$needs}</td></tr>";
											echo "<tr><td colspan='2' style='height:35px; vertical-align:bottom !important;'>";

											// Check if plugin is already installed and activated.
											if( is_plugin_active( $file ) ) {
												echo '<span class="stonehenge-success" style="float:right;">'. __('Plugin <strong>activated</strong>.') .'</span>';
											}
											else {
												if( !$plugin['type'] ) {
													$install = sprintf(__('Install %s'), 'Plugin');
													echo "<button class='stonehenge-button' style='float:right;'><{$link}>{$install}</a></button>";
												}
												else {
													echo '<span class="stonehenge-info" style="float:right;">Coming Soon</span>';
												}
											}
											echo '</td></tr></table>';
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


	#===============================================
	public static function define_plugins() {
		$plugins = get_transient('stonehenge_creations_plugins_feed');
		if( false === $plugins ) {
			$response = json_decode( file_get_contents( STONEHENGE . 'edd-api/v2/products/', true));
			unset($response->request_speed);
			$plugins = array();
			foreach( $response as $products ) {
				foreach( $products as $product ) {
					$info = $product->info;
					$title = str_replace(' – ', ' ', $info->title);
					$plugins[$title] = array(
						'id' 	=> $info->id,
						'slug' 	=> $info->slug,
						'name' 	=> $info->title,
						'link'	=> $info->link,
						'icon'	=> $info->thumbnail,
						'type' 	=> $product->coming_soon,
						'needs' => @$info->category[0]->name,
						'paid'	=> $product->licensing->enabled,
						'info'	=> $info->excerpt,
					);
				}
			}
			ksort($plugins);
			set_transient('stonehenge_creations_plugins_feed', $plugins, 86400 ); // Once per day.
		}
		return $plugins;
	}

} // End class.
