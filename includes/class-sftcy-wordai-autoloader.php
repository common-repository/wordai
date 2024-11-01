<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'SFTCY_Wordai_Autoloader') ) {
class SFTCY_Wordai_Autoloader {	
	public function __construct() {		
		new SFTCY_Wordai();
		new SFTCY_Wordai_Ajaxhandler();
		new SFTCY_Wordai_OpenAI();
		new SFTCY_Wordai_Metabox();
	}	
}
}
?>