<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data = SFTCY_Wordai::sc_wordai_get_current_api_settings();
$buffering_status = SFTCY_Wordai::wordai_check_output_buffering_status();
include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/settings-pages-header-template.php';
?>
<!-- OpenAI API Key -->
<div class="wrap openai-api-key-save-div-wrapper">    
<h4 class="sc-wordai-apikey-require-hints">
<?php 
    $allowed_html = array(
							'a' => array(
								'href' => array(),
								'title' => array(),
								'target' => array()
							),
							'br' => array(),
							'i' => array(
								'class' => array()
							),
							'strong' => array(),
						);
	echo wp_kses( '<i class="fa-brands fa-superpowers fa-xl boost-your-productivity-icon"></i> Boost up your productivity! Humanlike natural & SEO friendly 1-Click content writing solution for your Post/Page/Product. First <a href="https://platform.openai.com/account/api-keys" target="_blank">Get your OpenAI API key</a> & start generating your content & high quality(HD) images automatically.', $allowed_html );
?> 
</h4>
 <form id="wordai-api-key-form" method="post">
  <table>   
  	<tbody>  	 				
  			<tr>
  				<td class="api-key-label-td <?php echo ! empty( $data['wordai-api-key-data'] )? esc_attr('success') : '';?>"><i class="fa-solid fa-key"></i> <?php esc_html_e('OpenAI API Key', 'wordai');?></td>
  				<td class="api-key-input-data-td">
  					<input type="text" name="wordai-api-key-data" class="wordai-api-key-data" value="" placeholder="<?php echo esc_attr( $data['wordai-api-key-mask'] );?>" />
  				</td>
  				<td class="wordai-api-key-show-td">
  					<label for="wordai-api-key-show">
  						<input type="checkbox" id="wordai-api-key-show" name="wordai-api-key-show" class="wordai-api-key-show" /> 
  						<i class="fa-solid fa-eye" title="Show Hide API Key"></i> 
  					</label>  					
  				</td>
  				<td class="sc-wordai-save-apikey-submit-btn-td">
  				   <button type="submit" class="button button-primary sc-wordai-save-apikey-submit-btn" name="sc-wordai-save-apikey-submit-btn"><i class="fa-solid fa-hard-drive"></i> <?php esc_html_e('Save', 'wordai' ); ?></button>
  				</td>  		
				<td class="wordai-api-key-save-feedback-msg-td"></td>					  				
  			</tr>		
  	</tbody>  	
  </table>
  </form>
</div> 

<!-- API Call Parameters -->
<div id="openai-settings-div-wrapper" class="">   
  <h3 style="padding: 15px;">
  	<span class="sc-wordai-settings-icon dashicons dashicons-admin-settings"></span><?php esc_html_e('API Settings', 'wordai');?>
  	&nbsp;&nbsp;<small>( <?php esc_html_e('You can keep all API settings default.', 'wordai')?> )</small>
  </h3>  
   <form id="scwordai-apisettings-form" method="post">
   <table style="border-spacing: 15px;">
   		<tbody>   		    
   		    <tr>
   		    	<td colspan="2">
   		    		<button type="submit" class="button button-primary button-large"><i class="fa-regular fa-hard-drive"></i> <?php esc_html_e('Save API Settings','wordai');?></button>
   		    		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   		    		<button type="button" class="button button-primary button-large scwordai-apisettings-form-reset-data-btn"><i class="fa-regular fa-window-restore"></i> <?php esc_html_e('Reset Settings','wordai');?></button>
   		    		
   		    		<p class="sc-wordai-api-settings-msg"></p>
   		    	</td>
   		    </tr>
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('OpenAI Model', 'wordai');?></h4>
   					<?php
					    $openai_models	=	SFTCY_Wordai_OpenAI::openai_models();	
						echo '<select id="sc-wordai-openai-model-slug" name="sc-wordai-openai-model-slug">';
						foreach ( $openai_models as $modelslug => $modelname ) {
							$selected = isset( $data['sc-wordai-openai-model-slug'] ) ? selected( $data['sc-wordai-openai-model-slug'], $modelslug, false) : selected( $modelslug, SFTCY_Wordai_OpenAI::$MODEL );
							echo '<option value="'.  esc_attr( $modelslug ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $modelname ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('GPT-4o mini, most cost-efficient small model. GPT-4o mini is affordable and intelligent small model for fast, lightweight tasks. GPT-4o mini is cheaper and more capable than GPT-3.5 Turbo. GPT-4o mini enables a broad range of tasks with its low cost and latency. GPT-4o is the latest step in pushing the boundaries of deep learning, in the direction of practical usability. GPT-4o is 2x faster, half the price, and has 5x higher rate limits compared to GPT-4 Turbo. GPT-4 is a large multimodal model that can solve difficult problems with greater accuracy than any of previous models. GPT-4 is available in the OpenAI API to paying customers. The difference between GPT-4 and GPT-3.5 models is not significant. However, in more complex reasoning situations, GPT-4 is much more capable than any of OpenAI previous models. GPT-3.5 Turbo is most capable and cost effective model in the GPT-3.5 family.', 'wordai');?></p>
   				</td>   				
   			</tr>   		    
   			<tr>
   				<td>
  				    <label  for="wordAIStreamingInput">
   				      <h4>
   				    	<?php esc_html_e('Streaming', 'wordai');?>
   				      </h4>
   				      <input type="checkbox" name="sc-wordai-streaming" id="wordAIStreamingInput" class="streaming-input"  <?php checked( $data['sc-wordai-streaming'], 1 );?> />
   				    </label>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('The OpenAI API provides the ability to stream responses back to a client in order to allow partial results for certain requests. If streaming option checked is not working in your WordPress hosted server then just un-check the box and then save & try.', 'wordai');?></p>
   					<?php if ( isset( $buffering_status ) && $buffering_status === false ) { ?>   					
   					<small class="alert-msg"><i class="fa-solid fa-triangle-exclamation fa-beat"></i> <?php esc_html_e('Output buffering is disabled in your server, so streaming enabled may not work properly, if so then un-check streaming option and then try please. Your server administrator / support can resolve it.', 'wordai');?></small>
   					<?php } ?>
   				</td>   				
   			</tr>   		       			
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Temperature', 'wordai');?></h4>
   					<input type="range" name="sc-wordai-temperature" class="sc-temperature-input" value="<?php echo esc_attr( $data['sc-wordai-temperature'] );?>" min="0" max="2" step="0.1" />
   					<p class="range-selected-value"><?php echo esc_html( $data['sc-wordai-temperature'] );?></p>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. Generally recommend altering this or top_p but not both.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('top_p', 'wordai');?></h4>
   					<input type="number" name="sc-wordai-top-p" class="top-p-input" value="<?php echo esc_attr( $data['sc-wordai-top-p'] );?>" /><br/>   					
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.Generally recommend altering this or temperature but not both.', 'wordai');?></p>
   				</td>   				
   			</tr>

   			<tr>
   				<td>
   				    <h4><?php esc_html_e('max_tokens', 'wordai');?></h4>
   					<input type="number" name="sc-wordai-max-tokens" class="max-tokens-input" value="<?php echo esc_attr( $data['sc-wordai-max-tokens'] );?>" /><br/>   					
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('The maximum number of tokens to generate in the completion.The token count of your prompt plus max_tokens cannot exceed the model\'s context length. One token is roughly 4 characters for standard English text. The exact limit varies by model. Better keep it empty to complete your large requests all together.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('presence_penalty', 'wordai');?></h4>
   					<input type="range" name="sc-wordai-presence-penalty-input" class="sc-wordai-presence-penalty-input" value="<?php echo esc_attr( $data['sc-wordai-presence-penalty-input'] );?>" min="-2.0" max="2.0" step="0.1" />
   					<p class="range-selected-value"><?php echo esc_html( $data['sc-wordai-presence-penalty-input'] );?></p>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model\'s likelihood to talk about new topics.', 'wordai');?></p>
   				</td>   				
   			</tr>

   			<tr>
   				<td>
   				    <h4><?php esc_html_e('frequency_penalty', 'wordai');?></h4>
   					<input type="range" name="sc-wordai-frequency-penalty-input" class="sc-wordai-frequency-penalty-input" value="<?php echo esc_attr( $data['sc-wordai-frequency-penalty-input'] );?>" min="-2.0" max="2.0" step="0.1" />
   					<p class="range-selected-value"><?php echo esc_html( $data['sc-wordai-frequency-penalty-input'] );?></p>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model\'s likelihood to repeat the same line verbatim.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			  
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('best_of', 'wordai');?></h4>
   					<input type="number" name="sc-wordai-best-of-input" class="best-of-input" value="<?php echo esc_attr( $data['sc-wordai-best-of-input'] );?>" /><br/>   					
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('Generates best_of completions server-side and returns the "best" (the one with the highest log probability per token). Results cannot be streamed.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			   			 			

   			<tr>
   				<td>
   				    <h4><?php esc_html_e('stop', 'wordai');?></h4>
   					<input type="text" name="sc-wordai-stop-input" class="stop-input" value="<?php echo esc_attr( $data['sc-wordai-stop-input'] );?>" /><br/>   					
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('Up to 4 sequences where the API will stop generating further tokens. The returned text will not contain the stop sequence.', 'wordai');?></p>
   				</td>   				
   			</tr>   		     			   			   			   			   			
   		</tbody>
   </table>  
    </form>	
</div>