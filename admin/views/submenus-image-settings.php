<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data = SFTCY_Wordai::sc_wordai_get_current_image_settings();
include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/settings-pages-header-template.php';
$row_show_hide_dalle3 = isset( $data['sc-wordai-openai-image-model-slug'] ) && $data['sc-wordai-openai-image-model-slug'] == 'dall-e-3' ? '' : 'display: none;';
$row_show_hide_dalle2 = isset( $data['sc-wordai-openai-image-model-slug'] ) && $data['sc-wordai-openai-image-model-slug'] == 'dall-e-2' ? '' : 'display: none;';
?> 
 <div id="openai-image-settings-div-wrapper" class="">
  <h3 style="padding: 15px;"><span class="sc-wordai-settings-icon dashicons dashicons-admin-settings"></span><?php esc_html_e('Image Settings', 'wordai');?></h3>  
  <form id="scwordai-image-settings-form" method="post">
   <table style="border-spacing: 15px;">
   		<tbody>   		    
   		    <tr>
   		    	<td colspan="2">
   		    		<button type="submit" class="button button-primary button-large"><i class="fa-regular fa-hard-drive"></i> <?php esc_html_e('Save Image Settings', 'wordai');?></button>
   		    		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   		    		<button type="button" class="button button-primary button-large scwordai-image-apisettings-form-reset-data-btn"><i class="fa-regular fa-window-restore"></i> <?php esc_html_e('Reset Settings', 'wordai');?></button>
   		    		
   		    		<p class="sc-wordai-api-settings-msg"></p>
   		    	</td>
   		    </tr>
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Image Model', 'wordai');?></h4>
   					<?php
					    $openai_image_models = SFTCY_Wordai_OpenAI::openai_image_models();	
						echo '<select id="sc-wordai-openai-image-model-slug" name="sc-wordai-openai-image-model-slug">';
						foreach ( $openai_image_models as $modelslug => $modelname ) {
							$selected = isset( $data['sc-wordai-openai-image-model-slug'] ) ? selected( $data['sc-wordai-openai-image-model-slug'], $modelslug, false) : selected( $modelslug, SFTCY_Wordai_OpenAI::$IMAGE_MODEL );
							echo '<option value="'.  esc_attr( $modelslug ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $modelname ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('DALL E 2 is an AI system that can create realistic images and art from a description in natural language. DALL E 3 understands significantly more nuance and detail than previous models, allowing you to easily translate your ideas into exceptionally accurate images.  DALLE E 3 is the highest quality model and DALL E 2 is optimized for lower cost.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       		    
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('How many Image(s)', 'wordai'); ?></h4>   					   					
   					<?php
					    $image_numbers	= [ 1, 2, 3 ];	
						echo '<select id="sc-wordai-image-number" name="sc-wordai-image-number">';
						foreach ( $image_numbers as $key => $number ) {
							$selected = isset( $data['sc-wordai-image-number'] ) ? selected( $data['sc-wordai-image-number'], $number, false) : selected( $number, 2 );
							echo '<option value="'.  esc_attr( $number ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $number ) . '</option>';							
						}			
						echo '</select>';					
					?>
   					
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('How many image(s) you want to generate.', 'wordai');?></p>
   				</td>   				
   			</tr>   		
   			   			
   			<!-- DALL-E-3 image Size -->
   			<tr class="sc-wordai-dalle3-row" style="<?php echo  esc_attr( $row_show_hide_dalle3 ); ?>" >
   				<td>
   				    <h4><?php esc_html_e('Generated Images Size', 'wordai');?></h4>   					 
   					<?php
					    $image_sizes	=	[ '1024x1024', '1792x1024', '1024x1792' ];
						echo '<select id="sc-wordai-dalle3-image-size" name="sc-wordai-dalle3-image-size">';
						foreach ( $image_sizes as $key => $size ) {
							$selected = isset( $data['sc-wordai-dalle3-image-size'] ) ? selected( $data['sc-wordai-dalle3-image-size'], $size, false) : selected( $size, '1024x1024');
							echo '<option value="'.  esc_attr( $size ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $size ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('The size of the generated images. Must be one of 1024x1024, 1792x1024, or 1024x1792.', 'wordai');?></p>
   				</td>   				
   			</tr>   
   			<!-- DALL-E-3 image Style -->
   			<tr class="sc-wordai-dalle3-row" style="<?php echo  esc_attr( $row_show_hide_dalle3 ); ?>" >
   				<td>
   				    <h4><?php esc_html_e('Style', 'wordai');?></h4>   					 
   					<?php
					    $image_styles	=	SFTCY_Wordai_OpenAI::openai_dalle3_image_styles();
						echo '<select id="sc-wordai-dalle3-image-style" name="sc-wordai-dalle3-image-style">';
						foreach ( $image_styles as $style_slug => $style_name ) {
							$selected = isset( $data['sc-wordai-dalle3-image-style'] ) ? selected( $data['sc-wordai-dalle3-image-style'], $style_slug, false) : selected( $style_slug, 'vivid');
							echo '<option value="'.  esc_attr( $style_slug ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $style_name ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('The style of the generated images. Must be one of vivid or natural. Vivid causes the model to lean towards generating hyper-real and dramatic images. Natural causes the model to produce more natural, less hyper-real looking images. This param is only supported for dall-e-3.', 'wordai');?></p>
   				</td>   				
   			</tr>   	
   			<!-- DALL-E-3 image HD Quality -->
   			<tr class="sc-wordai-dalle3-row" style="<?php echo  esc_attr( $row_show_hide_dalle3 ); ?>" >
   				<td>
  				    <label for="scwordaiHDImagequalityChkbox">
   				    	<h4><?php esc_html_e('Image Quality(HD)', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-dalle3-image-hd-quality" id="scwordaiHDImagequalityChkbox" class="sc-wordai-dalle3-image-hd-quality" <?php checked($data['sc-wordai-dalle3-image-hd-quality'], 1 );?> />
   				    </label>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('The quality of the image that will be generated. HD creates images with finer details and greater consistency across the image. This param is only supported for DALL-E-3.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       			   				   						   			   				   					   						   			
   			<!-- DALL-E-2 image Size -->	
   			<tr class="sc-wordai-dalle2-row" style="<?php echo  esc_attr( $row_show_hide_dalle2 ); ?>" >
   				<td>
   				    <h4><?php esc_html_e('Generated Images Size', 'wordai');?></h4>   					 
   					<?php
					    $image_sizes	=	[ '256x256', '512x512', '1024x1024' ];
						echo '<select id="sc-wordai-dalle2-image-size" name="sc-wordai-dalle2-image-size">';
						foreach ( $image_sizes as $key => $size ) {
							$selected = isset( $data['sc-wordai-dalle2-image-size'] ) ? selected( $data['sc-wordai-dalle2-image-size'], $size, false) : selected( $size, '1024x1024');
							echo '<option value="'.  esc_attr( $size ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $size ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('The size of the generated images. Must be one of 256x256, 512x512, or 1024x1024. Smaller sizes are faster to generate.', 'wordai');?></p>
   				</td>   				
   			</tr>   		
   			<!-- DALL-E-2 image Style -->
   			<tr class="sc-wordai-dalle2-row"  style="<?php echo  esc_attr( $row_show_hide_dalle2 ); ?>" >
   				<td>
   				    <h4><?php esc_html_e('Style', 'wordai');?></h4>   					 
   					<?php
					    $image_styles	=	SFTCY_Wordai_OpenAI::openai_dalle2_image_styles();
						echo '<select id="sc-wordai-dalle2-image-style" name="sc-wordai-dalle2-image-style">';
						foreach ( $image_styles as $style_slug => $style_name ) {
							$selected = isset( $data['sc-wordai-dalle2-image-style'] ) ? selected( $data['sc-wordai-dalle2-image-style'], $style_slug, false) : selected( $style_slug, 'natural');
							echo '<option value="'.  esc_attr( $style_slug ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $style_name ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('DALL E 2 can create original, realistic images and art from a text description. It can combine concepts, attributes, and styles.', 'wordai');?></p>
   				</td>   				
   			</tr>   			   						   			   				   			   			
   			
   			<tr>
   				<td>
  				    <label for="scWordaiImageSaveToGalleryChkbox">
   				    	<h4><?php esc_html_e('Save Image(s) to Gallery', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-imagesave-togallery" id="scWordaiImageSaveToGalleryChkbox" class="sc-wordai-imagesave-togallery" <?php checked($data['sc-wordai-imagesave-togallery'], 1 );?> />
					</label>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('If checked then images will be saved to media gallery automatically when images are created successfully else will have option to save media gallery.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       			
   			<tr>
   				<td>
  				    <label for="scWordaiImageSetAsFeatureChkbox">
   				    	<h4><?php esc_html_e('Set Featured Image', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-set-feature-image" id="scWordaiImageSetAsFeatureChkbox" class="sc-wordai-set-feature-image" <?php checked($data['sc-wordai-set-feature-image'], 1 );?> />
					</label>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('If checked then generated image will be set as featured image for your Post / Page / Product. If more than 1 image then first generated image will be set as featured image.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       			 			     			   			 			  			     			   			 			   			     			   			 			   		  	   			   			   			   			   			
   		</tbody>
   </table>  
  </form>    
</div>
