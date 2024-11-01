<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data =	SFTCY_Wordai::sc_wordai_get_current_content_settings();
$image_data = SFTCY_Wordai::sc_wordai_get_current_image_settings();
?>
<button class="button button-primary button-large metabox-content-writer-btn"><i class="sc-wordai-metabox-button-custom-icon"></i> <?php esc_html_e('WordAI', 'wordai');?></button>

<!-- Metabox button click popup dialog - The modal / dialog box - hidden by default -->
<div id="metabox-button-click-dialog" style="padding: 15px; display: none; background-color: #FEE6FF;">    
   <table class="wordai-metabox-wrapper-table" style="border-spacing: 15px;">
   		<tbody>
   		
   			<tr>
   				<td>   					
   					<p>
   					    <h4 class="wordai-prompt-hints-label"><?php esc_html_e('Prompt / Hints: ', 'wordai');?></h4>
   					    <p class="prompt-alert-msg"></p>
   						<input type="text" name="scwordai-prompt" class="scwordai-prompt" placeholder="Write your prompt / hints words..." />
   					</p>
   					<p>
   					<?php 
						if ( SFTCY_Wordai_OpenAI::sc_wordai_streaming_status() ) {
					?>
						    <button class="button button-primary sc-wordai-generate-content-streaming-btn"><?php esc_html_e('Generate', 'wordai');?></button>
					<?php	
   						}
						else {
					?>		
					        <button class="button button-primary sc-wordai-generate-content-btn"><?php esc_html_e('Generate', 'wordai');?></button>
					<?php				
						}
					?>
					<!-- Cancel Button -->
					<button class="button button-primary sc-wordai-generate-cancel-requests-content-btn" style="display: none;"><?php esc_html_e('Cancel', 'wordai');?></button>		
              														               									
					<!-- Items Generate Options: Title | Content | Excerpt | Tags | Images -->
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="wordAITitleOptionChkBox">
					  <input type="checkbox" id="wordAITitleOptionChkBox" class="sc-wordai-content-generate-options sc-wordai-generate-content-post-title" value="title" checked /> 
					  <span class="sc-wordai-items-label"><?php esc_html_e('Title', 'wordai');?></span>&nbsp;&nbsp;&nbsp;
					</label>
					<label for="wordAIContentOptionChkBox">					
					  <input type="checkbox" id="wordAIContentOptionChkBox" class="sc-wordai-content-generate-options sc-wordai-generate-content-post-content" value="content" checked />
					  <span class="sc-wordai-items-label"><?php esc_html_e('Content', 'wordai');?></span>&nbsp;&nbsp;&nbsp;  					
					</label>
					<label for="wordAIExcerptOptionChkBox">
					  <input type="checkbox" id="wordAIExcerptOptionChkBox" class="sc-wordai-content-generate-options sc-wordai-generate-content-post-excerpt" value="excerpt" checked />
					  <span class="sc-wordai-items-label"><?php esc_html_e('Excerpt', 'wordai');?></span>&nbsp;&nbsp;&nbsp; 
					</label>
					<label for="wordAITagsOptionChkBox">
					  <input type="checkbox" id="wordAITagsOptionChkBox" class="sc-wordai-content-generate-options sc-wordai-generate-content-post-tags" value="tags" checked /> 
					  <span class="sc-wordai-items-label"><?php esc_html_e('Tags', 'wordai');?></span>&nbsp;&nbsp;&nbsp;					 					
					</label>
					<label for="wordAIImagesOptionChkBox">
					  <input type="checkbox" id="wordAIImagesOptionChkBox" class="sc-wordai-content-generate-options sc-wordai-generate-content-post-images" value="images" checked />
 					  <span class="sc-wordai-items-label"><?php esc_html_e('Images', 'wordai');?></span>&nbsp;&nbsp;&nbsp;  						
					</label>
   					</p>
   				</td>   				
   			</tr>
   			
   			<tr class="wave-animation-row" style="display: none;">
   				<td>
   					<div class="sc-wave">
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   						<div class="wave"></div>
   					</div>
   				</td>
   			</tr>
   			
   			<tr>
   				<td>      			
   				    <!-- Title -->	    
   					<div class="scwordai-title" style="display: none;" >  					    
   					    <h5><?php esc_html_e('Generated Title', 'wordai');?></h5>
   						<textarea class="sc-wordai-generated-title" rows="2"></textarea>
   						<br/>
   						<button class="button button-secondary sc-wordai-title-copy-btn" data-clipboard-target=".sc-wordai-generated-title"><?php esc_html_e('Copy Title', 'wordai');?></button>   						
   						<button class="button button-secondary sc-wordai-title-insert-btn"><?php esc_html_e('Insert Title', 'wordai');?></button>
   						<input type="hidden" class="sc-wordai-generated-title-auto-insert-status" value="<?php echo esc_attr( $data['sc-wordai-insert-title'] ); ?>" />
   					</div>		
   					<!-- Content -->	    
   					<div class="scwordai-content" style="display: none;">
   					    <h5><?php esc_html_e('Generated Content', 'wordai');?></h5>
   						<textarea class="sc-wordai-generated-content" rows="10"></textarea> 
   						<br/>  						
   						<button class="button button-secondary sc-wordai-content-copy-btn" data-clipboard-target=".sc-wordai-generated-content"><?php esc_html_e('Copy Content', 'wordai');?></button>   						
   						<button class="button button-secondary sc-wordai-content-insert-btn"><?php esc_html_e('Insert Content', 'wordai');?></button>      						
   						<input type="hidden" class="sc-wordai-replace-withbr-response-format-content" /> 
   						<input type="hidden" class="sc-wordai-generated-content-auto-insert-status" value="<?php echo esc_attr( $data['sc-wordai-insert-content'] ); ?>" />
   					</div>	
   					<!-- Excerpt -->	    
   					<div class="scwordai-excerpt" style="display: none;">
   					    <h5><?php esc_html_e('Generated Excerpt', 'wordai');?></h5>
   						<textarea class="sc-wordai-generated-excerpt" rows="5"></textarea>
   						<br/>   						
   						<button class="button button-secondary sc-wordai-excerpt-copy-btn" data-clipboard-target=".sc-wordai-generated-excerpt"><?php esc_html_e('Copy Excerpt', 'wordai');?></button>   						
   						<button class="button button-secondary sc-wordai-excerpt-insert-btn"><?php esc_html_e('Insert Excerpt', 'wordai');?></button>      						
   						<input type="hidden" class="sc-wordai-replace-withbr-response-format-excerpt" /> 
   						<input type="hidden" class="sc-wordai-generated-excerpt-auto-insert-status" value="<?php echo esc_attr( $data['sc-wordai-insert-excerpt'] ); ?>" />
   					</div>	 
   					<!-- Tags -->	    
   					<div class="scwordai-tags" style="display: none;" >  					    
   					    <h5><?php esc_html_e('Generated Tags', 'wordai');?></h5>
   						<textarea class="sc-wordai-generated-tags" rows="2"></textarea>
   						<br/>
   						<button class="button button-secondary sc-wordai-tags-copy-btn" data-clipboard-target=".sc-wordai-generated-tags"><?php esc_html_e('Copy Tags', 'wordai');?></button>   						
   						<button class="button button-secondary sc-wordai-tags-insert-btn"><?php esc_html_e('Insert Tags', 'wordai');?></button>
   						<input type="hidden" class="sc-wordai-generated-tags-auto-insert-status" value="<?php echo esc_attr( $data['sc-wordai-insert-tags'] ); ?>" />
   					</div>
   					<!-- Images -->	    		   					  					
   					<div class="scwordai-image" style="display: none;">
   					    <h5><?php esc_html_e('Generated Image(s)', 'wordai');?></h5>
   						<div class="sc-wordai-generated-image-wrapper"></div>   			
   						<?php if ( isset( $image_data['sc-wordai-imagesave-togallery'] ) && $image_data['sc-wordai-imagesave-togallery'] == 0 ) { ?>	
   						    <br/>		   						
							<button class="button button-secondary sc-wordai-upload-image-btn"><?php esc_html_e('Save to Gallery', 'wordai');?></button>  
							<span class="save-image-to-gallery-icon dashicons dashicons-format-image" style="display: none;"></span>   						    
   						<?php } ?>  						
   						
   						<input type="hidden" class="sc-wordai-generated-images-urls" />   						
   						<input type="hidden" class="sc-wordai-generated-images-save-to-gallery-status" value="<?php echo esc_attr( $image_data['sc-wordai-imagesave-togallery'] ); ?>" />
   						<input type="hidden" class="sc-wordai-generated-image-set-feature-status" value="<?php echo esc_attr( $image_data['sc-wordai-set-feature-image'] ); ?>" />   		<input type="hidden" class="sc-wordai-will-generate-images-number" value="<?php echo esc_attr( $image_data['sc-wordai-image-number'] ); ?>" />
   					</div>		       					   					   						       					   					
   				</td>
   			</tr>   			   			   			   			   			   			
   		</tbody>
   </table>  
    
</div>
<!-- End metaboc button click popup dialog -->
