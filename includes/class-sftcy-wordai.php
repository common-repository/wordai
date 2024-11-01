<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordai' ) ) {
class SFTCY_Wordai {
	private static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
	
	/**
	 * Hooks
	 */
	private static function initiate_hooks() {			    				
	    //add_action( 'admin_init', array( __CLASS__, 'add_scwordai_settings_data' ) );		
		add_action( 'admin_init', array( __CLASS__, 'sc_wordai_check_post_type_excerpt_support' ) );		
		add_action( 'admin_menu', array( __CLASS__, 'add_scwordai_submenus' ) );		
		add_action( 'admin_footer', array( __CLASS__, 'sc_wordai_add_html_contents_at_admin_footer' ) );
		
		add_action( 'admin_notices', array( __CLASS__, 'scwordai_admin_notices' ) );		
		add_action( 'plugins_loaded', array( __CLASS__, 'scwordai_load_textdomain') );
		//add_filter( 'plugin_row_meta',     array( __CLASS__, 'scwordai_row_link'), 10, 2 );
		
		add_filter( 'post_row_actions', array( __CLASS__, 'sc_wordai_add_post_row_actions'), 10, 2 );
		add_filter( 'page_row_actions', array( __CLASS__, 'sc_wordai_add_post_row_actions'), 10, 2 );
		//add_filter( 'page_row_actions', array( __CLASS__, 'sc_wordai_add_page_row_actions'), 10, 2 );
		
		self::$initiated = true;
	}
   
	/**
	 * Check & activate plugin
	 */
	public static function activate() {
		self::check_preactivation_requirements();
		flush_rewrite_rules( true );
		
	}
	
	/**
	 * Pre-active check
	 */
	public static function check_preactivation_requirements() {				
		if ( version_compare( PHP_VERSION, SFTCY_WORDAI_MINIMUM_PHP_VERSION, '<' ) ) {
			wp_die( esc_html_e( 'Minimum PHP Version required: ', 'wordai' ) . SFTCY_WORDAI_MINIMUM_PHP_VERSION );
		}
        global $wp_version;
		if ( version_compare( $wp_version, SFTCY_WORDAI_MINIMUM_WP_VERSION, '<' ) ) {
			wp_die( esc_html_e( 'Minimum Wordpress Version required: ', 'wordai' ) . SFTCY_WORDAI_MINIMUM_WP_VERSION );
		}
	}
	
	/**
	 * Load language text domain
	 */
	public static function scwordai_load_textdomain() {
		load_plugin_textdomain( 'wordai', false, SFTCY_WORDAI_PLUGIN_DIR . 'languages/' ); 
	}
			
	/**
	 * Add menus
	 */
	public static function add_scwordai_submenus() {
		
		// Top Menu|Parent Menu - WordAI
		$iconsvg_data 		= '';
		$icon_image_url		= plugins_url('../admin/css/images/WAI-Black-White-Icon-16x16.png', __FILE__ );
		//if (  $icon_image_contents	= wp_remote_retrieve_body( wp_remote_get( $icon_image_url ) ) ) {
		//	$iconsvg_data	= base64_encode( $icon_image_contents );
		//}		
		//add_menu_page( __( 'WordAI - Auto Content Writing', 'wordai' ), 'WordAI', 'manage_options', 'word-ai-topmenu', '', 'dashicons-welcome-write-blog', 6 );
		add_menu_page( __( 'WordAI - Auto Content Writing', 'wordai' ), 'WordAI', 'manage_options', 'word-ai-topmenu', '', $icon_image_url, 6 );
		//add_menu_page( __( 'WordAI - AI Content Writing', 'wordai' ), 'WordAI', 'manage_options', 'word-ai-topmenu', '', 'data:image/svg+xml;base64,' . $iconsvg_data, 6 );

										  
		// Submenu - API Settings - sc-wordai-api-settings page slug 
		add_submenu_page(
		    'word-ai-topmenu',
        __( 'WordAI - API Settings', 'wordai' ),
        __( 'API Settings', 'wordai' ),
            'manage_options',
            'word-ai-topmenu',
			array( __CLASS__, 'add_scwordai_submenus_apisettings_callback' )        
          );
		
		// Submenu - Content Settings - sc-wordai-content-settings page slug  
		add_submenu_page(
		    'word-ai-topmenu',
        __( 'WordAI - Content Settings', 'wordai' ),
        __( 'Content Settings', 'wordai' ),
            'manage_options',
            'sc-wordai-content-settings',
			array( __CLASS__, 'add_scwordai_submenus_content_settings_callback' )        
          );

		// Submenu - Image Settings - sc-wordai-image-settings page slug
		add_submenu_page(
		    'word-ai-topmenu',
        __( 'WordAI - Image Settings', 'wordai' ),
        __( 'Image Settings', 'wordai' ),
            'manage_options',
            'sc-wordai-image-settings',
			array( __CLASS__, 'add_scwordai_submenus_image_settings_callback' )        
          );						
	}	

	/**
	 * Menu callback page
	 */
	public static function add_scwordai_submenus_settings_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/submenus-settings.php';		
	}
	
	/**
	 * Menu callback page
	 */	
	public static function add_scwordai_submenus_apisettings_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/submenus-apisettings.php';		
	}

	/**
	 * Menu callback page
	 */	
	public static function add_scwordai_submenus_content_settings_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/submenus-content-settings.php';		
	}

	/**
	 * Menu callback page
	 */	
	public static function add_scwordai_submenus_image_settings_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/submenus-image-settings.php';		
	}
	
	/**
	 * Check Post Experpt support
	 * if not supported activate 
	 */
	public static function sc_wordai_check_post_type_excerpt_support() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}				
		if ( ! post_type_supports( 'page', 'excerpt' ) ) {
			add_post_type_support('page', 'excerpt');
		}
		if ( ! post_type_supports( 'post', 'excerpt' ) ) {
			add_post_type_support('post', 'excerpt');
		}		
	}
	
	/**
	 * OpenAI API settings data
	 * Default parameters
	 * return - array
	 */
	public static function sc_wordai_api_settings_default_parameters() {
		$default_params											= [];
		$default_params['sc-wordai-openai-model-slug']			= 'gpt-3.5-turbo';
		$default_params['sc-wordai-streaming']					= 1;
		$default_params['sc-wordai-temperature']				= 0.2;
		$default_params['sc-wordai-top-p']						= 0.1;
		$default_params['sc-wordai-max-tokens']					= null;
		$default_params['sc-wordai-presence-penalty-input']		= 0;
		$default_params['sc-wordai-frequency-penalty-input']	= 0;
		$default_params['sc-wordai-best-of-input']				= 1;
		$default_params['sc-wordai-stop-input']					= null; // Keep it null - if any chars mistyped then response may cut off
		
		return $default_params;
	}
	
	/**
	 * OpenAI API content settings data
	 * Default parameters
	 * return - array
	 */
	public static function sc_wordai_api_content_settings_default_parameters() {
		$default_params	= [];
		$default_params['sc-wordai-language-code'] = 'en-US';
		$default_params['sc-wordai-writing-style'] = 'descriptive';
		$default_params['sc-wordai-writing-tone'] = 'informative';
		$default_params['sc-wordai-title-length'] = '30n40';
		$default_params['sc-wordai-excerpt-words'] = 30;
		$default_params['sc-wordai-tags-number'] = 2;
		$default_params['wordai-suggested-title-number'] = 2;
		
		$default_params['sc-wordai-content-paragraphs'] = 3;
		$default_params['sc-wordai-insert-title'] = 1;
		$default_params['sc-wordai-insert-content'] = 1;
		$default_params['sc-wordai-insert-excerpt']	= 1;
		$default_params['sc-wordai-insert-tags'] = 1;		
		
		return $default_params;
	}

	/**
	 * OpenAI API image settings data
	 * Default parameters
	 * return - array
	 */
	public static function sc_wordai_api_image_settings_default_parameters() {
		$default_params											= [];
		$default_params['sc-wordai-openai-image-model-slug']	= 'dall-e-2';
		$default_params['sc-wordai-image-number']				= 2;
		$default_params['sc-wordai-dalle2-image-size']			= '1024x1024';
		$default_params['sc-wordai-dalle2-image-style']			= 'natural';
		$default_params['sc-wordai-dalle3-image-size']			= '1024x1024';
		$default_params['sc-wordai-dalle3-image-style']			= 'vivid';
		$default_params['sc-wordai-dalle3-image-hd-quality']	= 1;
		$default_params['sc-wordai-imagesave-togallery']		= 1;
		$default_params['sc-wordai-set-feature-image']			= 1;		
		
		return $default_params;
	}			
	/**
	 * Get current image settings parameter
	 * return - array - current image settings data
	 */
	public static function sc_wordai_get_current_image_settings() {		
		$default = self::sc_wordai_api_image_settings_default_parameters();
		$options_data = get_option('sftcy-wordai-image-settings-data');
		$options_data = $options_data ? maybe_unserialize( $options_data ) : [];

		// Image Model
		$options_data['sc-wordai-openai-image-model-slug'] = isset( $options_data['sc-wordai-openai-image-model-slug'] )? sanitize_text_field( $options_data['sc-wordai-openai-image-model-slug'] ) : SFTCY_Wordai_OpenAI::$IMAGE_MODEL;		
		
		// Image Number
		$options_data['sc-wordai-image-number'] = isset( $options_data['sc-wordai-image-number'] )? sanitize_text_field( $options_data['sc-wordai-image-number'] ) : $default['sc-wordai-image-number'];
		// Image Size - DALL-E-2
		$options_data['sc-wordai-dalle2-image-size'] = isset( $options_data['sc-wordai-dalle2-image-size'] )? sanitize_text_field( $options_data['sc-wordai-dalle2-image-size'] ) : $default['sc-wordai-dalle2-image-size'];
		// Image Style - DALL-E-2
		$options_data['sc-wordai-dalle2-image-style'] = isset( $options_data['sc-wordai-dalle2-image-style'] )? sanitize_text_field( $options_data['sc-wordai-dalle2-image-style'] ) : $default['sc-wordai-dalle2-image-style'];
        // Image Size - DALL-E-3
		$options_data['sc-wordai-dalle3-image-size'] = isset( $options_data['sc-wordai-dalle3-image-size'] )? sanitize_text_field( $options_data['sc-wordai-dalle3-image-size'] ) : $default['sc-wordai-dalle3-image-size'];
		// Image Style - DALL-E-3
		$options_data['sc-wordai-dalle3-image-style'] = isset( $options_data['sc-wordai-dalle3-image-style'] )? sanitize_text_field( $options_data['sc-wordai-dalle3-image-style'] ) : $default['sc-wordai-dalle3-image-style'];		
		
		// Initial true as default if not set yet - Image Save To Gallery
		$options_data['sc-wordai-imagesave-togallery']	= ( ! isset( $options_data['sc-wordai-imagesave-togallery'] ) )? $default['sc-wordai-imagesave-togallery'] : ( ( isset( $options_data['sc-wordai-imagesave-togallery'] ) && $options_data['sc-wordai-imagesave-togallery'] == 1 )? 1 : 0 );

		// Initial true as default if not set yet - Image HD Quality
		$options_data['sc-wordai-dalle3-image-hd-quality'] = ( ! isset( $options_data['sc-wordai-dalle3-image-hd-quality'] ) )? $default['sc-wordai-dalle3-image-hd-quality'] : ( ( isset( $options_data['sc-wordai-dalle3-image-hd-quality'] ) && $options_data['sc-wordai-dalle3-image-hd-quality'] == 1 )? 1 : 0 );				

		// Initial true as default if not set yet - Set Feature Image
		$options_data['sc-wordai-set-feature-image'] = ( ! isset( $options_data['sc-wordai-set-feature-image'] ) )? $default['sc-wordai-set-feature-image'] : ( ( isset( $options_data['sc-wordai-set-feature-image'] ) && $options_data['sc-wordai-set-feature-image'] == 1 )? 1 : 0 );				
		
		return $options_data;
	}
	
	/**
	 * Get current API settings parameter
	 * @return - array - current API settings data
	 */
	public static function sc_wordai_get_current_api_settings() {				
		$default = self::sc_wordai_api_settings_default_parameters();                 
		$options_data = get_option('sftcy-wordai-apisettings-data'); 
		$options_data = $options_data ? maybe_unserialize( $options_data ) : [] ;	
		
		$options_data['wordai-api-key-data'] = isset( $options_data['wordai-api-key-data'] )? sanitize_text_field( $options_data['wordai-api-key-data'] ) : '';		
		$options_data['wordai-api-key-mask'] = self::wordai_openai_api_key_mask_format( $options_data['wordai-api-key-data'] );		
				
		$settings_data['sc-wordai-openai-model-slug'] = isset( $options_data['sc-wordai-openai-model-slug'] )? sanitize_text_field( $options_data['sc-wordai-openai-model-slug'] ) : SFTCY_Wordai_OpenAI::$MODEL;
		// Initial streaming true as default if not set
		$options_data['sc-wordai-streaming'] = ( ! isset( $options_data['sc-wordai-streaming'] ) )? $default['sc-wordai-streaming'] : ( ( isset( $options_data['sc-wordai-streaming'] ) && $options_data['sc-wordai-streaming'] == 1 )? 1 : 0 );
		$options_data['sc-wordai-temperature'] = isset( $options_data['sc-wordai-temperature'] )? sanitize_text_field( $options_data['sc-wordai-temperature'] ) : $default['sc-wordai-temperature'];
		$options_data['sc-wordai-top-p'] = isset( $options_data['sc-wordai-top-p'] )? sanitize_text_field( $options_data['sc-wordai-top-p'] ) : $default['sc-wordai-top-p'];
		$options_data['sc-wordai-max-tokens'] =	isset( $options_data['sc-wordai-max-tokens'] )? sanitize_text_field( $options_data['sc-wordai-max-tokens'] ) : $default['sc-wordai-max-tokens'];
		$options_data['sc-wordai-presence-penalty-input'] =	isset( $options_data['sc-wordai-presence-penalty-input'] )? sanitize_text_field( $options_data['sc-wordai-presence-penalty-input'] ) : $default['sc-wordai-presence-penalty-input'];
		$options_data['sc-wordai-frequency-penalty-input'] = isset( $options_data['sc-wordai-frequency-penalty-input'] )? sanitize_text_field( $options_data['sc-wordai-frequency-penalty-input'] ) : $default['sc-wordai-frequency-penalty-input'];
		$options_data['sc-wordai-best-of-input'] = isset( $options_data['sc-wordai-best-of-input'] )? sanitize_text_field( $options_data['sc-wordai-best-of-input'] ) : $default['sc-wordai-best-of-input'];
		$options_data['sc-wordai-stop-input'] = isset( $options_data['sc-wordai-stop-input'] )? sanitize_text_field( $options_data['sc-wordai-stop-input'] ) : $default['sc-wordai-stop-input'];
		
		return $options_data;				
	}
	
	/**
	 * Check Output Buffering Status
	 * @return - boolean
	 */
	public static function wordai_check_output_buffering_status() {		
		return  array_filter( ob_get_status() ) ? true : false;					
	}
	
	/**
	 * OpenAI API Key Mask Format
	 * @return - string - API Key Mask
	 */
	public static function wordai_openai_api_key_mask_format( $api_key = '' ) {
		// Default - Placeholder display
		$openai_api_key_mask = 'sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
		if ( ! empty( $api_key ) ) {
			$openai_api_key_mask = str_split( $api_key, 3 );	
			array_walk( $openai_api_key_mask, function( &$val, $index ) { $val = ( $index % 2 !== 0 )? str_repeat( '*', strlen( $val )) : $val; } );		
			$openai_api_key_mask = implode('', $openai_api_key_mask );		
		}
		return $openai_api_key_mask;
	}
	
	/**
	 * Get current Content settings parameter
	 * return - array - current Content settings data
	 */
	public static function sc_wordai_get_current_content_settings() {	
		$default = self::sc_wordai_api_content_settings_default_parameters();
		$options_data = get_option('sftcy-wordai-content-settings-data');
		$options_data = $options_data ? maybe_unserialize( $options_data ) : [] ;

		$options_data['sc-wordai-language-code'] = isset( $options_data['sc-wordai-language-code'] ) ? sanitize_text_field( $options_data['sc-wordai-language-code'] ) : $default['sc-wordai-language-code'];
		$options_data['sc-wordai-writing-style'] = isset( $options_data['sc-wordai-writing-style'] ) ? sanitize_text_field( $options_data['sc-wordai-writing-style'] ) : $default['sc-wordai-writing-style'];
		$options_data['sc-wordai-writing-tone'] = isset( $options_data['sc-wordai-writing-tone'] ) ? sanitize_text_field( $options_data['sc-wordai-writing-tone'] ) : $default['sc-wordai-writing-tone'];
		$options_data['sc-wordai-title-length']	= isset( $options_data['sc-wordai-title-length'] ) ? sanitize_text_field( $options_data['sc-wordai-title-length'] ) : $default['sc-wordai-title-length'];
		$options_data['sc-wordai-content-paragraphs'] = isset( $options_data['sc-wordai-content-paragraphs'] )? sanitize_text_field( $options_data['sc-wordai-content-paragraphs'] ) : $default['sc-wordai-content-paragraphs'];
		$options_data['sc-wordai-excerpt-words'] = isset( $options_data['sc-wordai-excerpt-words'] ) ? sanitize_text_field( $options_data['sc-wordai-excerpt-words'] ) : $default['sc-wordai-excerpt-words'];
		$options_data['sc-wordai-tags-number'] = isset( $options_data['sc-wordai-tags-number'] ) ? sanitize_text_field( $options_data['sc-wordai-tags-number'] ) : $default['sc-wordai-tags-number'];
		$options_data['wordai-suggested-title-number'] = isset( $options_data['wordai-suggested-title-number'] ) ? sanitize_text_field( $options_data['wordai-suggested-title-number'] ) : $default['wordai-suggested-title-number'];
				

		// Insert title - Initial false as default if not set
		$options_data['sc-wordai-insert-title']	= ( ! isset( $options_data['sc-wordai-insert-title'] ) ) ? $default['sc-wordai-insert-title'] : ( ( isset( $options_data['sc-wordai-insert-title'] ) && $options_data['sc-wordai-insert-title'] == 1 )? 1 : 0 );
		// Insert content - Initial false as default if not set yet
		$options_data['sc-wordai-insert-content'] =	( ! isset( $options_data['sc-wordai-insert-content'] ) )? $default['sc-wordai-insert-content'] : ( ( isset( $options_data['sc-wordai-insert-content'] ) && $options_data['sc-wordai-insert-content'] == 1 )? 1 : 0 );
		// Insert Excerpt - Initial false as default if not set
		$options_data['sc-wordai-insert-excerpt'] =	( ! isset( $options_data['sc-wordai-insert-excerpt'] ) )? $default['sc-wordai-insert-excerpt'] : ( ( isset( $options_data['sc-wordai-insert-excerpt'] ) && $options_data['sc-wordai-insert-excerpt'] == 1 )? 1 : 0 );
		// Insert tags - Initial false as default if not set
		$options_data['sc-wordai-insert-tags'] = ( ! isset( $options_data['sc-wordai-insert-tags'] ) )? $default['sc-wordai-insert-tags'] : ( ( isset( $options_data['sc-wordai-insert-tags'] ) && $options_data['sc-wordai-insert-tags'] == 1 )? 1 : 0 );
				
		return $options_data;				
	}
	
	
	/**
	 * Add admin footer html content
	 * Suggest Title Popup window html content
	 */	
	public static function sc_wordai_add_html_contents_at_admin_footer() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/suggest-titles-popup.php';				
	}
	
	/**
	 * Suggest title popup window - Clickable link text
	 * Post rows
	 */
	public static function sc_wordai_add_post_row_actions( $actions, $post ) {
		$supported_post_types	= [ 'post', 'page', 'product' ];
		if ( in_array( $post->post_type, $supported_post_types ) ) {
			//$new_actions[]		= '<a href="javascript:void(0)" class="sc-wordai-suggest-titles" data-scwordai-post-id="' . esc_attr($post->ID) . '"><i class="dashicons dashicons-welcome-write-blog"></i>' . __('WordAI Title', 'wordai' ) . '</a>';
			$new_actions[]		= '<a href="javascript:void(0)" class="sc-wordai-suggest-titles" data-scwordai-post-id="' . esc_attr($post->ID) . '"><span class="sc-wordai-metabox-popup-window-title-icon"></span>' . __('WordAI Title', 'wordai' ) . '</a>';			
			return array_merge( $actions, $new_actions );
		}
	   return $actions;		
	}	
	
	/**
	 * Suggest title popup window - Clickable link text
	 * Page rows
	 */
	/*public static function sc_wordai_add_page_row_actions( $actions, $post ) {
		$supported_post_types	= [ 'post', 'page', 'product' ];
		if ( in_array( $post->post_type, $supported_post_types ) ) {
			$new_actions[]		= '<a href="Javascript:void(0)" class="sc-wordai-suggest-titles" data-scwordai-post-id="'.$post->ID.'"><i class="dashicons dashicons-welcome-write-blog"></i>'. __('WordAI Suggest Title', 'wordai') . '</a>';
			return array_merge( $actions, $new_actions );
		}
	   return $actions;		
	}*/	
	
	/**
	 * Admin notice
	 */
	public static function scwordai_admin_notices() {
		$admin_notice = false;		
		$data = SFTCY_Wordai::sc_wordai_get_current_api_settings();
		if ( ! isset( $data['wordai-api-key-data'] ) || empty( $data['wordai-api-key-data'] ) ) {
				$admin_notice = true;
		}							
		if ( $admin_notice ) {			
			$url 	= admin_url('admin.php?page=word-ai-topmenu');
			$openai_apikey_url = '<a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Key</a>';						
			$alink 	= '<a href="' . esc_url($url) . '">Add your OpenAI API Key.</a>';			
			printf('<div class="notice notice-warning is-dismissible sc-wordai-no-api-key-added-notice-wrapper">');
		    printf('<div class="scwordai-notice-wrapper"><h3><i class="dashicons dashicons-welcome-write-blog"></i> WordAI - AI Content Writer:</h3> <h4>%s required to generate your contents, HD images. %s</h4></div>', wp_kses( $openai_apikey_url, [ 'a' => [ 'href' => [], 'title' => [], 'target' => [] ] ] ), wp_kses( $alink, [ 'a' => [ 'href' => [], 'title' => [], 'target' => [] ] ] ) );
	        printf('</div>');
		}
		
	}
	
	public static function scwordai_row_link( $actions, $plugin_file ) {
		$wordsmtp_plugin 	= plugin_basename( SFTCY_WORDAI_PLUGIN_DIR );
		$plugin_name 		= basename($plugin_file, '.php');
		if ( $wordsmtp_plugin == $plugin_name ) {
			//$doclink[] 		= '<a href="https://softcoy.com/wordai" title="WordAI - Docs" target="_blank">WordAI Docs</a>';	
			//$doclink[] 		= '<a href="https://softcoy.com/wordai" title="WordAI Support" target="_blank">Support</a>';	
			//return array_merge( $actions, $doclink );
		}
		return $actions;
	}	
	
} // End class
}