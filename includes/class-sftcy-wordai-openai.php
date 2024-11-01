<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordai_OpenAI') ) {
class SFTCY_Wordai_OpenAI {
	private static $initiated 		= false;
	
	public static $API_KEY			= null;
	public static $AI_LISTMODEL_EP	= 'https://api.openai.com/v1/models';            
	//public static $AI_COMPLETION_EP	= 'https://api.openai.com/v1/completions';	    // OLD Models EP
	public static $AI_COMPLETION_EP	= 'https://api.openai.com/v1/chat/completions';     // GPT-4o | GPT-4 | GPT-3.5 Turbo Models EP
	public static $AI_IMAGE_EP		= 'https://api.openai.com/v1/images/generations';
		
	//public static $MODEL			= 'gpt-4';	
	public static $MODEL			= 'gpt-3.5-turbo'; 									// Default Model
	public static $IMAGE_MODEL      = 'dall-e-2';										// Default Dall-E-2
 	public static $STREAM			= null;
			
	public static $output			= [];
	
	public function __construct() {		
		if ( is_null( self::$API_KEY ) ) {
			$data = SFTCY_Wordai::sc_wordai_get_current_api_settings();			
			self::$API_KEY = isset( $data['wordai-api-key-data'] ) && ! empty( $data['wordai-api-key-data'] )? $data['wordai-api-key-data'] : null;
		}				
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
	
	/**
	 * Initiate all Hooks
	 */
	private static function initiate_hooks() {			
		self::$initiated = true;
	}
	
	/**
	 * cURL streaming hook
	 */
	public static function sc_curl_add_streaming_hook() {
		add_action('http_api_curl', [ __CLASS__ , 'sc_wordai_stream_response'] );
	}
		
	/**	 
	 * Check Streaming status setting
	 * return - boolean - true|false
	 */
	public static function sc_wordai_streaming_status() {				
        $data = SFTCY_Wordai::sc_wordai_get_current_api_settings();														
		return ( isset( $data['sc-wordai-streaming'] ) && $data['sc-wordai-streaming'] == 1 ) ? true : false;
	}
	/**
	 * Processing to generate valid and meaningful prompt
	 * Send valid prompt to OpenAI API server
	 * return - string - prompt
	 */
	public static function generate_prompt( $prompt_hints = null, $prompt_for =	null ) {		
		$generated_prompt =	'';
		if ( is_null( $prompt_hints) || is_null( $prompt_for) ) {
			return false;
		}
		else {
			$content_settings = SFTCY_Wordai::sc_wordai_get_current_content_settings();									
			$language = array_filter( self::language_list(), function( $lang ) use($content_settings) { 				                                    
												  return ( $content_settings['sc-wordai-language-code'] == $lang['code'] )? true : false ; 
											  } );			
			$language = array_values( $language ); // reset keys
			$language_name = sanitize_text_field( $language[0]['name'] );
			$title_length_readable = sanitize_text_field( self::title_lengths()[ $content_settings['sc-wordai-title-length'] ] );
			$content_length_readable = sanitize_text_field( self::content_paragraphs()[ $content_settings['sc-wordai-content-paragraphs'] ] );
			
						
			switch ( $prompt_for ) {
				case 'title':
					//$generated_prompt		= 'Write article title about ' . sanitize_text_field( $prompt_hints ) . ' in language code ' . $content_settings['language_code'] . '. Style:' . $content_settings['$writing_style_code'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'.Title length will be '. sanitize_text_field( $title_length_readable ) .'.';
					$generated_prompt		= 'Write article title about ' . sanitize_text_field( $prompt_hints ) . ' in ' . $language_name . ' language. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'. Title length will be '. $title_length_readable .'.';					
					break;
					
				case 'suggest-title':															
					$title_s = ( isset( $content_settings['wordai-suggested-title-number'] ) && $content_settings['wordai-suggested-title-number'] > 1 )? ' titles ' : ' title '; 
					//$generated_prompt		= 'Write article ' . sanitize_text_field( $scwordai_suggested_title_number ) .  $title_s . 'about ' . sanitize_text_field( $prompt_hints ) . ' in language code ' . $content_settings['language_code'] . '. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['writing_tone_code'] .'.Title length will be '. sanitize_text_field( $title_length_readable ) .'.';
					$generated_prompt		= 'Write article ' . $content_settings['wordai-suggested-title-number'] .  $title_s . 'about ' . sanitize_text_field( $prompt_hints ) . ' in ' . $language_name . ' language. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'. Title length will be '. $title_length_readable .'.';					
					break;										
					
				case 'content':
					// Inactive
					//$generated_prompt	= 'Write article about ' . $prompt_hints . ' in language code ' . $language_code . '. Style:' . $writing_style_code . '. Tone:' . $writing_tone_code .'.Article length will have '. $content_length_readable .'.Each paragraph with H2 heading title within 300 words.';
					
					// Last Active
					//$generated_prompt		= 'Write article about ' . sanitize_text_field( $prompt_hints ) . ' in language code ' . $content_settings['language_code'] . '. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'.Article length will have '. sanitize_text_field( $content_length_readable ) .'.Each paragraph with heading title within 300 words.';		
					// Last Active
					// $generated_prompt		= 'Write article about ' . sanitize_text_field( $prompt_hints ) . ' in ' . $language_name . ' language. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'. Article length will have '. $content_length_readable .'. Each paragraph with relevant heading within maximum 200 words.';							
					$generated_prompt		= 'Write article about ' . sanitize_text_field( $prompt_hints ) . ' in ' . $language_name . ' language. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'. Article length will have '. $content_length_readable .'. Each paragraph with relevant heading title.';
					
					// Inactive
					//$generated_prompt	= 'Write article within 300 words or less about ' . $prompt_hints . ' in language code ' . $language_code . '. Style:' . $writing_style_code . '. Tone:' . $writing_tone_code .'.Article length will have '. $content_length_readable .'.Each paragraph with heading title.';										
					break;
					
				case 'excerpt':
					$generated_prompt		= 'Write excerpt about ' . sanitize_text_field( $prompt_hints ) . ' in ' . $language_name . ' language. Style:' . $content_settings['sc-wordai-writing-style'] . '. Tone:' . $content_settings['sc-wordai-writing-tone'] .'. Excerpt length will be within ' . $content_settings['sc-wordai-excerpt-words'] .' words.';
					break;
					
				case 'tags':
					$generated_prompt		= 'You have to write comma separated '. $content_settings['sc-wordai-tags-number'] .' tag words on topic '. sanitize_text_field( $prompt_hints ) . ' in ' . $language_name . ' language.';
					break;					
					
				case 'image':
					$image_settings = SFTCY_Wordai::sc_wordai_get_current_image_settings();
					if ( isset( $image_settings['sc-wordai-openai-image-model-slug'] ) && $image_settings['sc-wordai-openai-image-model-slug'] == 'dall-e-2' ) {
						$generated_prompt = $prompt_hints . ' in ' . $image_settings['sc-wordai-dalle2-image-style'] . ' style';	
					}
					else if ( isset( $image_settings['sc-wordai-openai-image-model-slug'] ) && $image_settings['sc-wordai-openai-image-model-slug'] == 'dall-e-3' ) {
						$generated_prompt = $prompt_hints;
					}
					break;
				default:
					break;					
			}			
		}
		return $generated_prompt;
	}
	
	/**
	 * Params ref: https://platform.openai.com/docs/api-reference/chat/create
	 * Get OpenAI API settings params
	 * return - array
	 */
	public static function set_openai_params() {
		$openai_params = [];
		$data = SFTCY_Wordai::sc_wordai_get_current_api_settings();
		// $openai_params['model'] = $data['sc-wordai-openai-model-slug'];
		$openai_params['model'] = ! isset( $data['sc-wordai-openai-model-slug'] ) || empty( $data['sc-wordai-openai-model-slug'] )? self::$MODEL : trim( $data['sc-wordai-openai-model-slug'] );
		$openai_params['max_tokens'] = empty( $data['sc-wordai-max-tokens'] ) ? null : intval( $data['sc-wordai-max-tokens'] ); // Ref: https://platform.openai.com/docs/api-reference/chat/create#chat-create-max_tokens
		$openai_params['temperature'] = floatval( $data['sc-wordai-temperature'] );
		$openai_params['top_p'] =  floatval( $data['sc-wordai-top-p'] );
		$openai_params['presence_penalty'] = floatval( $data['sc-wordai-presence-penalty-input'] );
		$openai_params['frequency_penalty'] = floatval( $data['sc-wordai-frequency-penalty-input'] );
		// $openai_params['stop'] = $data['sc-wordai-stop-input'];		
		$openai_params['stop'] = empty( $data['sc-wordai-stop-input'] )? null : trim( $data['sc-wordai-stop-input'] );	// Ref: https://platform.openai.com/docs/api-reference/chat/create#chat-create-stop	
		
		if ( isset( $data['sc-wordai-streaming'] ) && $data['sc-wordai-streaming'] == 1 ) {
			$openai_params['stream'] = true;
			self::$STREAM = true;
			self::sc_curl_add_streaming_hook();
		}				
		return $openai_params;
	}
	
	/**
	 * Ref: https://platform.openai.com/docs/api-reference/images/create
	 * Get OpenAI API image settings params
	 * Default image model: Dall-E-2
	 * return - array
	 */
	public static function set_openai_image_params() {
		$openai_image_params = [];		
		$data = SFTCY_Wordai::sc_wordai_get_current_image_settings();								
		$openai_image_params['model'] = $data['sc-wordai-openai-image-model-slug'];
		$openai_image_params['n'] = 1; // Per request Generate Single image - Parallel request for multiple images generation
		
		switch ( $openai_image_params['model'] ) {
			case 'dall-e-2':
				$openai_image_params['size'] = $data['sc-wordai-dalle2-image-size'];	
				break;
			case 'dall-e-3':
				$openai_image_params['size'] = $data['sc-wordai-dalle3-image-size'];
				$openai_image_params['style'] = $data['sc-wordai-dalle3-image-style'];
				if ( isset( $data['sc-wordai-dalle3-image-hd-quality'] ) && $data['sc-wordai-dalle3-image-hd-quality'] == 1 ) {
					$openai_image_params['quality'] = 'hd';	
				}
				break;
			default:
				$openai_image_params['size'] = $data['sc-wordai-dalle2-image-size'];	
				break;
		}						
		return $openai_image_params;
	}
	
	
	/**
	 * API call
	 * Fetch OpenAI listmodels
	 */
	public static function get_list_models() {
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . self::$API_KEY,
					'Content-Type'  => 'application/json',
				),
				'timeout' 			=> 60,
				'httpversion' 		=> '1.0',
				'sslverify'   		=> false								
			);

			$response = wp_remote_get( self::$AI_LISTMODEL_EP, $args );

			if ( ! is_wp_error( $response ) ) {
				$body 			= json_decode( wp_remote_retrieve_body( $response ), true );				
				return $body;
			} else {
				$error_message = $response->get_error_message();
				throw new Exception( $error_message );
			}		
	}
	
	/**
	 * Create / Generate Content based on Request
	 * ChatGPT API Call
	 * Check Generated content
	 * If streaming true then 'responseBody' have null value
	 * max_token - NULL - may truncate the response text / content
	 * If max_token value larger than model's maximum support number then response will not be generated
	 * @since 1.0.0
	 */
	public static function create_content( $api_params ) {		  
		self::$output = [];	
		self::$output['wordAIRequestParams'] = $api_params;
    
		if ( is_null( self::$API_KEY ) || empty( self::$API_KEY) ) {
				self::$output['status'] = 'fail';
				self::$output['comment'] = 'Valid API Key missing.';
				self::$output['wordAIAPIKeyMissing'] = __( 'Valid OpenAI API Key required.', 'wordai' );
		} 
		else {
			// Last using $args
			// $args = array(
			// 	'headers' => array(
			// 		'Authorization' => 'Bearer ' . self::$API_KEY,
			// 		'Content-Type'  => 'application/json',
			// 	),
			// 	'timeout' 			=> 90,
			// 	'httpversion' 		=> '1.0',
			// 	'sslverify'   		=> false,
			// 	'body'				=> wp_json_encode( $api_params )			
			// );

			// Modified - $args
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . self::$API_KEY,
					'Content-Type'  => 'application/json; charset=UTF-8',
				),
				'timeout' 			=> 90,
				'httpversion' 		=> '1.0',
				'sslverify'   		=> false,								
				'data_format' 		=> 'body',	
				'body'				=> wp_json_encode( $api_params ),
			);

			// var_dump( $args );
			// exit();

			$response = wp_remote_post( self::$AI_COMPLETION_EP, $args );	// Last using			
			
			
			// var_dump( wp_remote_retrieve_body( $response ) );
			// exit;

			if ( ! is_wp_error( $response ) ) {
				$body = json_decode( wp_remote_retrieve_body( $response ), true );								
				// var_dump( $body );
				// exit;
				self::$output['responseBody'] = $body;
				// response['choices'][0]['message']['content'] - GPT 3.5 Turbo / GPT-4 response format
				// $body["choices"][0]["text"] 					- Old GPT models response format
				if ( isset( $body['choices'][0]['message']['content'] ) ) {					
					self::$output['responseText'] = $body['choices'][0]['message']['content'];
					self::$output['status']	= 'success';
				}
				elseif ( isset( $body["error"]["message"] ) ) {
					self::$output['errorMessage'] = $body["error"]["message"];
					self::$output['errorType']	= $body["error"]["type"];
					self::$output['status']	= 'fail';
				}				
			} else {
				$error_message = $response->get_error_message();				
				self::$output['errorMessage'] = $error_message;
				self::$output['status']	= 'fail';
				// throw new Exception( $error_message );
			}	
		} // else
		
	 return self::$output;
	}
			
	/**
	 * API Call
	 * Generate Images
	 * Ref: https://platform.openai.com/docs/api-reference/images/create
	 * Prompt maximum length is 1000 characters for dall-e-2 and 4000 characters for dall-e-3
	 * Fetch generated image
	 * @params - $api_params - array
	 */
	public static function generate_image( $api_params ) {				    
		    // $body		= [ 'prompt' => 'A tiger in the jungle', 'n' => 1, 'size' => '256x256' ]; // create n=1 image		    		
		    self::$output								= []; 
		    self::$output['wordAIRequestParams']		= $api_params;
		
			if ( is_null( self::$API_KEY ) || empty( self::$API_KEY) ) {
				self::$output['status']					= 'fail';
				self::$output['comment']	  			= 'Valid API Key missing.';
				self::$output['wordAIAPIKeyMissing']	= __( 'Valid OpenAI API Key required.', 'wordai' );
			} 
			else {						
				$args 							= array(
													'headers' => array(
														'Authorization' => 'Bearer ' . self::$API_KEY,
														'Content-Type'  => 'application/json',
													),
													'timeout' 			=> 90,
													'httpversion' 		=> '1.0',
													'sslverify'   		=> false,
													'body'				=> wp_json_encode( $api_params )	
												);

				// var_dump( $args );
				// exit();
				
				$response 								= wp_remote_post( self::$AI_IMAGE_EP, $args );
				if ( ! is_wp_error( $response ) ) {
					$body = json_decode( wp_remote_retrieve_body( $response ), true );
					self::$output['responseBody'] = $body;	
					if ( isset( $body['data'] ) ) {
						self::$output['responseImageUrls'] = $body['data'];
						self::$output['status']	= 'success';
					}
					elseif ( isset( $body["error"]["message"] ) ) {
						self::$output['errorMessage'] = $body["error"]["message"];
						self::$output['errorType'] = $body["error"]["type"];
						self::$output['status']	= 'fail';
					}								
				} else {				
					// throw new Exception( $error_message );
					$error_message = $response->get_error_message();				
					self::$output['errorMessage'] = $error_message;
					self::$output['status'] = 'fail';				
				}		
		} // else
		
	return self::$output;
	}
	
	/**
	 * cURL Hook Callback
	 * @param - $ch - cURL handler
	 * @return  - string - Json string chunk data to browser
	 * Sample Chunks
	 *  data: {"id":"chatcmpl-8OyJgfbdoPuV0h5jyS5LYGXKHQIU1","object":"chat.completion.chunk","created":1700962420,"model":"gpt-3.5-turbo-0613","choices":[{"index":0,"delta":{"content":"The"},"finish_reason":null}]}
	 *  data: {"id":"chatcmpl-8OyJgfbdoPuV0h5jyS5LYGXKHQIU1","object":"chat.completion.chunk","created":1700962420,"model":"gpt-3.5-turbo-0613","choices":[{"index":0,"delta":{},"finish_reason":"stop"}]}
	 *  data: [DONE]											
	 *
	 * @since 1.0.0	 
	 */
	
	// Note:  Last Working Method
	/*public static function sc_wordai_stream_response( $ch ) {		
		if ( self::$STREAM == true ) {						
			@ob_end_clean();
			header('Content-type: text/event-stream');
			header('Cache-Control: no-cache');								    		    
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl_info, $data) { 
				echo self::wordai_raw_contents( $data ); 									
				// ob_flush();
				@ob_end_flush();
    			flush();		
				// ob_end_clean();				
				return strlen($data);
				// return mb_strlen($data);	
            });												
		}
	}*/
	
	// Note:  Last Modified Method
	public static function sc_wordai_stream_response( $ch ) {		
		if ( self::$STREAM == true ) {					
			@ob_end_clean();	
			@ob_start();
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl_info, $data) { 
				// echo wp_kses( self::wordai_raw_contents( $data ), self::wordai_allowed_html_tags() );								
				echo self::wordai_raw_contents( $data ); 									
				@ob_end_flush();
    			flush();						
				return strlen($data);
            });												
		}
	}			
	/**
	 * @param - $data - string
	 * cUrl return partial response data	 
	 * ajaxCal onProgress - Receive raw contents partial data 
	 * @return - string
	 */
	public static function wordai_raw_contents( $data = '' ) {
		return str_replace( 'data: ', '', $data );		
	}


		/**
		 * Allowed html tags for wp_kses
		 * return - array
		 */
		public static function wordai_allowed_html_tags() {
			$allowed_tags = array(
				'img'      => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
					'src'   => array(),
					'alt'   => array(),
				),
				'label'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
					'for'   => array(),
				),
				'div'      => array(
					'id'            => array(),
					'class'         => array(),
					'data-*'        => true,
					'wfd-invisible' => array(),
					'style'         => array(),
				),
				'ul'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'input'    => array(
					'id'            => array(),
					'class'         => array(),
					'type'          => array(),
					'name'          => array(),
					'value'         => array(),
					'checked'       => array(),
					'min'       	=> array(),
					'max'       	=> array(),
					'step'       	=> array(),
					'readonly'      => array(),
					'disabled'      => array(),
					'wfd-invisible' => array(),
					'placeholder'   => array(),
				),
				'textarea' => array(
					'id'            => array(),
					'class'         => array(),
					'name'          => array(),
					'value'         => array(),
					'readonly'      => array(),
					'disabled'      => array(),
					'wfd-invisible' => array(),
					'rows'          => array(),
					'cols'          => array(),
					'placeholder'   => array(),
				),
				'span'     => array(
					'id'    => array(),
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'button'   => array(
					'id'    => array(),
					'class' => array(),
					'type'  => array(),
					'style' => array(),
				),
				'i'        => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'li'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h1'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h2'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h3'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h4'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h5'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h6'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'strong'   => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'b'        => array(
					'id'    => array(),
					'class' => array(),
				),
				'table'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'thead'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'tbody'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'tr'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'td'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'form'     => array(
					'id'      => array(),
					'class'   => array(),
					'name'    => array(),
					'enctype' => array(),
					'method'  => array(),
					'style'   => array(),
				),
				'select'   => array(
					'id'    => array(),
					'class' => array(),
					'name'  => array(),
					'style' => array(),
				),
				'option'   => array(
					'id'       => array(),
					'class'    => array(),
					'value'    => array(),
					'checked'  => array(),
					'selected' => array(),
					'style'    => array(),
				),
				'small'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'hr'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'br'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'p'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				)

			);
			return $allowed_tags;
		}

	
	/**
	 * Language List
	 * return - array
	 */
	public static function language_list() {
		// count 156
		$languages_list = array(
			array("name" => "Afrikaans", "code" => "af"),
			array("name" => "Albanian - shqip", "code" => "sq"),
			array("name" => "Amharic - አማርኛ", "code" => "am"),
			array("name" => "Arabic - العربية", "code" => "ar"),
			array("name" => "Aragonese - aragonés", "code" => "an"),
			array("name" => "Armenian - հայերեն", "code" => "hy"),
			array("name" => "Asturian - asturianu", "code" => "ast"),
			array("name" => "Azerbaijani - azərbaycan dili", "code" => "az"),
			array("name" => "Basque - euskara", "code" => "eu"),
			array("name" => "Belarusian - беларуская", "code" => "be"),
			array("name" => "Bengali - বাংলা", "code" => "bn"),
			array("name" => "Bosnian - bosanski", "code" => "bs"),
			array("name" => "Breton - brezhoneg", "code" => "br"),
			array("name" => "Bulgarian - български", "code" => "bg"),
			array("name" => "Catalan - català", "code" => "ca"),
			array("name" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)", "code" => "ckb"),
			array("name" => "Chinese - 中文", "code" => "zh"),
			array("name" => "Chinese (Hong Kong) - 中文（香港）", "code" => "zh-HK"),
			array("name" => "Chinese (Simplified) - 中文（简体）", "code" => "zh-CN"),
			array("name" => "Chinese (Traditional) - 中文（繁體）", "code" => "zh-TW"),
			array("name" => "Corsican", "code" => "co"),
			array("name" => "Croatian - hrvatski", "code" => "hr"),
			array("name" => "Czech - čeština", "code" => "cs"),
			array("name" => "Danish - dansk", "code" => "da"),
			array("name" => "Dutch - Nederlands", "code" => "nl"),
			array("name" => "English", "code" => "en"),
			array("name" => "English (Australia)", "code" => "en-AU"),
			array("name" => "English (Canada)", "code" => "en-CA"),
			array("name" => "English (India)", "code" => "en-IN"),
			array("name" => "English (New Zealand)", "code" => "en-NZ"),
			array("name" => "English (South Africa)", "code" => "en-ZA"),
			array("name" => "English (United Kingdom)", "code" => "en-GB"),
			array("name" => "English (United States)", "code" => "en-US"),
			array("name" => "Esperanto - esperanto", "code" => "eo"),
			array("name" => "Estonian - eesti", "code" => "et"),
			array("name" => "Faroese - føroyskt", "code" => "fo"),
			array("name" => "Filipino", "code" => "fil"),
			array("name" => "Finnish - suomi", "code" => "fi"),
			array("name" => "French - français", "code" => "fr"),
			array("name" => "French (Canada) - français (Canada)", "code" => "fr-CA"),
			array("name" => "French (France) - français (France)", "code" => "fr-FR"),
			array("name" => "French (Switzerland) - français (Suisse)", "code" => "fr-CH"),
			array("name" => "Galician - galego", "code" => "gl"),
			array("name" => "Georgian - ქართული", "code" => "ka"),
			array("name" => "German - Deutsch", "code" => "de"),
			array("name" => "German (Austria) - Deutsch (Österreich)", "code" => "de-AT"),
			array("name" => "German (Germany) - Deutsch (Deutschland)", "code" => "de-DE"),
			array("name" => "German (Liechtenstein) - Deutsch (Liechtenstein)", "code" => "de-LI"),
			array("name" => "German (Switzerland) - Deutsch (Schweiz)", "code" => "de-CH"),
			array("name" => "Greek - Ελληνικά", "code" => "el"),
			array("name" => "Guarani", "code" => "gn"),
			array("name" => "Gujarati - ગુજરાતી", "code" => "gu"),
			array("name" => "Hausa", "code" => "ha"),
			array("name" => "Hawaiian - ʻŌlelo Hawaiʻi", "code" => "haw"),
			array("name" => "Hebrew - עברית", "code" => "he"),
			array("name" => "Hindi - हिन्दी", "code" => "hi"),
			array("name" => "Hungarian - magyar", "code" => "hu"),
			array("name" => "Icelandic - íslenska", "code" => "is"),
			array("name" => "Indonesian - Indonesia", "code" => "id"),
			array("name" => "Interlingua", "code" => "ia"),
			array("name" => "Irish - Gaeilge", "code" => "ga"),
			array("name" => "Italian - italiano", "code" => "it"),
			array("name" => "Italian (Italy) - italiano (Italia)", "code" => "it-IT"),
			array("name" => "Italian (Switzerland) - italiano (Svizzera)", "code" => "it-CH"),
			array("name" => "Japanese - 日本語", "code" => "ja"),
			array("name" => "Kannada - ಕನ್ನಡ", "code" => "kn"),
			array("name" => "Kazakh - қазақ тілі", "code" => "kk"),
			array("name" => "Khmer - ខ្មែរ", "code" => "km"),
			array("name" => "Korean - 한국어", "code" => "ko"),
			array("name" => "Kurdish - Kurdî", "code" => "ku"),
			array("name" => "Kyrgyz - кыргызча", "code" => "ky"),
			array("name" => "Lao - ລາວ", "code" => "lo"),
			array("name" => "Latin", "code" => "la"),
			array("name" => "Latvian - latviešu", "code" => "lv"),
			array("name" => "Lingala - lingála", "code" => "ln"),
			array("name" => "Lithuanian - lietuvių", "code" => "lt"),
			array("name" => "Macedonian - македонски", "code" => "mk"),
			array("name" => "Malay - Bahasa Melayu", "code" => "ms"),
			array("name" => "Malayalam - മലയാളം", "code" => "ml"),
			array("name" => "Maltese - Malti", "code" => "mt"),
			array("name" => "Marathi - मराठी", "code" => "mr"),
			array("name" => "Mongolian - монгол", "code" => "mn"),
			array("name" => "Nepali - नेपाली", "code" => "ne"),
			array("name" => "Norwegian - norsk", "code" => "no"),
			array("name" => "Norwegian Bokmål - norsk bokmål", "code" => "nb"),
			array("name" => "Norwegian Nynorsk - nynorsk", "code" => "nn"),
			array("name" => "Occitan", "code" => "oc"),
			array("name" => "Oriya - ଓଡ଼ିଆ", "code" => "or"),
			array("name" => "Oromo - Oromoo", "code" => "om"),
			array("name" => "Pashto - پښتو", "code" => "ps"),
			array("name" => "Persian - فارسی", "code" => "fa"),
			array("name" => "Polish - polski", "code" => "pl"),
			array("name" => "Portuguese - português", "code" => "pt"),
			array("name" => "Portuguese (Brazil) - português (Brasil)", "code" => "pt-BR"),
			array("name" => "Portuguese (Portugal) - português (Portugal)", "code" => "pt-PT"),
			array("name" => "Punjabi - ਪੰਜਾਬੀ", "code" => "pa"),
			array("name" => "Quechua", "code" => "qu"),
			array("name" => "Romanian - română", "code" => "ro"),
			array("name" => "Romanian (Moldova) - română (Moldova)", "code" => "mo"),
			array("name" => "Romansh - rumantsch", "code" => "rm"),
			array("name" => "Russian - русский", "code" => "ru"),
			array("name" => "Scottish Gaelic", "code" => "gd"),
			array("name" => "Serbian - српски", "code" => "sr"),
			array("name" => "Serbo - Croatian", "code" => "sh"),
			array("name" => "Shona - chiShona", "code" => "sn"),
			array("name" => "Sindhi", "code" => "sd"),
			array("name" => "Sinhala - සිංහල", "code" => "si"),
			array("name" => "Slovak - slovenčina", "code" => "sk"),
			array("name" => "Slovenian - slovenščina", "code" => "sl"),
			array("name" => "Somali - Soomaali", "code" => "so"),
			array("name" => "Southern Sotho", "code" => "st"),
			array("name" => "Spanish - español", "code" => "es"),
			array("name" => "Spanish (Argentina) - español (Argentina)", "code" => "es-AR"),
			array("name" => "Spanish (Latin America) - español (Latinoamérica)", "code" => "es-419"),
			array("name" => "Spanish (Mexico) - español (México)", "code" => "es-MX"),
			array("name" => "Spanish (Spain) - español (España)", "code" => "es-ES"),
			array("name" => "Spanish (United States) - español (Estados Unidos)", "code" => "es-US"),
			array("name" => "Sundanese", "code" => "su"),
			array("name" => "Swahili - Kiswahili", "code" => "sw"),
			array("name" => "Swedish - svenska", "code" => "sv"),
			array("name" => "Tajik - тоҷикӣ", "code" => "tg"),
			array("name" => "Tamil - தமிழ்", "code" => "ta"),
			array("name" => "Tatar", "code" => "tt"),
			array("name" => "Telugu - తెలుగు", "code" => "te"),
			array("name" => "Thai - ไทย", "code" => "th"),
			array("name" => "Tigrinya - ትግርኛ", "code" => "ti"),
			array("name" => "Tongan - lea fakatonga", "code" => "to"),
			array("name" => "Turkish - Türkçe", "code" => "tr"),
			array("name" => "Turkmen", "code" => "tk"),
			array("name" => "Twi", "code" => "tw"),
			array("name" => "Ukrainian - українська", "code" => "uk"),
			array("name" => "Urdu - اردو", "code" => "ur"),
			array("name" => "Uyghur", "code" => "ug"),
			array("name" => "Uzbek - o‘zbek", "code" => "uz"),
			array("name" => "Vietnamese - Tiếng Việt", "code" => "vi"),
			array("name" => "Walloon - wa", "code" => "wa"),
			array("name" => "Welsh - Cymraeg", "code" => "cy"),
			array("name" => "Western Frisian", "code" => "fy"),
			array("name" => "Xhosa", "code" => "xh"),
			array("name" => "Yiddish", "code" => "yi"),
			array("name" => "Yoruba - Èdè Yorùbá", "code" => "yo"),
			array("name" => "Zulu - isiZulu", "code" => "zu")
		);
		
		return $languages_list;
		
	}

	/**
	 * OpenAI Models
	 * @since 1.0.0
	 */
	public static function openai_models() {
		$openai_models	=	[
			'gpt-3.5-turbo'   => 'GPT-3.5 Turbo',
			'gpt-4'			  => 'GPT-4',
			'gpt-4o'		  => 'GPT-4o',
			'gpt-4o-mini'     => 'GPT-4o-mini'
		];
		
		return $openai_models;
	}
	
	/**
	 * OpenAI Image Models
	 * @since 1.0.0
	 */
	public static function openai_image_models() {
		$openai_image_models	=	[
			'dall-e-2'   => 'DALL E 2',
			'dall-e-3'	 => 'DALL E 3',
		];
		
		return $openai_image_models;
	}
	
	/**
	 * Hard-coded styles
	 * Images Style for DALL E 2
	 * @return - array
	 */
	public static function openai_dalle2_image_styles() {
		$openai_image_styles	=	[
			'vivid'   			=> 'Vivid',
			'natural'	 		=> 'Natural',
			'landscape'         => 'Landscape',
			'portrait'			=> 'Portrait',
			'street'			=> 'Street',
			'architectural'		=> 'Architectural',
			'sports'			=> 'Sports',
			'abstract'			=> 'Abstract',
			'astrophotography' 	=> 'Astrophotography',
			'composite'			=> 'Composite',
			'event'				=> 'Event',
			'editorial'			=> 'Editorial',
			'wedding'			=> 'Wedding',
			'product'			=> 'Product',
			'travel'			=> 'Travel',
			'food'				=> 'Food'			
		];		
		return $openai_image_styles;		
	}
	
	/**
	 * ref: https://platform.openai.com/docs/api-reference/images/create#images-create-style
	 * Style support only Dall E 3
	 * OpenAI Image Styles
	 * @since 1.0.0
	 */
	public static function openai_dalle3_image_styles() {
		$openai_image_styles	=	[
			'vivid'   			=> 'Vivid',
			'natural'	 		=> 'Natural'
		];		
		return $openai_image_styles;
	}
		
	
	/**
	 * Writing styles
	 * @since 1.0.0
	 */
	public static function writing_styles() {
		$writing_styles	=	[
			'narrative' 		=> 'Narrative',
			'descriptive'		=> 'Descriptive',
			'expository'		=> 'Expository',
			'persuasive'		=> 'Persuasive'
		];
		
		return $writing_styles;
	}

	/**
	 * Writing tones
	 * @since 1.0.0
	 */
	public static function writing_tones() {
		$writing_tones	=	[
			'curious'			=> 'Curious',
			'eager'				=> 'Eager',
			'cheerful'			=> 'Cheerful',
			'humorous'			=> 'Humorous',
			'Energetic'			=> 'Energetic',
			'enthusiastic'		=> 'Enthusiastic',
			'informative'		=> 'Informative',
			'knowledgeable'		=> 'Knowledgeable',
			'allusive'			=> 'Allusive',
			'factual'			=> 'Factual',
			'Formal'			=> 'Formal'	
		];
		
		return $writing_tones;
	}
	
	/**
	 * Title length
	 * @since 1.0.0
	 */
	public static function title_lengths() {
		$title_lengths	=	[
			'30n40'				=> 'between 30 & 40 characters',
			'40n50'				=> 'between 40 & 50 characters',
			'50n60'				=> 'between 50 & 60 characters',
		];
		
		return $title_lengths;
	}
   
	/**
	 * Content paragraph
	 */
	public static function content_paragraphs() {
		$content_paragraphs	=	[
			'1'					=> '1 Paragraph',
			'2'					=> '2 Paragraphs',
			'3'					=> '3 Paragraphs',
			'4'					=> '4 Paragraphs',
			'5'					=> '5 Paragraphs',
		];
		
		return $content_paragraphs;
	}

	/**
	 * Excerpt words
	 */
	public static function excerpt_words() {
		$excerpt_words	=	[
			'20'					=> '20 Words',
			'30'					=> '30 Words',
			'40'					=> '40 Words'
		];
		
		return $excerpt_words;
	}
	
	/**
	 * Tags Number
	 */
	public static function tags_number() {
		$tags_number	=	[
			'2'					=> '2 Tags',
			'3'					=> '3 Tags',
			'4'					=> '4 Tags',
			'5'					=> '5 Tags'			
		];		
		return $tags_number;
	}		
	
	
} // End class
}