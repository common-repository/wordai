<?php
/**
 * Plugin Name: WordAI
 * Plugin URI:  https://softcoy.com
 * Description: AI driven humanlike SEO friendly content writing with HD images generation based on OpenAI. Automatize your Post / Page / Product content writing tasks. 
 * Version:     1.0.4
 * Author:      softcoy
 * Author URI:  https://softcoy.com/
 * License:     GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordai
 * Domain Path: /languages
 */
 
 /*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  
  Copyright 2024 softcoy.com
 */ 

  if ( ! defined( 'ABSPATH' ) ) {
	  exit; // Exit if accessed directly.
  }
  
if ( ! defined( 'SFTCY_WORDAI_VERSION') ) { 
  define( 'SFTCY_WORDAI_VERSION', '1.0.4' );
}
if ( ! defined( 'SFTCY_WORDAI_MINIMUM_PHP_VERSION') ) {
  define( 'SFTCY_WORDAI_MINIMUM_PHP_VERSION', '7.2' );
}
if ( ! defined( 'SFTCY_WORDAI_MINIMUM_WP_VERSION') ) {
  define( 'SFTCY_WORDAI_MINIMUM_WP_VERSION', '6.0' );
}
if ( ! defined( 'SFTCY_WORDAI_PLUGIN_DIR') ) {
  define( 'SFTCY_WORDAI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );       
}
if ( ! defined( 'SFTCY_WORDAI_PLUGIN_INC') ) {
  define( 'SFTCY_WORDAI_PLUGIN_INC', plugin_dir_path( __FILE__ ) . '/includes/' );     
}

  if ( ! function_exists( 'sftcy_wordai_autoloader' ) ) {
	  /**
	   * Autoload classes 
	   * @param - $sc_class - Class name
	   * @since 1.0.0
	   */
	  function sftcy_wordai_autoloader( $sc_class ) {		
		  $sc_class  = 'class-' . trim( $sc_class );
		  $classfile = SFTCY_WORDAI_PLUGIN_INC . strtolower( str_replace( '_', '-', $sc_class ) ) . '.php';
		  if ( file_exists( $classfile ) ) {
			  require_once( $classfile );		  		  
		  }
	  }  
  }  
  spl_autoload_register('sftcy_wordai_autoloader');  
  // Bootstrapping
  new SFTCY_Wordai_Autoloader();	  
  register_activation_hook( __FILE__, array( 'SFTCY_Wordai','activate') );      