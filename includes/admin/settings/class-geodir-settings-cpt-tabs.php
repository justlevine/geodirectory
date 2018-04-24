<?php
/**
 * GeoDirectory CPT Sorting Settings
 *
 * @author      AyeCode
 * @category    Admin
 * @package     GeoDirectory/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'GeoDir_Settings_Cpt_Tabs', false ) ) :

	/**
	 * GeoDir_Admin_Settings_General.
	 */
	class GeoDir_Settings_Cpt_Tabs extends GeoDir_Settings_Page {

		/**
		 * Post type.
		 *
		 * @var string
		 */
		private static $post_type = '';

		/**
		 * Sub tab.
		 *
		 * @var string
		 */
		private static $sub_tab = '';

		/**
		 * Constructor.
		 */
		public function __construct() {

			self::$post_type = ! empty( $_REQUEST['post_type'] ) ? sanitize_title( $_REQUEST['post_type'] ) : 'gd_place';
			self::$sub_tab   = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'general';


			$this->id    = 'cpt-tabs';
			$this->label = __( 'Tabs Layout', 'geodirectory' );

			add_filter( 'geodir_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'geodir_settings_' . $this->id, array( $this, 'output' ) );

			add_action( 'geodir_manage_tabs_available_fields', array( $this, 'output_standard_fields' ) );
			add_action( 'geodir_manage_tabs_available_fields_predefined', array( $this, 'output_predefined_fields' ) );
			add_action( 'geodir_manage_tabs_available_fields_custom', array( $this, 'output_custom_fields' ) );


		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {

			$sections = array(
				//'' => __( 'Custom Fields', 'geodirectory' ),
				//	'location'       => __( 'Custom fields', 'geodirectory' ),
				//	'pages' 	=> __( 'Sorting options', 'geodirectory' ),
				//'dummy_data' 	=> __( 'Dummy Data', 'geodirectory' ),
				//'uninstall' 	=> __( 'Uninstall', 'geodirectory' ),
			);

			return apply_filters( 'geodir_get_sections_' . $this->id, $sections );
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $hide_save_button;

			$hide_save_button = true;

			$listing_type = self::$post_type;

			$sub_tab = self::$sub_tab;

			include( dirname( __FILE__ ) . '/../views/html-admin-settings-cpt-cf.php' );


		}


		/**
		 * Returns heading for the CPT settings left panel.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The page heading.
		 */
		public static function left_panel_title() {
			return sprintf( __( 'Fields', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );

		}

		/**
		 * Returns description for given sub tab - available fields box.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The box description.
		 */
		public function left_panel_note() {
			return sprintf( __( 'Fields that can be added to the %s tabs.', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );
		}

		/**
		 * Output the admin settings cpt sorting left panel content.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 */
		public function left_panel_content() {
			?>
			<h3><?php _e( 'Standard Fields', 'geodirectory' ); ?></h3>

			<div class="inside">

				<div id="gd-form-builder-tab" class="gd-form-builder-tab gd-tabs-panel">

					<?php
					/**
					 * Adds the available fields to the custom fields settings page per post type.
					 *
					 * @since 1.0.0
					 *
					 * @param string $sub_tab The current settings tab name.
					 */
					do_action( 'geodir_manage_tabs_available_fields', self::$sub_tab ); ?>

					<div style="clear:both"></div>
				</div>

			</div>

			<h3><?php _e( 'Predefined Fields', 'geodirectory' ); ?></h3>
			<div class="inside">

				<div id="gd-form-builder-tab-predefined" class="gd-form-builder-tab gd-tabs-panel">

					<?php
					/**
					 * Adds the available fields to the custom fields predefined settings page per post type.
					 *
					 * @since 1.6.9
					 *
					 * @param string $sub_tab The current settings tab name.
					 */
					do_action( 'geodir_manage_tabs_available_fields_predefined', self::$sub_tab ); ?>

					<div style="clear:both"></div>
				</div>

			</div>

			<h3><?php _e( 'Custom Fields', 'geodirectory' ); ?></h3>
			<div class="inside">

				<div id="gd-form-builder-tab" class="gd-tabs-panel">

					<?php
					/**
					 * Adds the available fields to the custom fields custom added settings page per post type.
					 *
					 * @since 1.6.9
					 *
					 * @param string $sub_tab The current settings tab name.
					 */
					do_action( 'geodir_manage_tabs_available_fields_custom', self::$sub_tab ); ?>

					<div style="clear:both"></div>
				</div>

			</div>
			<?php
		}


		/**
		 * Returns heading for the CPT settings left panel.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The page heading.
		 */
		public static function right_panel_title() {
			return sprintf( __( '%s Tabs', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );
		}

		/**
		 * Returns description for given sub tab - available fields box.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The box description.
		 */
		public function right_panel_note() {
			return sprintf( __( 'Drag and drop the items to create the %s tabs.', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );
		}

		public static function get_tabs_fields($post_type,$type='post'){
			global $wpdb;
			return $wpdb->get_results($wpdb->prepare("SELECT * FROM ".GEODIR_TABS_LAYOUT_TABLE." WHERE post_type=%s AND tab_layout=%s ORDER BY sort_order ASC",$post_type,$type));

		}
		/**
		 * Output the admin cpt settings fields left panel content.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 */
		public function right_panel_content() {

			$type = "post";
			$post_type = self::$post_type;
			global $wpdb;
			$tabs = self::get_tabs_fields($post_type,$type);
			//print_r($tabs);
			?>
			<form></form> <!-- chrome removes the first form inside a form for some reason so we need this ?> -->
			<div class="inside">

				<div id="gd-form-builder-tab" class="gd-form-builder-tab gd-tabs-panel">
					<div class="field_row_main">
						<div class="dd gd-tabs-layout" >
							
							<?php


							echo '<ul class="dd-list gd-tabs-sortable">';

							if ( ! empty( $tabs ) ) {

								echo self::loop_tabs_output($tabs);

							} else {
								_e( 'No tab items have been added yet.', 'geodirectory' );
							}
							echo '</ul>';

							?>

						</div>
					</div>
				</div>
			</div>
					
			<?php
		}


		/**
		 * Loop through the base to output them with the different levels.
		 * @param $tabs
		 * @param bool $sub
		 */
		public static function loop_tabs_output($tabs,$tab_id = ''){
			ob_start();

			if(!empty($tabs)){
				foreach($tabs as $key => $tab){
					
					if($tab_id && $tab->id!=$tab_id){
						continue;
					}elseif($tab_id && $tab->id==$tab_id && $tab->tab_level > 0){
						echo self::get_tab_item($tab); break;
					}

					if($tab->tab_level=='1' ){continue;}


					$tab_rendered = self::get_tab_item($tab);
					$tab_rendered = str_replace("</li>","",$tab_rendered);
					$child_tabs = '';
					foreach($tabs as $child_tab){
						if($child_tab->tab_parent==$tab->id){
							$child_tabs .= self::get_tab_item($child_tab);
						}
					}

					if($child_tabs){
						$tab_rendered .= "<ul>";
						$tab_rendered .= $child_tabs;
						$tab_rendered .= "</ul>";
					}

					echo $tab_rendered;
					echo "</li>";

					unset($tabs[$key]);

				}
			}
			return ob_get_clean();
		}

		public static function loop_tabs_output_delete_me($tabs,$sub = false){
			if(!empty($tabs)){
				foreach($tabs as $key => $tab){
					$tab_rendered = self::get_tab_item($tab);

					if($sub && $tab->tab_level=='1'){
						echo $tab_rendered;
					}elseif($tab->tab_level=='0'){
						if($sub){break;}
						$tab_rendered = str_replace("</li>","",$tab_rendered);
						echo $tab_rendered;
						unset($tabs[$key]);
						echo "<ul>";
						self::loop_tabs_output($tabs,true);
						echo "</ul>";
						echo "</li>";
					}
					unset($tabs[$key]);

				}
			}
		}

		/**
		 * Check if the field already exists.
		 *
		 * @param $field
		 *
		 * @return WP_Error
		 */
		public static function field_exists( $htmlvar_name, $post_type ) {
			global $wpdb;

			$check_html_variable = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT htmlvar_name FROM " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " WHERE htmlvar_name = %s AND post_type = %s",
					array( $htmlvar_name, $post_type )
				)
			);

			return $check_html_variable;

		}


		/**
		 * Output the tab fields to be selected.
		 *
		 * @param $cfs
		 */
		public function output_fields($cfs) {
			if ( ! empty( $cfs ) ) {
				echo '<ul>';
				foreach ( $cfs as $id => $cf ) {
					$cf = (array)$cf;
					?>
					<li>
						<a id="gd-<?php echo esc_attr($cf['tab_key']); ?>"
						   class="gd-draggable-form-items gd-fieldset"
						   href="javascript:void(0);"
						   data-tab_layout="post"
						   data-tab_type="<?php echo esc_attr($cf['tab_type']); ?>"
						   data-tab_name="<?php echo esc_attr($cf['tab_name']); ?>"
						   data-tab_icon="<?php echo esc_attr($cf['tab_icon']); ?>"
						   data-tab_key="<?php echo esc_attr($cf['tab_key']); ?>"
						   data-tab_content="<?php echo esc_attr($cf['tab_content']); ?>"
						   onclick="gd_tabs_add_tab(this);return false;">

							<i class="fa <?php echo esc_attr($cf['tab_icon']); ?>" aria-hidden="true"></i>
							<?php echo esc_attr($cf['tab_name']); ?>

							<!--							<span class="gd-help-tip gd-help-tip-no-margin dashicons dashicons-editor-help" title="--><?php //_e( 'This adds a section separator with a title.', 'geodirectory' );?><!--"></span>-->
						</a>
					</li>
					<?php
				}
				echo '</ul>';
			} else {
				_e( 'There are no custom fields here yet.', 'geodirectory' );
			}
		}

		/**
		 * Adds admin html for custom fields available fields.
		 *
		 * @since 1.0.0
		 * @since 1.6.9 Added
		 *
		 * @param string $type The custom field type, predefined, custom or blank for default
		 *
		 * @package GeoDirectory
		 */
		public function output_standard_fields() {
			$cfs = self::get_standard_fields();
			self::output_fields($cfs);

		}

		public function get_standard_fields(){
			global $wpdb;
			$fields = array();


			// shortcode
			$fields[] = array(
				'tab_type'   => 'fieldset',
				'tab_name'   => __('Fieldset','geodirectory'),
				'tab_icon'   => 'fa-minus',
				'tab_key'    => '',
				'tab_content'=> ''
			);

			$table = GEODIR_CUSTOM_FIELDS_TABLE;
			$cfs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE post_type=%s",self::$post_type));
			if(!empty($cfs)){
				foreach($cfs as $cf){
					$cf = (array)$cf;
					$fields[] = array(
						'tab_type'   => 'meta',
						'tab_name'   => !empty($cf['admin_title']) ? esc_attr($cf['admin_title']) : esc_attr($cf['frontend_title']),
						'tab_icon'   => isset($cf['field_icon']) && $cf['field_icon'] ? $cf['field_icon'] : "fa-cog",
						'tab_key'    => esc_attr($cf['htmlvar_name']),
						'tab_content'=> ''
					);
				}

			}


			return $fields;
		}

		/**
		 * Adds admin html for custom fields available fields.
		 *
		 * @since 1.0.0
		 * @since 1.6.9 Added
		 *
		 * @param string $type The custom field type, predefined, custom or blank for default
		 *
		 * @package GeoDirectory
		 */
		public function output_predefined_fields() {
			$cfs = self::get_predefined_fields();
			self::output_fields($cfs);
		}

		public function get_predefined_fields(){
			$fields = array();

			// Reviews
			$fields[] = array(
				'tab_type'   => 'standard',
				'tab_name'   => __('Reviews','geodirectory'),
				'tab_icon'   => 'fa-comments',
				'tab_key'    => 'reviews',
				'tab_content'=> ''
			);

			// Map
			$fields[] = array(
				'tab_type'   => 'standard',
				'tab_name'   => __('Map','geodirectory'),
				'tab_icon'   => 'fa-globe',
				'tab_key'    => 'post_map',
				'tab_content'=> '[gd_map width="100%" height="425px" maptype="ROADMAP" zoom="0" map_type="post" map_directions="1"]'
			);

			// Photos
			$fields[] = array(
				'tab_type'   => 'standard',
				'tab_name'   => __('Photos','geodirectory'),
				'tab_icon'   => 'fa-picture-o',
				'tab_key'    => 'post_images',
				'tab_content'=> '[gd_post_images type="gallery" ajax_load="1" slideshow="1" show_title="1" animation="slide" controlnav="1" link_to="lightbox"]'
			);

			return $fields;
		}

		/**
		 * Adds admin html for custom fields available fields.
		 *
		 * @since 1.0.0
		 * @since 1.6.9 Added
		 *
		 * @param string $type The custom field type, predefined, custom or blank for default
		 *
		 * @package GeoDirectory
		 */
		public function output_custom_fields() {

			// insert the required code for the SD button.
			$js_insert_function = self::insert_shortcode_function();
			WP_Super_Duper::shortcode_insert_button('',$js_insert_function);


			$cfs = self::get_custom_fields();
			self::output_fields($cfs);
		}

		public function insert_shortcode_function(){
			ob_start();
			?>
			function sd_insert_shortcode(){
				$shortcode = jQuery('#sd-shortcode-output').val();
				if($shortcode){
					jQuery('.gd-tab-settings-open textarea').val($shortcode);
					tb_remove();
				}
			}
			<?php
			return ob_get_clean();

		}

		public function get_custom_fields(){
			$fields = array();

			// shortcode
			$fields[] = array(
				'tab_type'   => 'shortcode',
				'tab_name'   => __('Shortcode','geodirectory'),
				'tab_icon'   => 'fa-cubes',
				'tab_key'    => '',
				'tab_content'=> ''
				
			);

			return $fields;
		}

		/**
		 * Output the tab item.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 */
		public static function get_tab_item($tab) {
			ob_start();
			$tab = (object)$tab;
			include( dirname( __FILE__ ) . '/../views/html-admin-settings-cpt-tab-item.php' );
			return ob_get_clean();
		}

		/**
		 * Savethe tab item.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 */
		public static function save_tab_item($tab) {
			global $wpdb;
			$tab = (object)$tab;

			$tab_id = isset($tab->id) && $tab->id ? absint($tab->id) : '';
			$table = GEODIR_TABS_LAYOUT_TABLE;

			// set the tab key
			$tab->tab_key = !empty($tab->tab_key) ? $tab->tab_key : sanitize_title( $tab->tab_name,'tab-'.$tab->tab_icon  );
			$postarr = array(
				'post_type'     => $tab->post_type,
				'tab_layout'    => $tab->tab_layout,
				'tab_type'      => $tab->tab_type,
				'tab_name'      => $tab->tab_name,
				'tab_icon'      => $tab->tab_icon,
				'tab_key'       => $tab->tab_key,
				'tab_content'   => $tab->tab_content,
			);

			if(isset($tab->sort_order)){
				$postarr['sort_order'] = $tab->sort_order;
			}
			if(isset($tab->tab_level)){
				$postarr['tab_level'] = $tab->tab_level;
			}

			$format = array_fill( 0, count( $postarr ), '%s' );

			if ( $tab_id ) {// update
				$result = $wpdb->update(
					$table,
					$postarr,
					array( 'id' => $tab_id ),
					$format
				);
			} else { // insert
				$result = $wpdb->insert(
					$table,
					$postarr,
					$format
				);
				$tab->id = $wpdb->insert_id;
			}

			if(false === $result){
				return new WP_Error( 'failed', __("Something went wrong!","geodirectory") );
			}else{
				$tabs = self::get_tabs_fields($tab->post_type,$tab->tab_layout);

				//print_r($tabs);
				//echo $tab->post_type.'###'.$tab->tab_layout;
				return self::loop_tabs_output($tabs,$tab->id);
				//return self::get_tab_item($tab);
			}


		}

		/**
		 * Set the tabs order and count level.
		 */
		public static function set_tabs_orders($tabs = array()){
			global $wpdb;

//			print_r($tabs);
			$count = 0;
			if (!empty($tabs)) {
				$result = false;
				foreach ( $tabs as $index => $info ) {
					$result = $wpdb->update(
						GEODIR_TABS_LAYOUT_TABLE,
						array('sort_order' => $index,'tab_level' => $info['tab_level'],'tab_parent' => $info['tab_parent']),
						array('id' => absint($info['id'])),
						array('%d','%d','%d')
					);
					$count ++;
				}
				if($result !== false){
					return true;
				}else{
					return new WP_Error( 'failed', __( "Failed to sort tab items.", "geodirectory" ) );
				}
			}else{
				return new WP_Error( 'failed', __( "Failed to sort tab items.", "geodirectory" ) );
			}
		}

		/**
		 * Delete a tab.
		 */
		public static function delete_tab($tab_id,$post_type = ''){
			global $wpdb;
			if (!empty($tab_id)) {
				$where = array('id'=>$tab_id);
				$format = array( '%d' );
				if($post_type){
					$where['post_type'] = $post_type;
					$format[] = "%s";
				}
				$result = $wpdb->delete( GEODIR_TABS_LAYOUT_TABLE, $where, $format );
				if($result !== false){
					self::delete_tab_children($tab_id,$post_type);
					return true;
				}else{
					return new WP_Error( 'failed', __( "Failed to delete tab item.", "geodirectory" ) );
				}
			}else{
				return new WP_Error( 'failed', __( "Failed to delete tab item.", "geodirectory" ) );
			}
		}

		/**
		 * Delete a tabs children.
		 */
		public static function delete_tab_children($tab_id,$post_type = ''){
			global $wpdb;

			if (!empty($tab_id)) {
				if($post_type ){
					$children = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".GEODIR_TABS_LAYOUT_TABLE." WHERE tab_parent=%d AND post_type=%s",$tab_id,$post_type) );
				}else{
					$children = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".GEODIR_TABS_LAYOUT_TABLE." WHERE tab_parent=%d",$tab_id) );
				}
				if($children ){
					foreach($children as $child){
						$where = array('id'=>$child->id);
						$format = array( '%d' );
						if($post_type){
							$where['post_type'] = $post_type;
							$format[] = "%s";
						}
						$wpdb->delete( GEODIR_TABS_LAYOUT_TABLE, $where, $format );
					}
				}
			}
		}


	}

endif;

return new GeoDir_Settings_Cpt_Tabs();
