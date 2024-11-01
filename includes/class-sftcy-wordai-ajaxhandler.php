<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordai_Ajaxhandler' ) ) {
class SFTCY_Wordai_Ajaxhandler {	
	private static $initiated             	  = false;		
	public static $output					  = [];	
				
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}								
	}
	
	private static function initiate_hooks() {		
		  // Enqueue scripts
		  add_action('admin_enqueue_scripts', array( __CLASS__, 'admin_required_scripts') );			 
		  // API settings
		  add_action('wp_ajax_sc_wordai_api_test', [ __CLASS__, 'sc_wordai_api_test' ] );	
		  add_action('wp_ajax_sc_wordai_apisettings_data', [ __CLASS__, 'sc_wordai_apisettings_data' ] );	
		  add_action('wp_ajax_wordai_api_key_show', [ __CLASS__, 'wordai_api_key_show' ] );	
		  add_action('wp_ajax_wordai_api_key_data_save', [ __CLASS__, 'wordai_api_key_data_save' ] );	
		  add_action('wp_ajax_sc_wordai_apisettings_reset_data', [ __CLASS__, 'sc_wordai_apisettings_reset_data' ] );	
		  add_action('wp_ajax_sc_wordai_content_settings_data', [ __CLASS__, 'sc_wordai_content_settings_data' ] );	 
		  add_action('wp_ajax_sc_wordai_content_settings_reset_data', [ __CLASS__, 'sc_wordai_content_settings_reset_data' ] );	 
		  add_action('wp_ajax_sc_wordai_image_settings_data', [ __CLASS__, 'sc_wordai_image_settings_data' ] );	
		  add_action('wp_ajax_sc_wordai_image_settings_reset_data', [ __CLASS__, 'sc_wordai_image_settings_reset_data' ] );			
		  // Content Writing
		  add_action( 'wp_ajax_sc_wordai_write_titles', [ __CLASS__, 'sc_wordai_write_titles'] );		  				
		  add_action( 'wp_ajax_sc_wordai_write_suggest_titles', [ __CLASS__, 'sc_wordai_write_suggest_titles'] );				  				  		  		  
		  add_action( 'wp_ajax_sc_wordai_update_suggest_title', [ __CLASS__, 'sc_wordai_update_suggest_title'] );			  				  		 				 
		  add_action( 'wp_ajax_sc_wordai_write_content', [ __CLASS__, 'sc_wordai_write_content'] );
		  add_action( 'wp_ajax_sc_wordai_write_excerpt', [ __CLASS__, 'sc_wordai_write_excerpt'] );
		  add_action( 'wp_ajax_sc_wordai_write_tags', [ __CLASS__, 'sc_wordai_write_tags'] ); 
		  add_action('wp_ajax_sc_wordai_save_tags', [ __CLASS__, 'sc_wordai_save_tags' ] );	
		  add_action( 'wp_ajax_sc_wordai_generate_image', [ __CLASS__, 'sc_wordai_generate_image'] );
		  add_action( 'wp_ajax_sc_wordai_upload_image_to_wp_media', [ __CLASS__, 'sc_wordai_upload_image_to_wp_media'] );		
		  add_action( 'wp_ajax_sc_wordai_suggested_title_number_save', [ __CLASS__, 'sc_wordai_suggested_title_number_save'] );		 		  		
		  add_filter('plupload_default_settings', array( __CLASS__, 'sc_wordai_image_upload_mime_type_issue'), 10 , 1 ); 		 		  
		 		    		
		  self::$initiated = true;
	}	
		
	/**
	 * Enqueue Scripts
	 * @since 1.0.0 
	 */
	public static function admin_required_scripts( $page_name_hook ) {				
		// get current admin screen
		global $pagenow;
		$screen 		= get_current_screen();				
		// Loaded only on Plugin required admin pages
		$allowed_pages	=	[ 'toplevel_page_word-ai-topmenu', 'wordai_page_sc-wordai-content-settings', 'wordai_page_sc-wordai-image-settings' ];
		if ( in_array( $screen->id, $allowed_pages) || in_array( $screen->post_type, [ 'post', 'page', 'product' ] ) ) {			    
				
			    wp_enqueue_style('sftcy-wordai-style', plugins_url( '../admin/css/sc-wordai-misc-styles.css', __FILE__ ) , array(), SFTCY_WORDAI_VERSION, 'all' );		
		        wp_enqueue_style('sftcy-wordai-jquery-ui-style', plugins_url('../admin/css/jquery-ui.css', __FILE__ ) , array(), SFTCY_WORDAI_VERSION, 'all' );
			    wp_enqueue_style('sftcy-wordai-fontawesome', plugins_url('../admin/css/fontawesome6-all.min.css', __FILE__ ) , array(), SFTCY_WORDAI_VERSION, 'all' );
		
				wp_enqueue_script('sftcy-wordai-misc-script', plugins_url( '../admin/js/sc-wordai-misc-script.js', __FILE__ ) , array('jquery', 'jquery-ui-dialog', 'jquery-ui-core' ), SFTCY_WORDAI_VERSION, true);		
				wp_enqueue_script('sftcy-wordai-content-generate-script', plugins_url( '../admin/js/sc-wordai-content-generate-script.js', __FILE__ ) , array('jquery', 'jquery-ui-dialog', 'jquery-ui-core' ), SFTCY_WORDAI_VERSION, true);						        				
			    wp_enqueue_script('sftcy-wordai-admin-view-script', plugins_url( '../admin/js/wordai-admin-view-script.js', __FILE__ ), array('jquery', 'sftcy-wordai-misc-script', 'sftcy-wordai-content-generate-script' ), SFTCY_WORDAI_VERSION, true);
				
		        $nonce = wp_create_nonce( 'scwordai_wpnonce' );
				$data  = array(
						'adminajax_url'                  => admin_url('admin-ajax.php'),
						'nonce'                          => $nonce, 
						'current_screenid'               => $screen->id,
						'current_posttype'               => $screen->post_type,
						'current_pagenow'                => $pagenow,			
					
						'copy_title'         			 => __( 'Copy Title', 'wordai'),
						'insert_title'          		 => __( 'Insert Title', 'wordai'),
						'copy_content'         			 => __( 'Copy Content', 'wordai'),
					    'copy_excerpt'         			 => __( 'Copy Excerpt', 'wordai'),
					    'copy_tags'         			 => __( 'Copy Tags', 'wordai'),
					    'insert_content'				 => __( 'Insert Content', 'wordai'),
					    'insert_excerpt'				 => __( 'Insert Excerpt', 'wordai'),
					    'insert_tags'				 	 => __( 'Insert Tags', 'wordai'),
					    'copied'                		 => __( 'Copied', 'wordai'),
					    'inserted'                		 => __( 'Inserted', 'wordai'), 
					
					    'write_ur_prompt'               => __( 'Please write your prompt!', 'wordai'),					 					
					    'generated_title_success'		=> __( 'Generated Title(s) Successfully!', 'wordai'), 
					    'generated_content_success'		=> __( 'Generated Content Successfully!', 'wordai'), 
					    'generated_excerpt_success'		=> __( 'Generated Excerpt Successfully!', 'wordai'), 
					    'generated_tags_success'		=> __( 'Generated Tags Successfully!', 'wordai'), 
					    'check_ur_generate_options'     => __( 'Check any option [ Title / Content / Images ] To Generate!', 'wordai'), 
					
					    'something_went_wrong'			   => __( 'Something went wrong!','wordai'),
					    'something_went_wrong_title'	   => __( 'Title Generation: Something went wrong!','wordai'),
					    'something_went_wrong_content'	   => __( 'Content Generation: Something went wrong!','wordai'),
					    'something_went_wrong_images'	   => __( 'Image(s) Generation: Something went wrong!','wordai'),
					    'something_went_wrong_images_save' => __( 'Image(s) Save: Something went wrong!','wordai'),
					    'suggest_titles_btn_text'		   => __( 'Suggest Title(s)','wordai'),
					    'cancel_btn_text'				   => __( 'Cancel','wordai'), 
					
					    'updated_title_success'			=> __( 'Updated Title Successfully!','wordai'),
					    'updated_title_not_selected'	=> __( 'Please select the Title first!','wordai'),
					    'generated_image_success'		=> __( 'Generated Image(s) Successfully!','wordai'),	
					    'images_saved_to_gallery'		=> __( 'Image(s) saved to Gallery Successfully!','wordai'),
					    
					    'saved_apisetting_success'		=> __( 'Saved OpenAI API settings successfully!','wordai'),
					    'saved_apikey_success'		    => __( 'Saved OpenAI API Key successfully!','wordai'),
					    'nothing_changes'				=> __( 'Nothing changes!','wordai'),
					    'failed_to_save'				=> __( 'Failed to save, try again!','wordai'),
					
					    'saved_content_setting_success'	=> __( 'Saved OpenAI content settings successfully!','wordai'),
					    'saved_image_setting_success'	=> __( 'Saved OpenAI image settings successfully!','wordai'), 	
					
					    'popup_dialog_suggest_title'	=> __( 'WordAI Suggest Titles','wordai'), 
					    'metabox_popup_dialog_title'	=> __( 'WordAI - AI Content Generator','wordai'), 
					
					    
					    //'sc_wordai_icon'				=> '<span class="sc-wordai-icon dashicons dashicons-welcome-write-blog"></span>',	
					    'sc_wordai_icon'				=> '<span class="sc-wordai-metabox-popup-window-title-icon"></span>',	
					    'wordai_success_icon'           => '<i class="fa-regular fa-circle-check"></i>',
					    'wordai_info_icon'              => '<i class="fa-solid fa-circle-info"></i>',
					    'wordai_error_icon'             => '<i class="fa-regular fa-circle-xmark"></i>',
					    
						'lazy_loadimage'    			=> ''
					);
				
				// Localize script				
				wp_localize_script( 'sftcy-wordai-misc-script', 'sftcy_wordai_metabox_script_obj', $data );
		        wp_localize_script( 'sftcy-wordai-content-generate-script', 'sftcy_wordai_content_generate_script_obj', $data );
			    wp_localize_script( 'sftcy-wordai-admin-view-script', 'sftcy_wordai_admin_view_script_obj', $data );
		}
	}
				
	/**
	 * API test
	 */
	public static function sc_wordai_api_test() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );
		// echo 'Hello';
		// var_dump( SFTCY_Wordai_OpenAI::get_list_models() );		
		$response	=	 SFTCY_Wordai_OpenAI::create_content();
		var_dump( $response["choices"][0]["text"] );
		var_dump( $response );		
		// $response	=	 SFTCY_Wordai_OpenAI::create_image();		
	    // var_dump( $response );
		// var_dump( $response['data'][0] );		
		// foreach ( $response['data'] as $image ) {
		//	 var_dump( $image['url'] );
		// }
		wp_die();
	}
	
	/**
	 * Add Tags
	 * return - Json
	 */
	public static function sc_wordai_save_tags() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );		
		self::$output				= [];		
		$tags_name_comma_separated	= isset( $_POST['params']['tags'] )? sanitize_text_field( $_POST['params']['tags'] ) : '';	
		$post_id					= isset( $_POST['params']['postID'] )? sanitize_text_field( $_POST['params']['postID'] ) : 0;			
		$update_tags_ids			= wp_set_post_tags( $post_id, $tags_name_comma_separated );		
		if ( $update_tags_ids ) {
			self::$output['status']	= 'success';
			self::$output['postID']	= $post_id;
			self::$output['tags']	= $tags_name_comma_separated;
			self::$output['tagIDs']	= $update_tags_ids;
		}
		else {
			self::$output['status']	= 'fail';
			self::$output['postID']	= $post_id;
		}
		echo wp_json_encode( self::$output );		
        wp_die();								
	}
	
	
	/**
	 * Ajax Request
	 * Title number - how many title number
	 * @return - json
	 */
	public static function sc_wordai_suggested_title_number_save() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );		
		self::$output = [];
		$params = [];  
		$params['wordai-suggested-title-number'] = isset( $_POST['suggestedTitle'] )? sanitize_text_field( $_POST['suggestedTitle'] ) : 2;						
		$is_option_data_added = get_option('sftcy-wordai-content-settings-data');
		$updated_options = $is_option_data_added ? array_merge( maybe_unserialize( $is_option_data_added ), $params ) : $params;      		
		$update_data = update_option('sftcy-wordai-content-settings-data', maybe_serialize( $updated_options ) );		
		if ( $update_data ) {
			self::$output['status']	= 'success';
			self::$output['comment'] = 'Updated Suggest Title Number To: ' . $params['wordai-suggested-title-number'];
		}
		else {
			self::$output['status']	= 'warning';
			self::$output['comment'] = 'Nothing changes.';
		}
		echo wp_json_encode( self::$output );		
        wp_die();						
	}

	/**
	 * Ajax Request - Post
	 * OpenAI API Key Show - Show Key Checkbox
	 * @return - json
	 */	
	public static function wordai_api_key_show() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );		
		self::$output =	[];		
		$checked = isset( $_POST['postData'] ) ? sanitize_text_field( $_POST['postData']['checked'] ) : false;
		if ( $checked ) {
			$is_option_data_added = get_option('sftcy-wordai-apisettings-data');						
			$options_data = $is_option_data_added ? maybe_unserialize( $is_option_data_added ) : '';			
			if ( isset( $options_data['wordai-api-key-data'] ) && ! empty( $options_data['wordai-api-key-data'] ) ) {
				self::$output['status']	= 'success';
				self::$output['apiKey'] = $options_data['wordai-api-key-data'];
				self::$output['comment'] = 'API Key found.';
			}
			else {
				self::$output['status']	= 'fail';
				self::$output['feedbackMsg'] = __( 'API Key not set yet.', 'wordai' );											
			}
		}
		else {
			self::$output['status']	= 'fail';
			self::$output['feedbackMsg'] = __( 'Something went wrong.', 'wordai' );			
		}		
		echo wp_json_encode( self::$output );		
        wp_die();						
	}
	
	/**
	 * Ajax Request - Post
	 * OpenAI API Key Save	 
	 * @return - json
	 */	
	public static function wordai_api_key_data_save() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );		
		self::$output =	[];				
		$post_data = isset( $_POST['postData'] ) ? sanitize_text_field( $_POST['postData']) : '';
		parse_str( $post_data, $params );				
		$params['wordai-api-key-data'] = isset( $params['wordai-api-key-data'] ) ? sanitize_text_field( $params['wordai-api-key-data'] ) : '';	
		$data = SFTCY_Wordai::sc_wordai_get_current_api_settings();
		// When API Key already added - Clicked on Save API Key button with EMPTY value
		if ( empty( $params['wordai-api-key-data'] ) && ! empty( $data['wordai-api-key-data'] ) ) {
				self::$output['status']	= 'warning';
				self::$output['feedbackMsg'] = __( 'Nothing Changes.', 'wordai' );			
		}
		// When API Key NOT added yet - Clicked on Save API Key button with empty value
		elseif ( empty( $params['wordai-api-key-data'] ) ) {
			self::$output['status'] = 'fail';
			self::$output['feedbackMsg'] = __( 'Enter OpenAI API Key.', 'wordai' );
		}
		// When API Key NOT added yet - Clicked on Save API Key button with NEW value
		elseif ( ! empty( $params['wordai-api-key-data'] ) ) {
			$is_option_data_added = get_option('sftcy-wordai-apisettings-data');						
			$updated_options_data = $is_option_data_added ? array_merge( maybe_unserialize( $is_option_data_added ), $params ) : $params;
			$update_data = update_option('sftcy-wordai-apisettings-data', maybe_serialize( $updated_options_data ) );							
			if ( $update_data ) {
				self::$output['status']	= 'success';
				self::$output['feedbackMsg'] =  __( 'API Key saved successfully.', 'wordai' );
			}
			else if ( ! $update_data ) {
				self::$output['status']	= 'warning';
				self::$output['feedbackMsg'] = __( 'Nothing Changes.', 'wordai' );
			}
			else {
				self::$output['status']	= 'fail';
				self::$output['feedbackMsg'] = __( 'Something went wrong.', 'wordai' );
			}	
		}
		echo wp_json_encode( self::$output );		
        wp_die();						
	}
	
	/**
	 * Ajax Request
	 * OpenAI API settings parameter
	 * API settings parameter - Form submit data
	 * return - json
	 */
	public static function sc_wordai_apisettings_data() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );		
		self::$output =	[];				
		$post_data = isset( $_POST['postData'] ) ? sanitize_text_field( $_POST['postData'] ) : '';		
		parse_str( $post_data, $params );						
		array_walk( $params, function( &$value, $key ) { $value	= sanitize_text_field( $value ); });
		$params['sc-wordai-streaming']	= ( isset( $params['sc-wordai-streaming'] ) && $params['sc-wordai-streaming'] == 'on' )? 1 : 0;															
		$is_option_data_added           = get_option('sftcy-wordai-apisettings-data');
		$updated_options_data           = $is_option_data_added ? array_merge( maybe_unserialize( $is_option_data_added ), $params ) : $params;		
		$update_data					= update_option('sftcy-wordai-apisettings-data', maybe_serialize( $updated_options_data ) );				
							
		if ( $update_data ) {
			self::$output['status']		= 'success';
			self::$output['feedbackMsg'] = __( 'API Settings Parameter saved successfully.', 'wordai' );
		}
		else if ( ! $update_data ) {
			self::$output['status']		= 'warning';
			self::$output['feedbackMsg'] = __( 'Nothing Changes.', 'wordai' );			
		}
		else {
			self::$output['status']		= 'fail';
			self::$output['feedbackMsg'] = __( 'Something went wrong, try again.', 'wordai' );			
		}
		echo wp_json_encode( self::$output );		
        wp_die();				
	}
	
	/**
	 * Ajax Request
	 * OpenAI API settings parameter
	 * Reset settings parameter
	 * return - json
	 */
	public static function sc_wordai_apisettings_reset_data() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );		
		self::$output	= [];
		$params = SFTCY_Wordai::sc_wordai_api_settings_default_parameters();
		$is_option_data_added = get_option('sftcy-wordai-apisettings-data');
		$updated_options_data = $is_option_data_added ? array_merge( maybe_unserialize( $is_option_data_added ), $params ) : $params;
		$update_data = update_option('sftcy-wordai-apisettings-data', maybe_serialize( $updated_options_data ) );						
		if ( $update_data ) {
			self::$output['status']	= 'success';
			self::$output['feedbackMsg'] = __('Reset to default settings successfully.', 'wordai');
		}
		else if ( ! $update_data ) {
			self::$output['status']	= 'warning';
			self::$output['feedbackMsg'] = __('Nothing Changes, API settings already in default.', 'wordai');
		}		
		else {
			self::$output['status']	= 'fail';
			self::$output['feedbackMsg'] = __('Something went wrong, try again.', 'wordai');
		}
		echo wp_json_encode( self::$output );		
        wp_die();				
	}
	
	
	/**
	 * OpenAI API - content settings
	 * Content settings parameter - form submit data
	 * return - json
	 */
	public static function sc_wordai_content_settings_data() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );			
		self::$output = [];		
		$post_data = isset( $_POST['postData'] ) ? sanitize_text_field( $_POST['postData'] ) : '';
		parse_str( $post_data, $params );				
		array_walk( $params, function( &$value, $key ) { $value	= sanitize_text_field( $value ); });
		$params['sc-wordai-insert-title']	= ( isset( $params['sc-wordai-insert-title'] ) && $params['sc-wordai-insert-title'] == 'on' )? 1 : 0;
		$params['sc-wordai-insert-content']	= ( isset( $params['sc-wordai-insert-content'] ) && $params['sc-wordai-insert-content'] == 'on' )? 1 : 0;
		$params['sc-wordai-insert-excerpt']	= ( isset( $params['sc-wordai-insert-excerpt'] ) && $params['sc-wordai-insert-excerpt'] == 'on' )? 1 : 0;
		$params['sc-wordai-insert-tags']	= ( isset( $params['sc-wordai-insert-tags'] ) && $params['sc-wordai-insert-tags'] == 'on' )? 1 : 0;
		
		$is_option_data_added = get_option('sftcy-wordai-content-settings-data');
		$updated_options = $is_option_data_added ? array_merge( maybe_unserialize( $is_option_data_added ), $params ) : $params;      		
		$update_data = update_option('sftcy-wordai-content-settings-data', maybe_serialize( $updated_options ) );						
		if ( $update_data ) {
			self::$output['status']	= 'success';
			self::$output['feedbackMsg'] = __( 'Content settings saved successfully.', 'wordai' );
		}
		elseif ( ! $update_data ) {
			self::$output['status']	= 'warning';
			self::$output['feedbackMsg'] = __( 'Nothing Changes.', 'wordai' );			
		}
		else {
			self::$output['status']	= 'fail';
			self::$output['feedbackMsg'] = __( 'Something went wrong, try again.', 'wordai' );
		}		
		echo wp_json_encode( self::$output );		
        wp_die();						
	}
	
	/**
	 * OpenAI API - content settings
	 * Content settings parameter - Reset to default settings
	 * return - json
	 */
	public static function sc_wordai_content_settings_reset_data() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );			
		self::$output = [];
		$params = SFTCY_Wordai::sc_wordai_api_content_settings_default_parameters();
		$is_option_data_added = get_option('sftcy-wordai-content-settings-data');
		$updated_options = $is_option_data_added ? array_merge( maybe_unserialize( $is_option_data_added ), $params ) : $params;      		
		$update_data = update_option('sftcy-wordai-content-settings-data', maybe_serialize( $updated_options ) );						
		if ( $update_data ) {
			self::$output['status']	= 'success';
			self::$output['feedbackMsg'] = __('Reset to default settings successfully.', 'wordai');
		}
		elseif ( ! $update_data ) {
			self::$output['status']	= 'warning';
			self::$output['feedbackMsg'] = __('Nothing changes. No required to reset settings.', 'wordai');
		}		
		else {
			self::$output['status']	= 'fail';
			self::$output['feedbackMsg'] = __('Something went wrong,try again.', 'wordai');
		}
		echo wp_json_encode( self::$output );		
        wp_die();				
		
	}
	
	
	/**
	 * OpenAI API - image settings
	 * Image settings parameter - form submit data
	 * @return - json
	 */
	public static function sc_wordai_image_settings_data() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );			
		self::$output = [];		
		$post_data = isset( $_POST['postData'] ) ? sanitize_text_field( $_POST['postData'] ) : '';
		parse_str( $post_data, $params );	
		array_walk( $params, function( &$value, $key ) { $value = sanitize_text_field( $value ); });
		$params['sc-wordai-imagesave-togallery'] = ( isset( $params['sc-wordai-imagesave-togallery'] ) && $params['sc-wordai-imagesave-togallery'] == 'on' )? 1 : 0;
		$params['sc-wordai-dalle3-image-hd-quality'] = ( isset( $params['sc-wordai-dalle3-image-hd-quality'] ) && $params['sc-wordai-dalle3-image-hd-quality'] == 'on' )? 1 : 0;
		$params['sc-wordai-set-feature-image'] = ( isset( $params['sc-wordai-set-feature-image'] ) && $params['sc-wordai-set-feature-image'] == 'on' )? 1 : 0;		
		$update_data = update_option('sftcy-wordai-image-settings-data', maybe_serialize( $params ) );		
		if ( $update_data ) {
			self::$output['status']	= 'success';
			self::$output['feedbackMsg'] = __( 'Image settings saved successfully.', 'wordai' );
		}
		elseif ( ! $update_data ) {
			self::$output['status']	= 'warning';
			self::$output['feedbackMsg'] = __( 'Nothing Changes.', 'wordai' );
		}		
		else {
			self::$output['status']	= 'fail';
			self::$output['feedbackMsg'] = __( 'Something went wrong,try again.', 'wordai' );
		}
		echo wp_json_encode( self::$output );		
        wp_die();						
	}
	
	/**
	 * OpenAI API - image settings
	 * Image settings parameter - Reset to default settings
	 * return - json
	 */
	public static function sc_wordai_image_settings_reset_data() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );			
		self::$output = [];
		$params	= SFTCY_Wordai::sc_wordai_api_image_settings_default_parameters();					
		$update_data = update_option('sftcy-wordai-image-settings-data', maybe_serialize( $params ) );		
		if ( $update_data ) {
			self::$output['status']	= 'success';
			self::$output['feedbackMsg'] = __('Reset to default settings successfully.', 'wordai');
		}
		else if ( ! $update_data ) {
			self::$output['status']	= 'warning';
			self::$output['feedbackMsg'] = __('Nothing changes. No required to reset settings', 'wordai');
		}		
		else {
			self::$output['status']	= 'fail';
			self::$output['feedbackMsg'] = __('Something went wrong,try again.', 'wordai');
		}		
		echo wp_json_encode( self::$output );		
        wp_die();						
	}
	
	
	/**
	 * Write titles
	 * return - json
	 */
	public static function sc_wordai_write_titles() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );						
		$prompt_hints			= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : '';				
		$prompt					= SFTCY_Wordai_OpenAI::generate_prompt( trim( $prompt_hints ), 'title' );		
		$api_params				= SFTCY_Wordai_OpenAI::set_openai_params();	
		//$api_params['prompt']	= $prompt;
		$api_params['messages']	= [ array('role' => 'user', 'content' => $prompt ) ];
		
		//var_dump( $api_params );						
		
		$response				= SFTCY_Wordai_OpenAI::create_content( $api_params);		
		//var_dump( SC_Wordai_OpenAI::$output );	
		
		if ( isset( SFTCY_Wordai_OpenAI::$output['status'] ) && SFTCY_Wordai_OpenAI::$output['status'] == 'success' ) {
			// Replace \n\n with br to insert content into dynamic created core/paragraph inside properly
			SFTCY_Wordai_OpenAI::$output['responseText'] = preg_replace("/[\n\n\"]+/","", SFTCY_Wordai_OpenAI::$output['responseText'] );
		}		
		
		echo wp_json_encode( SFTCY_Wordai_OpenAI::$output );		
		wp_die();
	}
		
	
	/**
	 * Suggest titles
	 * return - json
	 */
	public static function sc_wordai_write_suggest_titles() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );						
		$prompt_hints			= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : '';				
		$prompt					= SFTCY_Wordai_OpenAI::generate_prompt( trim( $prompt_hints ), 'suggest-title' );		
		$api_params				= SFTCY_Wordai_OpenAI::set_openai_params();	
		//$api_params['prompt']	= $prompt;				
		$api_params['messages']	= [ array('role' => 'user', 'content' => $prompt ) ];				
		$response				= SFTCY_Wordai_OpenAI::create_content( $api_params);				
		
		if ( isset( SFTCY_Wordai_OpenAI::$output['status'] ) && SFTCY_Wordai_OpenAI::$output['status'] == 'success' ) {
			// var_dump( SFTCY_Wordai_OpenAI::$output['responseText'] );
			// pick the text between double quotes			
			preg_match_all('/"([^"]+)"/', SFTCY_Wordai_OpenAI::$output['responseText'], $matches );
			// var_dump($matches );
			SFTCY_Wordai_OpenAI::$output['listOfTitles'] =	'';
			$i = 1;
			if ( isset ( $matches[1] ) && array_filter( $matches[1] ) ) {				
				foreach ( $matches[1] as $title ) {					
					$radio_input_id = 'WordAISuggesTitleRadioBox-' . $i;
					$title										 = preg_replace("/[\n\n\"]+/","", $title );					
					SFTCY_Wordai_OpenAI::$output['listOfTitles']	.= '<li><label for="' . $radio_input_id . '"><input type="radio" id="' . $radio_input_id . '" class="suggested-title-radio" name="suggested-title-radio" /> '. $title .'</label></li>';
					$i++;
				}				
			}
			else {
				$radio_input_id = 'WordAISuggesTitleRadioBox-' . $i;
				$single_sanitized_title_name = preg_replace("/[\n\n\"]+/","", SFTCY_Wordai_OpenAI::$output['responseText'] );				
				SFTCY_Wordai_OpenAI::$output['listOfTitles'] .= '<li><label for="' . $radio_input_id . '"><input type="radio" id="' . $radio_input_id . '" class="suggested-title-radio" name="suggested-title-radio" /> '. $single_sanitized_title_name .'</label></li>';
			}
		}		
		
		echo wp_json_encode( SFTCY_Wordai_OpenAI::$output );		
		wp_die();
	}
				
	
	/**
	 * Update - suggest title
	 * return - json
	 */
	public static function sc_wordai_update_suggest_title() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );								
		self::$output			= [];
		$selected_title			=  isset( $_POST['params']['selectedTitle'] )? sanitize_text_field( $_POST['params']['selectedTitle'] ) : '';		
		$post_id				=  isset( $_POST['params']['postID'] )? sanitize_text_field( $_POST['params']['postID'] ) : 0;		
		//var_dump( $post_id );
		//var_dump( $selected_title );
		
		// Update post title
        $post_data = array(
            'ID'           => intval($post_id),
            'post_title'   => $selected_title,
        );

        // Update post title into the database
        $update_status	=	wp_update_post( $post_data );
		if ( $update_status ) {
			self::$output['status']	= 'success';
			self::$output['postID']	= $update_status;
		}
		else {
			self::$output['status']	= 'fail';
		}
						
		echo wp_json_encode( self::$output );		
		wp_die();		
	}
	
	/**
	 * Write content
	 * return - json
	 * @since 1.0.0
	 */
	public static function sc_wordai_write_content() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );								
		$prompt_hints			= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : '';		
		$prompt					= SFTCY_Wordai_OpenAI::generate_prompt( trim( $prompt_hints ), 'content' );								
		$api_params				= SFTCY_Wordai_OpenAI::set_openai_params();
		//$api_params['prompt']	= $prompt;
		$api_params['messages']	= [ array('role' => 'user', 'content' => $prompt ) ];
				
		$response				= SFTCY_Wordai_OpenAI::create_content( $api_params);		
		// var_dump( SFTCY_Wordai_OpenAI::$output );		

		//SFTCY_Wordai_OpenAI::$output['status']			= 'success';
		//SFTCY_Wordai_OpenAI::$output['responseText']	= "\n\nDog: Man's Best Friend\n\nIntroduction\n\nThe dog is one of the most beloved animals in the world. It is a loyal and faithful companion that has been a part of human life for thousands of years. Dogs have been used for hunting, protection, and companionship, and they are still a popular pet today. Dogs come in a variety of shapes, sizes, and colors, and they can be found in almost every country in the world. Dogs are known for their intelligence, loyalty, and unconditional love, and they are often referred to as \"man's best friend.\"\n\nPhysical Characteristics\n\nDogs come in a wide variety of shapes, sizes, and colors. They can range from small toy breeds such as Chihuahuas and Pomeranians to large breeds such as Great Danes and St. Bernards. They can have short or long coats, and their fur can be straight, wavy, or curly. Dogs can be solid colors, or they can have a variety of markings. Some of the most popular colors are black, white, brown, and red.\n\nPersonality Traits\n\nDogs are known for their intelligence, loyalty, and unconditional love. They are very social animals and enjoy spending time with their owners. Dogs are also very protective of their owners and will often bark or growl when they sense danger. They are also very playful and enjoy playing fetch, tug-of-war, and other games. Dogs are also very trainable and can learn a variety of commands and tricks.\n\nConclusion\n\nThe dog is one of the most beloved animals in the world. It is a loyal and faithful companion that has been a part of human life for thousands of years. Dogs come in a variety of shapes, sizes, and colors, and they are known for their intelligence, loyalty, and unconditional love. Dogs are often referred to as \"man's best friend,\" and they make wonderful pets.";
				
		if ( isset( SFTCY_Wordai_OpenAI::$output['status'] ) && SFTCY_Wordai_OpenAI::$output['status'] == 'success' ) {
			// Replace \n\n with br to insert into dynamic created core/paragraph inside properly
			SFTCY_Wordai_OpenAI::$output['responseTextWithBR'] = preg_replace("/\n\n/","<br/><br/>", SFTCY_Wordai_OpenAI::$output['responseText'] );
		}
		
		echo wp_json_encode( SFTCY_Wordai_OpenAI::$output );		
		wp_die();
	}
	
	/**
	 * Write Excerpt
	 * return - json
	 */
	public static function sc_wordai_write_excerpt() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );						
		$prompt_hints			= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : '';				
		$prompt					= SFTCY_Wordai_OpenAI::generate_prompt( trim( $prompt_hints ), 'excerpt' );		
		$api_params				= SFTCY_Wordai_OpenAI::set_openai_params();	
		//$api_params['prompt']	= $prompt;
		$api_params['messages']	= [ array('role' => 'user', 'content' => $prompt ) ];		
		//var_dump( $api_params );						
		$response				= SFTCY_Wordai_OpenAI::create_content( $api_params);		
		//var_dump( SFTCY_Wordai_OpenAI::$output );	
		
		if ( isset( SFTCY_Wordai_OpenAI::$output['status'] ) && SFTCY_Wordai_OpenAI::$output['status'] == 'success' ) {
			// Replace \n\n with br to insert content into dynamic created core/paragraph inside properly			
			SFTCY_Wordai_OpenAI::$output['responseTextWithBR'] = preg_replace("/\n\n/","<br/><br/>", SFTCY_Wordai_OpenAI::$output['responseText'] );
		}		
		
		echo wp_json_encode( SFTCY_Wordai_OpenAI::$output );		
		wp_die();
	}
	
	/**
	 * Write Tags
	 * return - json
	 */
	public static function sc_wordai_write_tags() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );						
		$prompt_hints			= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : '';				
		$prompt					= SFTCY_Wordai_OpenAI::generate_prompt( trim( $prompt_hints ), 'tags' );		
		$api_params				= SFTCY_Wordai_OpenAI::set_openai_params();	
		//$api_params['prompt']	= $prompt;
		$api_params['messages']	= [ array('role' => 'user', 'content' => $prompt ) ];		
		//var_dump( $api_params );								
		$response				= SFTCY_Wordai_OpenAI::create_content( $api_params);		
		//var_dump( SFTCY_Wordai_OpenAI::$output );	
		
		if ( isset( SFTCY_Wordai_OpenAI::$output['status'] ) && SFTCY_Wordai_OpenAI::$output['status'] == 'success' ) {
			// Replace \n\n with br to insert content into dynamic created core/paragraph inside properly
			SFTCY_Wordai_OpenAI::$output['responseText'] = preg_replace("/[\n\n\"]+/","", SFTCY_Wordai_OpenAI::$output['responseText'] );
		}		
		
		echo wp_json_encode( SFTCY_Wordai_OpenAI::$output );		
		wp_die();
	}	
	
	/**
	 * Generate image
	 * @since 1.0.0
	 */
	public static function sc_wordai_generate_image() {
		check_ajax_referer( 'scwordai_wpnonce', 'security' );
		$api_image_params			= [];
		$prompt_hints				= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : '';		
		$prompt						= SFTCY_Wordai_OpenAI::generate_prompt( trim( $prompt_hints ), 'image' );	
		$api_image_params			= SFTCY_Wordai_OpenAI::set_openai_image_params();	
		$api_image_params['prompt']	= $prompt;
		
		// var_dump( $api_image_params );
		// exit();
		
		$response				= SFTCY_Wordai_OpenAI::generate_image( $api_image_params );						
		$images_url				= [];
		if ( SFTCY_Wordai_OpenAI::$output['responseImageUrls'] ) {
			foreach ( SFTCY_Wordai_OpenAI::$output['responseImageUrls'] as $image ) {
				$images_url[]	= $image['url'];
			}
			// SFTCY_Wordai_OpenAI::$output['openAIImgURLs']	= implode( ',', $images_url );	
			SFTCY_Wordai_OpenAI::$output['openAIImgURLs']	= $images_url;	
		}
		
		// var_dump( SFTCY_Wordai_OpenAI::$output );
		echo wp_json_encode( SFTCY_Wordai_OpenAI::$output );		
		wp_die();		
	}
	
	public static function sc_wordai_image_upload_mime_type_issue( $settings ) {
		if (defined('ALLOW_UNFILTERED_UPLOADS') && ALLOW_UNFILTERED_UPLOADS) {
			unset($settings['filters']['mime_types']);
		}		
		return $settings;
	}
	
	/**
	 * OpenAI generated image URLs
	 * Check raw image urls & process one by one
	 * @since 1.0.0
	 */
	public static function sc_wordai_upload_image_to_wp_media() {								
		check_ajax_referer( 'scwordai_wpnonce', 'security' );
		self::$output							= [];				
		// $post_id								= sanitize_text_field( $_POST['params']['postID'] );				
		$imgURLS								= isset( $_POST['params']['imgURLs'] ) ? sanitize_url( $_POST['params']['imgURLs'] ) : '';
		$images_url								= explode( ',', $imgURLS );
		// array_walk( $images_url, function( &$value, $key ) { $value = sanitize_url( $value ); });
		array_walk( $images_url, function( &$value, $key ) { $value = esc_url_raw( $value ); });
		$prompt									= isset( $_POST['params']['prompt'] )? sanitize_text_field( $_POST['params']['prompt'] ) : time() . 'image';
		$prompt_slug							= preg_replace( '/[\s]+/', '-', $prompt );							
		
		// $images_url array - raw image URLs
		foreach ( $images_url as $image_url ) {			
			$image_name							= $prompt_slug . '-' . time() . '.jpg';		
			self::$output['imgaesUploadInfo'][]	= self::sc_wordai_upload_image_to_media_gallery( $image_url, $image_name );											
		}
				
		self::$output['totalImages']			= count( $images_url );
		self::$output['totalSuccess']			= count ( array_filter( self::$output['imgaesUploadInfo'], function($eachArr) { if ( $eachArr['status'] == 'success') return true; }) );
		self::$output['totalFail']			    = count ( array_filter( self::$output['imgaesUploadInfo'], function($eachArr) { if ( $eachArr['status'] == 'fail') return true; }) );
		self::$output['attachmentIDs']			= array_map( function($eachArr) { if ( $eachArr['status'] == 'success') { return $eachArr['attachmentID'];} }, self::$output['imgaesUploadInfo'] );
		self::$output['attachmentURLs']			= array_map( function($eachArr) { if ( $eachArr['status'] == 'success') { return $eachArr['attachmentURL'];} }, self::$output['imgaesUploadInfo'] );
		
		self::$output['firstImageAttachmentID']	= reset( self::$output['attachmentIDs'] );
		self::$output['firstImageAttachmentURL']= reset( self::$output['attachmentURLs'] );
		//self::$output['firstImageAtltText']		= update_post_meta(self::$output['firstImageAttachmentID'], '_wp_attachment_image_alt', 'Alt Text for prompt ' . $prompt );				
		
		if ( self::$output['totalImages'] ==  self::$output['totalSuccess'] ) {
			self::$output['status']				= 'success';
			self::$output['statusMsg']          = 'All images uploaded.';
		}
		else if ( self::$output['totalSuccess'] > 0 ) {
			self::$output['status']				= 'success';
			self::$output['statusMsg']          = 'Total ' . self::$output['totalSuccess'] . ' success of ' . self::$output['totalImages'];
		}
		else {
			self::$output['status']				= 'fail';
		}
		
		// print_r( self::$output );
		echo wp_json_encode( self::$output );
		wp_die();		
	}
	
	/**
	 * Download images from openAI generated URL
	 * Upload download images to WP gallery
	 * @since 1.0.0
	 */
	public static function sc_wordai_upload_image_to_media_gallery( $image_url = null, $image_name = null ) {
		$output									= [];				
		if ( empty( $image_url ) || is_null( $image_url ) ) {
			$output['status']					= 'fail';	
			$output['errorMessage']				= 'Image URL required.';
		}
		else {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		
			
			$tmp 								= download_url( $image_url );
			$file_array 						= array(
													//'name'     => basename( $image_url ),
				                                    'name'     => $image_name, 
													'tmp_name' => $tmp
			);

			$id 								= media_handle_sideload( $file_array, 0 );			
			if ( is_wp_error( $id ) ) {			
				@unlink( $file_array['tmp_name'] );
				$output['attachmentID']			= $id;	
				$output['status']				= 'fail';	
				$output['errorMessage']			= $id->get_error_message();
			}
			else {
				$output['status']				= 'success';
				$output['attachmentID']			= $id;
				$output['attachmentURL']		= wp_get_attachment_url( $id );						
			}

			// Unlink tmp
			@unlink( $tmp );		
		}
		
		return $output;			
	}
	
	
	
				
} // End Class
}
?>