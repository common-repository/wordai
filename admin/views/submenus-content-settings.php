<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data =	SFTCY_Wordai::sc_wordai_get_current_content_settings();
include_once SFTCY_WORDAI_PLUGIN_DIR . 'admin/views/settings-pages-header-template.php';
?>
 
 <div id="openai-content-settings-div-wrapper" class="">
  <h3 style="padding: 15px;"><span class="sc-wordai-settings-icon dashicons dashicons-admin-settings"></span><?php esc_html_e('Content Settings', 'wordai');?></h3>  
  <form id="scwordai-content-settings-form" method="post">
   <table style="border-spacing: 15px;">
   		<tbody>   		    
   		    <tr>
   		    	<td colspan="2">
   		    		<button type="submit" class="button button-primary button-large"><i class="fa-regular fa-hard-drive"></i> <?php esc_html_e('Save Content Settings', 'wordai');?></button>
   		    		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   		    		<button type="button" class="button button-primary button-large scwordai-content-apisettings-form-reset-data-btn"><i class="fa-regular fa-window-restore"></i> <?php esc_html_e('Reset Settings', 'wordai');?></button>
   		    		   		    		
   		    		<p class="sc-wordai-api-settings-msg"></p>
   		    	</td>
   		    </tr>
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Language', 'wordai'); ?></h4>   					   					
   					<?php
					    $languages	=	SFTCY_Wordai_OpenAI::language_list();	
						echo '<select id="sc-wordai-language-code" name="sc-wordai-language-code">';
						foreach ( $languages as $key => $lang ) {
							$selected = isset( $data['sc-wordai-language-code'] ) ? selected( $data['sc-wordai-language-code'], $lang['code'], false) : selected( $lang['code'], 'en-US');
							echo '<option value="'.  esc_attr( $lang['code'] ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $lang['name'] ) . '</option>';							
						}			
						echo '</select>';					
					?>
   					
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('Select the language you want WordAI to write your content.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Writing Style', 'wordai');?></h4>   					 
   					<?php
					    $writing_styles	= SFTCY_Wordai_OpenAI::writing_styles();	
						echo '<select id="sc-wordai-writing-style" name="sc-wordai-writing-style">';
						foreach ( $writing_styles as $stylecode => $stylename ) {
							$selected = isset( $data['sc-wordai-writing-style'] ) ? selected( $data['sc-wordai-writing-style'], $stylecode, false) : selected( $stylecode, 'descriptive');
							echo '<option value="'.  esc_attr( $stylecode ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $stylename ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('An article is an effective format to package and deliver information to a larger audience. Depending on its purpose, an article will most likely fit into one of four types: expository, persuasive, narrative, or descriptive.', 'wordai');?></p>
   				</td>   				
   			</tr>

   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Writing Tone', 'wordai');?></h4>
   					<?php
					    $writing_tones	= SFTCY_Wordai_OpenAI::writing_tones();	
						echo '<select id="sc-wordai-writing-tone" name="sc-wordai-writing-tone">';
						foreach ( $writing_tones as $tonecode => $tonename ) {
							$selected   = isset( $data['sc-wordai-writing-tone'] ) ? selected( $data['sc-wordai-writing-tone'], $tonecode, false) : selected( $tonecode, 'informative');
							echo '<option value="'.  esc_attr( $tonecode ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $tonename ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('There are as many tones in writing as there are human emotions. The differences between these tones are the context, syntax, and diction that authors employ to cultivate personalities and emotions in characters or to appeal to their readers.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Title Length', 'wordai');?></h4>
   					<?php
					    $title_lengths	=	SFTCY_Wordai_OpenAI::title_lengths();	
						echo '<select id="sc-wordai-title-length" name="sc-wordai-title-length">';
						foreach ( $title_lengths as $title_length_code => $title_length_name ) {
							$selected = isset( $data['sc-wordai-title-length']) ? selected( $data['sc-wordai-title-length'], $title_length_code, false) : selected( $title_length_code, '30n40');
							echo '<option value="'.  esc_attr( $title_length_code ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $title_length_name ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('Title length will be between the selected characters long.', 'wordai');?></p>
   				</td>   				
   			</tr>

   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Content Paragraph number', 'wordai');?></h4>
   					<?php
					    $content_paragraphs	=	SFTCY_Wordai_OpenAI::content_paragraphs();	
						echo '<select id="sc-wordai-content-paragraphs" name="sc-wordai-content-paragraphs">';
						foreach ( $content_paragraphs as $content_paragraph_code => $content_paragraph_name ) {
							$selected = isset( $data['sc-wordai-content-paragraphs']) ? selected( $data['sc-wordai-content-paragraphs'], $content_paragraph_code, false) : selected( $content_paragraph_code, '3');
							echo '<option value="'.  esc_attr( $content_paragraph_code ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $content_paragraph_name ) . '</option>';							
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('How many paragraph you want to generate for your article / content.', 'wordai');?></p>
   				</td>   				
   			</tr>
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Excerpt Words Length', 'wordai');?></h4>
   					<?php
					    $excerpt_words	=	SFTCY_Wordai_OpenAI::excerpt_words();	
						echo '<select id="sc-wordai-excerpt-words" name="sc-wordai-excerpt-words">';
						foreach ( $excerpt_words as $excerpt_words_code => $excerpt_words_name ) {
							$selected = isset( $data['sc-wordai-excerpt-words'] ) ? selected( $data['sc-wordai-excerpt-words'], $excerpt_words_code, false) : selected( $excerpt_words_code, '30');
							echo '<option value="'.  esc_attr( $excerpt_words_code ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $excerpt_words_name ) . '</option>';
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('How many excerpt words you want to generate for your Post / Page / Product.', 'wordai');?></p>
   				</td>   				
   			</tr>  
   			<tr>
   				<td>
   				    <h4><?php esc_html_e('Tags Number', 'wordai');?></h4>
   					<?php
					    $tags_number	= SFTCY_Wordai_OpenAI::tags_number();	
						echo '<select id="sc-wordai-tags-number" name="sc-wordai-tags-number">';
						foreach ( $tags_number as $tags_number_code => $tags_number_name ) {
							$selected = isset( $data['sc-wordai-tags-number'] ) ? selected( $data['sc-wordai-tags-number'], $tags_number_code, false) : selected( $tags_number_code, '2');
							echo '<option value="'.  esc_attr( $tags_number_code ) .'" ' . esc_attr( $selected ) . '>' . esc_html( $tags_number_name ) . '</option>';
						}			
						echo '</select>';					
					?>   					 
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('How many Tag words you want to generate for your Post / Page / Product.', 'wordai');?></p>
   				</td>   				
   			</tr>  			     			   	   			     			   	   						     			   	   			     			   	
   			<tr>
   				<td>
  				    <label for="scWordaiautoInsertTitleChkbox">
   				    	<h4><?php esc_html_e('Insert Title', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-insert-title" id="scWordaiautoInsertTitleChkbox" class="sc-wordai-insert-title" <?php checked( $data['sc-wordai-insert-title'], 1 );?> />
   				    </label>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('If checked then Title will be inserted in your Post / Page / Product editor when generation complete.', 'wordai');?></p>
   				</td>   				
   			</tr>   		 
   			<tr>
   				<td>
  				    <label for="scWordaiInsertContentChkbox">
   				    	<h4><?php esc_html_e('Insert Content', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-insert-content" id="scWordaiInsertContentChkbox" class="sc-wordai-insert-content" <?php checked( $data['sc-wordai-insert-content'], 1 );?> />
   				    </label>
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('If checked then Content will be inserted in your Post / Page / Product editor when generation complete.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       			
   			<tr>
   				<td>
  				    <label for="scWordaiInsertExcerptChkbox">
   				    	<h4><?php esc_html_e('Insert Excerpt', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-insert-excerpt" id="scWordaiInsertExcerptChkbox" class="sc-wordai-insert-excerpt" <?php checked( $data['sc-wordai-insert-excerpt'], 1 );?> />
					</label>	
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('If checked then Excerpt will be inserted in your Post / Product editor when Excerpt generation complete.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       			
   			<tr>
   				<td>
  				    <label for="scWordaiInsertTagsChkbox">
   				    	<h4><?php esc_html_e('Insert Tags', 'wordai');?></h4>
   				    	<input type="checkbox" name="sc-wordai-insert-tags" id="scWordaiInsertTagsChkbox" class="sc-wordai-insert-tags" <?php checked( $data['sc-wordai-insert-tags'], 1 );?> />
					</label>	
   				</td>
   				<td>   					
   					<p class="sc-wordai-settings-text-hints"><?php esc_html_e('If checked then Tags will be inserted in your Post / Page / Product editor when Tags generation complete.', 'wordai');?></p>
   				</td>   				
   			</tr>   		       			
   		</tbody>
   </table>  
  </form>	 
</div>
