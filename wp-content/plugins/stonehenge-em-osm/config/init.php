<?php
if( !defined('ABSPATH') ) exit;
include_once(ABSPATH.'wp-admin/includes/plugin.php');
include_once('license.php');

if( !class_exists('Stonehenge_Plugin_Base') ) {

	Class Stonehenge_Plugin_Base {

		#===============================================
		public function __construct() {
			add_action('admin_menu', array($this, 'create_admin_menu'), 10, 2);
			include('more.php');
		}

		#===============================================
		public static function create_main_plugin_data() {
			$main = array(
				'name' 		=> 'Stonehenge Creations',
				'short' 	=> 'Stonehenge',
				'text' 		=> 'stonehenge-creations',
				'slug' 		=> 'stonehenge-creations',
				'capab'		=> 'manage_options',
				'parent' 	=> 'stonehenge-creations',
				'icon' 		=> 'dashicons-plugins-checked',
			);
			return $main;
		}

		#===============================================
		public static function create_admin_menu() {
			$main = self::create_main_plugin_data();
			add_menu_page( $main['name'], $main['short'], $main['capab'], $main['slug'], array(__CLASS__, 'show_main_page'), $main['icon'], 26);
			add_submenu_page( $main['slug'], $main['name'], $main['short'], $main['capab'], $main['slug'], array(__CLASS__, 'show_main_page'));
			remove_submenu_page($main['slug'], $main['slug']);
			do_action('stonehenge_menu');
		}

		#===============================================
		public static function show_main_page() {
			/* Nothing to display */
		}
	}
}


if( !class_exists('Stonehenge_Plugin') ) {

	Class Stonehenge_Plugin {

		#===============================================
		public function __construct($plugin) {
			global $config, $SC_License;
			$this->plugin	= $plugin;
			$config 		= $plugin;
			$SC_License 	= new Stonehenge_License($plugin);

			add_filter('plugin_action_links', array($this, 'add_settings_link'), 10, 2);
			add_filter('plugin_row_meta', array($this, 'create_additional_plugin_links'), 10, 2);
			add_action('admin_enqueue_scripts', array($this, 'register_core_assets'), 20);
			add_action('wp_enqueue_scripts', array($this, 'register_core_assets'), 20);

			$this->updater($plugin);

			if( $this->check_dependency($plugin) ) {
				if( !has_action('stonehenge_menu') ) {
					new Stonehenge_Plugin_Base();
				}
				add_action('stonehenge_menu', array($this, 'create_sub_menu'), $plugin['prio']);

				if( !empty($plugin['license']) && $SC_License->is_valid($plugin) ) {
					add_action('admin_init', array($this, 'start_plugin'));
					add_action('stonehenge_loaded', array($this, 'register_options'));
					add_action('admin_enqueue_scripts', array($plugin['class'], 'register_assets'), 20);
					add_action('wp_enqueue_scripts', array($plugin['class'], 'register_assets'), 20);
				}
			}
			// Clean up.
			if( wp_next_scheduled('puc_cron_updater-'. $plugin['base']) && !has_action( 'puc_cron_updater-'. $plugin['base'] ) ) {
				wp_clear_scheduled_hook('puc_cron_updater-'. $plugin['base']);
			}
		}


		#===============================================
		public function updater( $plugin ) {
			global $SC_License;
			if( $SC_License->is_licensed($plugin) ) {
				$base = $plugin['base'];
				require('server/update-checker.php');
				$UpdateChecker = Puc_v4_Factory::buildUpdateChecker(
					'https://www.stonehengecreations.nl/update-checker/?action=get_metadata&slug='.$base,
					WP_PLUGIN_DIR ."/{$base}/{$base}.php",
					$base
				);

				if( has_action('puc_cron_updater-'. $plugin['base']) && method_exists($plugin['class'], 'validate') ) {
					add_action('puc_cron_updater-'. $plugin['base'], array($plugin['class'], 'validate'), 8);
				}
			}
		}


		#===============================================
		public function check_dependency( $plugin ) {
			$dependency = method_exists($plugin['class'], 'dependency') ? $plugin['class']::dependency() : true;
			return $dependency;
		}


		#===============================================
		public static function start_plugin() {
			$plugin = $this->plugin;
			$saved 	= get_option( $plugin['slug'] );
			if( !$saved || empty($saved) || !is_array($saved) ) {
				self::maybe_set_defaults( $plugin );
			}
			$check = ( get_site_option($plugin['version_slug']) != $plugin['version'] ) ? false : true;
			if( !$check ) {
				if( method_exists( $plugin['class'], 'update_this_plugin') ) {
					$plugin['class']::update_this_plugin();
				}

				if( !add_site_option($plugin['version_slug'], $plugin['version'], '', 'no') ) {
					update_site_option($plugin['version_slug'], $plugin['version'], 'no');
				}
			}
			return;
		}


		#===============================================
		public static function maybe_set_defaults( $plugin ) {
			$defaults 	= array();
			$sections 	= $plugin['class']::define_options();
			foreach($sections as $section) {
				foreach($section['fields'] as $field) {
					$id 		= $section['id'];
					$option 	= $field['id'];
					$no_fields 	= array('info', 'intro', 'span', 'notice', 'recipients', 'pro');
					if( !in_array( $field['type'], $no_fields) ) {
						$value = !empty($field['default']) ? $field['default'] : null;
						$defaults[$section['id']][$field['id']] = esc_attr($value);
					}
				}
			}
			if( count( (array) $defaults) == 1 ) {
				$defaults = array_values($defaults)[0];
			}
			if( method_exists( $plugin['class'], 'add_defaults') ) {
				$extra 		= $plugin['class']::add_defaults();
				$defaults 	= wp_parse_args_recursive($extra, $defaults);
			}
			if( !add_option( $plugin['slug'], $defaults, '', 'no') ) {
				update_option( $plugin['slug'], $defaults, 'no');
			}
			return;
		}


		#===============================================
		public static function register_core_assets() {
			$version = '3.1';
			wp_register_style('stonehenge-css', plugins_url('assets/stonehenge.min.css', __FILE__), '', $version, 'all');
			wp_register_script('stonehenge-js', plugins_url('assets/stonehenge.min.js', __FILE__), array('jquery'), $version, true);
			wp_localize_script('stonehenge-js', 'CORE', self::localize_core_assets());

			$locale = substr(get_bloginfo('language'), 0, 2);
			wp_register_script('parsley-validation', plugins_url('assets/parsley/parsley.min.js', __FILE__), null, $version, true);
			wp_register_script('parsley-locale', plugins_url('assets/parsley/i18n/'. $locale .'.js', __FILE__), null, $version, true);
			wp_register_script('parsley-locale-extra', plugins_url('assets/parsley/i18n/'. $locale .'.extra.js', __FILE__), null, $version, true);
		}


		#===============================================
		public static function load_core_assets() {
			$styles 	= array('stonehenge-css');
			$scripts 	= array('stonehenge-js', 'parsley-validation', 'parsley-locale', 'parsley-locale-extra');
			wp_enqueue_style( $styles );
			wp_enqueue_script( $scripts );
		}


		#===============================================
		public static function localize_core_assets() {
			global $config;
			$plugin = $config;
			$text 	= $plugin['text'];
			$saved 	= get_option( $plugin['slug'] );

			$loc['showError'] 		= __('Not all required fields were filled out. Please check all sections and try again.', $text);
			$loc['add'] 			= __('Add');
			$loc['remove']			= __('Remove');
			$loc['edit'] 			= __('Edit');
			$loc['year_range'] 		= '"'. date('Y') .':'. (date('Y') + 5) .'"';
			$loc['date_format']		= get_option('dbem_date_format');
			$dbem_date_format_js 	= get_option('dbem_date_format_js');
			$loc['date_format_js']	= empty($dbem_date_format_js) ? 'yy-mm-dd' : $dbem_date_format_js;
			$loc['time_format']		= get_option('time_format');
			$loc['chooseFile'] 		= __('Choose File');
			$loc['locale']			= strtoupper( substr(get_bloginfo('language'), 0, 2) );
			return $loc;
		}


		#===============================================
		public function create_sub_menu() {
			$plugin = $this->plugin;
			$page 	= add_submenu_page(
				'stonehenge-creations',
				__($plugin['name'], $plugin['text']),
				$plugin['icon'] . esc_html__($plugin['short'], $plugin['text']),
				'manage_options',
				esc_html($plugin['slug']),
				array($this, 'show_options_page')
			);
			add_action('admin_print_styles-'.$page, array(__CLASS__, 'load_core_assets'));
			add_action('admin_print_styles-'.$page, array($plugin['class'], 'load_admin_assets'));
		}


		#===============================================
		public static function show_options_page() {
			$plugin 	= $this->plugin;
			$text 		= $plugin['text'];
			$SC_License = new Stonehenge_License($plugin);

			?>
			<div class="wrap">
				<?php
				if( $SC_License->is_licensed($plugin) ) {
					echo '<a href="'. STONEHENGE .'account/tickets/" target="_blank" class="stonehenge-button" style="float:right;margin-top:10px;">Get Support</a>';
				}
				?>
				<h1><?php echo wp_kses_allowed($plugin['icon']) . esc_html__( $plugin['name'], $text); ?></h1>
				<div class="listFieldError" style="display:none;"></div>
				<br style="clear:both;">

				<?php
				do_action('stonehenge_before_options');

				if( $SC_License->is_licensed($plugin) ) {
					$SC_License->show_license($plugin);
				}

				if( $SC_License->is_valid($plugin) && did_action($plugin['base'].'_loaded') ) {
					?>
					<form method="post" action="options.php" id="stonehenge-options-form" autocomplete="off" data-parsley-validate="" novalidate="">
						<?php
						settings_fields($plugin['slug']);
						do_settings_sections($plugin['slug']);
						$options = $plugin['class']::define_options();
						self::display_options($plugin, $options);
						?>
						<div class="listFieldError" style="display:none;"></div>
						<input type="submit" class="stonehenge-button" value="<?php esc_html_e('Save Changes'); ?>" onclick="setTimeout(showErrors, 100)">
					</form>
					<?php
				}

				do_action('stonehenge_after_options');
				?>
				<br style="clear:both;">
			</div>
			<?php
		}


		#===============================================
		public static function display_options( $plugin, $sections ) {
			if( !is_array($sections) ) {
				return;
			}
			foreach( $sections as $section ) {
				?>
				<div class="stonehenge-section" id="<?php echo esc_html($section['id']); ?>">
					<div class"stonehenge-section-header">
						<h3 class="handle"><?php esc_html_e($section['label'], $plugin['text']); ?></h3>
					</div>
					<div class="stonehenge-section-content">
						<table class="form-table stonehenge-table">
						<?php
						$database	= get_option( $plugin['slug'] );
						foreach( $section['fields'] as $fields => $field ) {
							$default 	= wp_kses_allowed( $field['default'] ?? '');
							$section_id	= esc_attr( $section['id'], ENT_QUOTES );
							$field_id	= esc_attr( $field['id'], ENT_QUOTES );

							if( count((array) $sections) > 1 ) {
								$id 		= esc_attr( str_replace('-', '_', "{$section_id}_{$field_id}"), ENT_QUOTES );
								$name 		= esc_attr( "{$plugin['slug']}[{$section_id}][{$field_id}]", ENT_QUOTES );
								$saved 		= $database[$section_id][$field_id] ?? $default; 	// Escape later - multiple options.
							}
							else {
								$id 		= esc_attr( str_replace('-', '_', "{$section_id}_{$field_id}"), ENT_QUOTES );
								$name 		= esc_attr( "{$plugin['slug']}[{$field_id}]", ENT_QUOTES );
								$saved 		= $database[$field_id] ?? $default; 	// Escape later - multiple options.
							}

							$class 		= esc_attr( str_replace('_', '-', "{$section_id}-{$field_id}"), ENT_QUOTES );
							$type 		= esc_attr( $field['type'], ENT_QUOTES );
							$size 		= esc_attr( $field['size'] ?? 'regular-text', ENT_QUOTES );
							$min 		= esc_attr( $field['min'] ?? '1', ENT_QUOTES );
							$max 		= esc_attr( $field['max'] ?? '9999', ENT_QUOTES );

							$required	= isset($field['required']) && ($field['required'] != false) ? esc_attr('required=required', ENT_QUOTES) : '';
							$helper		= isset($field['helper']) ? '<p class="description">'. wp_kses_allowed($field['helper']) .'</p>' : '';
							$before	 	= isset($field['before']) ? '<p>'. wp_kses_allowed($field['before']) .'</p>' : '';
							$after		= isset($field['after']) ? '&nbsp;&nbsp;'. $field['after'] : '';

							$choices 	= $field['choices'] ?? array('no' => __('No'), 'yes' => __('Yes'));

							$label 		= esc_html( $field['label'] ?? '');
							$label 		= "<th scope='row'><label for='{$id}'>{$label}</label></th><td>{$before}";

							echo "<tr class='{$class}'>";
							switch( $type ) {
								case 'info':
								case 'intro':
								case 'notice':
									echo "<td colspan='2'><p>{$default}</p>";
									break;
								case 'span':
									echo "{$label}{$default}";
									break;
								case 'text':
								case 'email':
								case 'url':
								case 'password':
									echo sprintf('%s<input type="%s" id="%s" name="%s" value="%s" class="%s" %s>',
										$label, $type, $id, $name, esc_attr($saved, ENT_QUOTES), $size, $required);
									break;
								case 'number':
									echo sprintf('%s<input type="%s" id="%s" name="%s" value="%s" class="small" %s min="%s" max="%s">',
										$label, $type, $id, $name, esc_attr($saved, ENT_QUOTES), $required, $min, $max);
									break;
								case 'select':
								case 'dropdown':
									echo "{$label}<select name='{$name}' id='{$id}' {$required}>";
									// Add an empty option first.
									echo sprintf('<option value="" disabled selected>- %s -</option>', esc_html__('Select') );
									foreach( $choices as $k => $v ) {
										$selected = ($k === $saved ? esc_attr('selected') : '');
										echo sprintf('<option value="%s" %s>%s</option>', esc_attr($k, ENT_QUOTES), $selected, esc_html($v) );
									}
									echo '</select>';
									break;
								case 'textarea':
									echo sprintf('%s<textarea id="%s" name="%s" rows="6" %s>%s</textarea>',
										$label, $id, $name, $required, wp_kses_allowed(stripslashes($saved)) );
									break;
								case 'editor':
									$required  = $field['required'] ?? false;
									echo "{$label}";
									if( $required ) { add_filter('the_editor', array('Stonehenge_Plugin', 'require_editor'), 10, 1); }
									$args = array(
									    'wpautop' => true,
									    'media_buttons' => false,
									    'textarea_name' => $name,
									    'textarea_rows' => 6,
									    'editor_css' => '',
									    'editor_class' => '',
									    'teeny' => false,
									    'dfw' => true,
									    'tinymce' => false, // <-----
									    'quicktags' => true
									);
									wp_editor( wp_kses_allowed(stripslashes($saved)), $id, $args );
									if( $required ) { remove_filter('the_editor', array('Stonehenge_Plugin', 'require_editor'), 10, 1); }
									break;
								case 'file':
									wp_enqueue_script( 'jquery' );
									wp_enqueue_media();
									$file 	= esc_html__('Please select a file');
									$remove = esc_html__('Clear');
									$input 	= sprintf('<input type="url" id="%s" name="%s" value="%s" class="%s filename" readonly>',
										$id, $name, esc_url_raw($saved, ENT_QUOTES), 'regular-text');
									$select = sprintf('<button type="button" id="%s_button" class="button-secondary file-button" title="%s">%s</button>',
										$id, esc_attr($file, ENT_QUOTES), '&#x1F4CE; '. esc_html($file) );
									$clear 	= sprintf('<button type="button" class="button-secondary clear-file" title="%s">%s</button>',
										esc_attr($remove, ENT_QUOTES), '&#x2718;');
									echo sprintf('%s%s%s&nbsp;&nbsp;%s',
										$label, $input, $select, $clear
									);
									break;
								case 'checkboxes':
									echo "{$label}<div id='checkboxes' class='{$class}'>";
									foreach( $choices as $key => $result) {
										$checked = in_array($key, (array) $saved) ? esc_attr('checked=checked', ENT_QUOTES) : null;
										echo sprintf( '<input type="checkbox" id="%s" name="%s" value="%s" %s %s> <label for="%s">%s</label><br>',
											esc_attr($section_id.'_'.$key, ENT_QUOTES),
											$name.'[]',
											esc_attr($key, ENT_QUOTES),
											$checked,
											'data-parsley-group="status" '.$required,
											esc_attr($section_id.'_'.$key,
											ENT_QUOTES), esc_html($result)
										);
									}
									echo '</div>';
									break;
								case 'toggle':
									echo $label . '<div class="switch-toggle switch-holo" style="width: 120px; height:1.85em;">';
									foreach ( $choices as $v => $l ) {
										$checked = $v === $saved ? esc_attr('checked=checked', ENT_QUOTES) : '';
										echo sprintf( '<input type="radio" name="%s" id="%s" value="%s" %s %s><label for="%s">%s</label>',
											$name, esc_attr($id.'_'.$v, ENT_QUOTES), esc_attr($v, ENT_QUOTES), $checked, $required, esc_attr($id.'_'.$v, ENT_QUOTES), esc_html($l)
										);
									}
									echo '<a></a></div>';
									break;
									case 'flip':
										$checked = (!empty($saved) && $saved != 'no') ? 'checked="checked"' : '';
										echo sprintf('%s<label class="flip"><input type="checkbox" id="%s" name="%s" value="yes" %s><span data-unchecked="%s" data-checked="%s"></span></label>',
											$label, $id, $name, $checked, esc_attr( __('No'), ENT_QUOTES), esc_attr( __('Yes'), ENT_QUOTES));
									break;
								case 'radio':
									echo sprintf('%s<fieldset><legend class="screen-reader-text"></legend>', $label);
									$c = 0;
									foreach( $choices as $v => $l ) {
										$checked = $v === $saved ? esc_attr('checked=checked', ENT_QUOTES) : '';
										echo sprintf('<label><input type="radio" name="%s" id="%s" value="%s" %s %s><span>%s</span></label>%s',
											$name, esc_attr($id.'_'.$v, ENT_QUOTES), esc_attr($v, ENT_QUOTES), $checked, $required, esc_html($l), ($c < count($choices) - 1 ? '<br>' : ''));
										$c++;
									}
									echo "</fieldset>";
									break;
								case 'color':
									wp_enqueue_style('farbtastic');
									wp_enqueue_script('farbtastic');
									echo sprintf('%s<div style="position:relative;"><input type="text" name="%s" id="%s" %s><input type="button" class="pickcolor button-secondary" value="%s"><div class="colorpicker" style="z-index:100; position:absolute; display:none;"></div></div>',
									$label, $name, $id, $required, esc_html__('Select Color') );
									break;
								case 'date':
									wp_enqueue_script('jquery-ui-datepicker');
									wp_enqueue_style('datepicker', plugins_url('assets/datepicker.min.css', __FILE__));
									echo sprintf('%s<input type="text" name="%s" value="%s" class="datepicker" autocomplete="off" size="21" %s>',
										$label, $name, esc_attr($saved, ENT_QUOTES), $required);
									break;
								case 'time':
									wp_enqueue_script('timepicker-js', plugins_url('assets/timepicker.min.js', __FILE__), '', array('jquery'), false);
									echo sprintf('%s<input type="text" id="%s" name="%s" value="%s" class="time-picker" size="10" autocomplete="off">',
										$label, $id, $name, esc_attr($saved, ENT_QUOTES) );
									break;
								case 'feedback':
									echo sprintf('%s<input type="%s" id="%s" name="%s" value="%s" class="%s" %s>',
										$label, $type, $id, $name, $saved, 'large-text', $required);
									break;
								default:
									echo "{$label}Field not defined yet: {$type}</td>";
									break;
							}
							echo wp_kses_allowed($after) . wp_kses_allowed($helper) .'</td></tr>';
						}
						?>
						</table>
					</div>
				</div>
				<br style="clear:both;">
				<?php
			}
		}


		#===============================================
		public static function register_options() {
			$plugin = $this->plugin;
			register_setting( $plugin['slug'], $plugin['slug'], array( $plugin['class'], 'sanitize_options') );
		}


		#===============================================
		public static function sanitize_options($input, $sections) {
			if( count( (array) $sections) > 1 ) {
				$clean = self::sanitize_multiple( $input, $sections );
			} else {
				$clean = self::sanitize_single( $input, $sections );
			}
			return $clean;
		}


		#===============================================
		public static function sanitize_multiple( $input, $sections ) {
			$clean = array();
			foreach($sections as $section) {
				foreach($section['fields'] as $fields => $field) {
					$id 	= "{$section['id']}_{$field['id']}";
					$type 	= $field['type'];
					foreach($input as $tabs => $tab) {
						foreach($tab as $key => $value) {
							$tab_key = $tabs.'_'.$key;
							switch($field['type']) {
								case 'text':
								case 'number':
								case 'tel':
								case 'phone':
								case 'password':
								case 'select':
								case 'dropdown':
								case 'color':
									if($id === $tab_key) { $clean[$tabs][$key] = sanitize_text_field(stripslashes($input[$tabs][$key])); }
									break;
								case 'time':
									if($id === $tab_key) { $clean[$tabs][$key] = sanitize_text_field(stripslashes(date("H:i:s",strtotime($input[$tabs][$key])))); }
								break;
								case 'date':
									if($id === $tab_key) { $clean[$tabs][$key] = sanitize_text_field(stripslashes(date("Y-m-d",strtotime($input[$tabs][$key])))); }
								break;
								case 'toggle':
								case 'switch':
								case 'radio':
									if($id === $tab_key) { $clean[$tabs][$key] = sanitize_text_field(wp_unslash($input[$tabs][$key])); }
									break;
								case 'email':
									if($id === $tab_key) { $clean[$tabs][$key] = strtolower(sanitize_email($input[$tabs][$key])); }
									break;
								case 'textarea':
									if($id === $tab_key) { $clean[$tabs][$key] = wp_kses_allowed(stripslashes($input[$tabs][$key])); }
									break;
								case 'editor':
									if($id === $tab_key) { $clean[$tabs][$key] = wp_kses_allowed(stripslashes($input[$tabs][$key])); }
									break;
								case 'url':
								case 'media':
								case 'image':
								case 'file':
									if($id === $tab_key) { $clean[$tabs][$key] = esc_url_raw($input[$tabs][$key]); }
									break;
								case 'checkbox':
								case 'flip':
									if($id === $tab_key) { $clean[$tabs][$key] = sanitize_key($input[$tabs][$key]); }
									break;
								case 'checkboxes':
									if($id === $tab_key) { $clean[$tabs][$key] = @array_map('sanitize_text_field', wp_unslash($input[$tabs][$key])); }
									break;
								case 'multi':
								case 'select_multi':
									if($id === $tab_key) { $clean[$tabs][$key] = array_map('sanitize_text_field', $input[$tabs][$key]); }
									break;
								case 'feedback':
									if($id === $tab_key) { $clean[$tabs][$key] = wp_kses_some(stripslashes($input[$tabs][$key])); }
									break;
							}
						}
					}
				}
			}
			return $clean;
		}


		#===============================================
		public static function sanitize_single( $input, $sections ) {
			$clean = array();
			foreach( $sections[0]['fields'] as $fields => $field ) {
				$id = $field['id'];
				foreach( $input as $key => $value ) {
					switch($field['type']) {
						case 'text':
						case 'number':
						case 'tel':
						case 'phone':
						case 'password':
						case 'select':
						case 'dropdown':
						case 'color':
							if($id === $key) { $clean[$key] = sanitize_text_field(stripslashes($input[$key])); }
							break;
						case 'time':
							if($id === $key) { $clean[$key] = sanitize_text_field(stripslashes(date("H:i:s",strtotime($input[$key])))); }
						break;
						case 'date':
							if($id === $key) { $clean[$key] = sanitize_text_field(stripslashes(date("Y-m-d",strtotime($input[$key])))); }
						break;
						case 'toggle':
						case 'switch':
						case 'radio':
							if($id === $key) { $clean[$key] = sanitize_text_field(wp_unslash($input[$key])); }
							break;
						case 'email':
							if($id === $key) { $clean[$key] = strtolower(sanitize_email($input[$key])); }
							break;
						case 'textarea':
						case 'editor':
							if($id === $key) { $clean[$key] = wp_kses_allowed(stripslashes($input[$key])); }
							break;
						case 'url':
						case 'media':
						case 'image':
						case 'file':
							if($id === $key) { $clean[$key] = esc_url_raw($input[$key]); }
							break;
						case 'checkbox':
						case 'flip':
							if($id === $key) { $clean[$key] = sanitize_key($input[$key]); }
							break;
						case 'checkboxes':
							if($id === $key) { $clean[$key] = @array_map('sanitize_text_field', wp_unslash($input[$key])); }
							break;
						case 'multi':
						case 'select_multi':
							if($id === $key) { $clean[$key] = array_map('sanitize_text_field', $input[$key]); }
							break;
						case 'feedback':
							if($id === $tab_key) { $clean[$key] = wp_kses_some(stripslashes($input[$key])); }
							break;
					}
				}
			}
			return $clean;
		}


		#===============================================
		function add_settings_link( $links, $file ) {
			$plugin = $this->plugin;
			$base  	= $plugin['base'];
			if( $file != plugin_basename("{$base}/{$base}.php")) {
				return $links;
			}
			else {
				$settings_link = sprintf( '&#128736;&#65039; <a href="%s">%s</a>', $plugin['url'], __('Settings') );
				array_unshift($links, $settings_link);
				return $links;
			}
		}


		#===============================================
		function create_additional_plugin_links( $links, $file ) {
			$plugin 		= $this->plugin;
			$base 			= $plugin['base'];
			$style 			= 'style="color:red !important;"';
			$author			= 'DuisterDenHaag';
			$rate_url 		= 'https://wordpress.org/support/plugin/'. $base .'/reviews/?rate=5#new-post';
			$rate 			= array(' &#11088; <a href="'.$rate_url.'" target="_blank">'. __('Rate this plugin',$plugin['text']) .'</a>' );
			$support 		= array('<a href="https://www.stonehengecreations.nl/contact/open-a-ticket/" target="_blank" '.$style.'>&#127808; <strong>Premium Support</strong></a>');
			$donate			= 'https://useplink.com/payment/VRR7Ty32FJ5mSJe8nFSx';
			$donate  		= array('&#127873; <a href="'.$donate.'" target="_blank">'.__('Donate', $plugin['text']).'</a>');
			$more 			= array('<a href="https://www.stonehengecreations.nl/my-plugins/" target="_blank">&#x1F50C; More Goodies</a>');

			if( $file != plugin_basename("{$base}/{$base}.php")) {
				return $links;
			}
			return Stonehenge_License::is_licensed($plugin) ? array_merge($links, $support, $more) : array_merge($links, $rate, $donate, $more);
		}


		#===============================================
		public static function show_notice( $notice, $class ) {
			$notice  = wp_kses_allowed( $notice );
			return( '<p><span class="stonehenge-'.$class.'">'. $notice .'</span></p>' );
		}


		#===============================================
		public static function show_settings_notice( $plugin, $class ) {
			if( !current_user_can('manage_options') ) {
				return;
			}
			$message = sprintf("<strong>{$plugin['short']}: </strong>" . _x('Please check your <a href=%1$s>settings</a> and click on "%2$s".', 'Please check your settings and click on "Save Changes".', $plugin['text']), $plugin['url'], __('Save Changes') );

			add_action('admin_notices', function () use ($class, $message) {
				printf('<div class="notice notice-%1$s"><p>%2$s</p></div>', esc_html($class), $message);
			});
		}


		#===============================================
		public static function show_new_options_notice( $plugin, $class ) {
			if( !current_user_can('manage_options') ) {
				return;
			}
			$message = sprintf('<strong>%2$s: </strong>' . __('New options have been added. Please, check your <a href=%1$s>settings</a>.', $plugin['text']), $plugin['url'], $plugin['short'] );
			$message = wp_kses_allowed( $message );
			add_action('admin_notices', function () use ($class, $message) {
				printf('<div class="notice notice-%1$s"><p>%2$s</p></div>', esc_html($class), $message);
			});
		}


		#===============================================
		public static function minify_js( $input ) {
		    if(trim($input) === "") return $input;
		    return preg_replace( array('#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#', '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s', '#;+\}#', '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i', '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'), array( '$1', '$1$2', '}', '$1$3', '$1.$3'), $input);
		}


		#===============================================
		public static function minify_css( $input ) {
			$output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $input);
			$output = str_replace(': ', ':', $output);
			$output = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $output);
			$css 	= '<style>'. $output .'</style>';
			return $css;
		}


		#===============================================
		public static function require_editor( $editor ) {
	    	$editor = str_replace( '<textarea', '<textarea required="required"', $editor );
			return $editor;
		}


		#===============================================
		public static function convert_to_UTC( $input, $format = false ) {
			if( !$format ) {
				$format 	= 'Y-m-d H:i:s';
			}
			$timezone 	= get_option('timezone_string');
			$local_tz 	= new DateTimeZone( $timezone );
			$UTC 		= new DateTimeZone( 'UTC' );
			$Date		= new DateTime( date( $format, strtotime( $input ) ), $local_tz );
			$Date->setTimezone( $UTC );
			$output 	= $Date->format( $format );
			return $output;
		}


		#===============================================
		public static function convert_to_local( $input, $format = false ) {
			if( !$format ) {
				$format 	= 'Y-m-d H:i:s';
			}
			$timezone 	= get_option('timezone_string');
			$local_tz 	= new DateTimeZone( $timezone );
			$UTC 		= new DateTimeZone( 'UTC' );
			$Date		= new DateTime( date( $format, strtotime( $input ) ), $UTC );
			$Date->setTimezone( $local_tz );
			$output 	= $Date->format( $format );
			return $output;
		}

	} // End class.
}

#===============================================
if( !function_exists('wp_parse_args_recursive') ) {
	function wp_parse_args_recursive( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[$k] ) ) {
				$result[$k] = wp_parse_args_recursive( $v, $result[$k] );
			} else {
				$result[$k] = $v;
			}
		}
		return $result;
	}
}

#===============================================
if( !function_exists('wp_kses_allowed') ) {
	function wp_kses_allowed( $context ) {
		global $allowedposttags;
		return wp_kses( $context, $allowedposttags, wp_allowed_protocols() );
	}
}

#===============================================
if( !function_exists('wp_kses_some') ) {
	function wp_kses_some( $context ) {
		$allowed = array( 'br' => [], 'i' => [], 'b' => [], 'u' => [] );
		return wp_kses( $context, $allowed, array() );
	}
}

