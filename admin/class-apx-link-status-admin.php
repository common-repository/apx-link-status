<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://alignpixel.com
 * @since      1.0.0
 *
 * @package    APx_Link_Status
 * @subpackage APx_Link_Status/admin
 * @author     Align Pixel <contact@alignpixel.com>
 */
class APx_Link_Status_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	public $get_apx_link_status_setting;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $apx_links_add_meta_box_post_type = array();


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->get_apx_link_status_setting = (array)get_option('apx-link-status-setting');
		/*
		Set default setting post type
		*/
		if(empty($this->get_apx_link_status_setting['post_type'])):
			$this->get_apx_link_status_setting['post_type'] = array('post');
		endif;

		/*
		Set default setting for post type meta meta
		*/
		foreach($this->get_apx_link_status_setting['post_type'] as $get_apx_link_status_setting_key => $get_apx_link_status_setting_val):
			$this->apx_links_add_meta_box_post_type[] = $get_apx_link_status_setting_key;
		endforeach;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in APx_Link_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The APx_Link_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/apx-style-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Custom function for remove unselected media from link
	 *
	 * @since    1.0.0  
	 */
	private function remove_unselected_media_from_link($link) {
		$apx_media = array('gif','jpg','jpeg','png','mp4','pdf','doc','docx');
		$get_apx_link_status_setting = (array)get_option('apx-link-status-setting');

		if(empty($get_apx_link_status_setting['media'])):
			$get_apx_link_status_setting['media'] = array();
		endif;

		foreach($get_apx_link_status_setting['media'] as $get_apx_link_status_setting_media_key => $get_apx_link_status_setting_media_val):
			if($get_apx_link_status_setting_media_key == 'image'):
				$apx_media = array_diff($apx_media, array('gif','jpg','jpeg','png'));
			endif;
			if($get_apx_link_status_setting_media_key == 'video'):
				$apx_media = array_diff($apx_media, array('mp4','m4p','mpg','3gp','webm','flv','ogv','ogg','avi','wmv','mov'));
			endif;
			if($get_apx_link_status_setting_media_key == 'pdf'):
				$apx_media = array_diff($apx_media, array('pdf'));
			endif;
			if($get_apx_link_status_setting_media_key == 'doc'):
				$apx_media = array_diff($apx_media, array('doc','docx','pages','xls','xlsx','ppt','pptx'));
			endif;
		endforeach;

		// Using strtolower to overcome case sensitive
		$ext = strtolower(pathinfo($link, PATHINFO_EXTENSION)); 
		if (in_array($ext, $apx_media)):
		    return 'yes';
		endif;
	}


	/**
	 * Link Status for the post ---- Main Function
	 *
	 * @since    1.0.0 
	 */
	private function all_links_in_post_content($post,$linkinfo = false){
		$i = $external = $internal = 0;
		$link_details = array();

		preg_match_all("/(<a.[^>]*>)(.[^<]*)(<\/a>)/ismU",$post,$matches,PREG_SET_ORDER);
		$count = count($matches);

		foreach($matches as $key => $value):
			preg_match("/href\s*=\s*[\'|\"]\s*([^\"|\']*)\s*[\'|\"]/i",$value[1],$href);

			$clear_link_suffix = str_replace(array('https://','http://','www.'), '', $href[1]);
			$site_link = str_replace(array('https://','http://','www.'), '', get_bloginfo("url"));

			if($this->remove_unselected_media_from_link($clear_link_suffix) == 'yes'):
				$count--;
				continue;
			endif;

			if(substr($clear_link_suffix,0,strlen($site_link)) == $site_link ):
		        $internal++;
		        if($linkinfo):
		        	$link_details['details'][$i]['link_type'] = 'Internal';
		       	endif;
		    else:
		        $external++;
		        if($linkinfo):
		        	$link_details['details'][$i]['link_type'] = 'External';
		        endif;
		    endif;

		    if($linkinfo):
				$link_details['details'][$i]['anchor'] = $value[2];
				$link_details['details'][$i]['link'] = $href[1];
				$i++;
			endif;
		endforeach;

		$link_details['info']['total'] = $count;
		$link_details['info']['internal'] = $internal;
		$link_details['info']['external'] = $external;

		return $link_details;
	}

	/*
	Callback from meta for link status 
	*/
	public function link_status_meta_display( $post) {
		$post_content = $post->post_content;

		//Get all links information array
		$link_details = $this->all_links_in_post_content($post_content, true);

		//wc_get_templat
		include( plugin_dir_path( __FILE__ ) . 'partials/meta-display.php' );
		
	}

	/*
	Custom meta box for Link Status
	*/
	public function apx_links_add_meta_box() {
		add_meta_box(
			'apx_link_status_meta',
			__( 'APX Link Status Info', 'apx-link-status' ),
			array( $this, 'link_status_meta_display'),
			$this->apx_links_add_meta_box_post_type,
			'normal',
			'default'
		);
	}

	/*
	Callback for admin column title 
	*/
	public function apx_link_show_on_column($columns){
	    $columns['all_links'] 		= '<span><span class="apx-all-links" title="'.esc_html__('All Links','apx-link-status').'"><span class="screen-reader-text">'.esc_html__('All Links','apx-link-status').'</span></span></span>';//esc_html__('All Links','apx-link-status');
	    $columns['internal_links'] 	= '<span><span class="apx-internal-links" title="'.esc_html__('Internal Links','apx-link-status').'"><span class="screen-reader-text">'.esc_html__('Internal Links','apx-link-status').'</span></span></span>';//esc_html__('Internal','apx-link-status');
	    $columns['external_links'] 	= '<span><span class="apx-external-links" title="'.esc_html__('External Links','apx-link-status').'"><span class="screen-reader-text">'.esc_html__('External Links','apx-link-status').'</span></span></span>';//esc_html__('External','apx-link-status');

	    return $columns;
	}

	/*
	Callback for admin column value
	*/
	public function apx_link_show_on_column_result($column_name, $id){
		$post = get_post_field('post_content', $id);
		$link_details = $this->all_links_in_post_content($post);

	    if($column_name === 'all_links'):
	        echo esc_html($link_details['info']['total']);
	    endif;
	    if($column_name === 'internal_links'):
	        echo esc_html($link_details['info']['internal']);
	    endif;
	    if($column_name === 'external_links'):
	        echo esc_html($link_details['info']['external']);
	    endif;
	}

	public function apx_link_status_menu(){
		add_options_page( __('APX Link Status','apx-link-status'), __('APX Link Status','apx-link-status'), 'manage_options', 'apx-link-status', array($this,'apx_link_status_options_page') );		
	}

	public function apx_link_status_options_page(){
		include( plugin_dir_path( __FILE__ ) . 'partials/options-main-wrap.php' );
	}

	public function apx_link_status_setting_init(){
		register_setting( 'apx-link-status-setting-group', 'apx-link-status-setting' );
		add_settings_section( 'section-main', __('Main Settings','apx-link-status'), array($this,'main_setting_callback'), 'apx-link-status-section' );
		add_settings_field( 'apx-post-type', __('Select Post Type','apx-link-status'), array($this,'redirect_mobile_callback'), 'apx-link-status-section', 'section-main' );
		add_settings_field( 'apx-media-link', __('Add Image link','apx-link-status'), array($this,'apx_add_media_link_callback'), 'apx-link-status-section', 'section-main' );
	}

	public function main_setting_callback(){
		esc_html_e('Select post type where you want to show the APX Link Status. By default, all media link are excluded, if you want to show the media link please select media type.','apx-link-status');

		//$setting = (array)get_option('apx-link-status-setting');
		//print_r($setting);
		//print_r($setting['post_type']);
	}

	public function redirect_mobile_callback(){
		$setting = (array)get_option('apx-link-status-setting');
		$post_types = get_post_types(array('public'   => true));

		include( plugin_dir_path( __FILE__ ) . 'partials/options-post-type-fields.php' );

		
	}

	public function apx_add_media_link_callback(){
		$setting = (array)get_option('apx-link-status-setting');
		$media_arr = array('image'=> 'Image (png, jpg, jpeg, gif)','video'=>'Video (mp4, m4p, mpg, 3gp, webm, flv, ogv, ogg, avi, wmv, mov)','pdf'=>'PDF','doc'=>'Document (doc, docx, pages, xls, xlsx, ppt, pptx)'); 

		include( plugin_dir_path( __FILE__ ) . 'partials/options-media-link-fields.php' );

		
	}

}
