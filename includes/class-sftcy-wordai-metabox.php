<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordai_Metabox') ) {
class SFTCY_Wordai_Metabox {
	private static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}	
	/**
	 * Hook	
	 */
	private static function initiate_hooks() {			    						
		add_action( 'add_meta_boxes', array( __CLASS__, 'wordai_add_metaboxes' ) );				
		self::$initiated = true;
	}			
	/**
	 * Add metabox for - post , page, product
	 */	
	public static function wordai_add_metaboxes() {
		add_meta_box(
					'sftcy_wordai_metabox',
					__('AI Content Generator', 'wordai'),                // Metabox Title
					array( __CLASS__, 'scwordai_metabox_html_callback'), // Callback function
					array('post', 'page', 'product'),                    // Post types
					'side', 
					'high' 
				);		
		
	}	
	public static function scwordai_metabox_html_callback() {		
		include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/metabox-popup.php';
	}
} // End class
}