<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data =	SFTCY_Wordai::sc_wordai_get_current_content_settings();
?>
<!-- Start Suggest titles popup dialog -->
<div id="suggest-titles-button-click-dialog" style="padding: 15px; display: none; background-color: #FEE6FF;">    
   <table style="border-spacing: 15px;">
   		<tbody>   		
   			<tr>
   				<td>   					
   					<p>
   					    <h4><?php esc_html_e('Prompt / Hints', 'wordai');?></h4>
   					    <p class="prompt-alert-msg"></p>
   						<input type="text" name="scwordai-suggest-title-prompt" class="scwordai-suggest-title-prompt" placeholder="<?php esc_html_e('Write your prompt for title...', 'wordai');?>" style="min-width: 500px; height: 50px;"  />
   					</p>
   					
   					<p>
   					    <?php 
						if ( SFTCY_Wordai_OpenAI::sc_wordai_streaming_status() ) {
							echo '<button class="button button-primary sc-wordai-write-suggest-titles-streaming-btn">' . esc_html__('Suggest Title(s)','wordai') . '</button>';
						} 
						else {
							echo '<button class="button button-primary sc-wordai-write-suggest-titles-btn">' . esc_html__('Suggest Title(s)','wordai') . '</button>';
						}
						?>   						   						   						   						
  						<button class="button button-primary sc-wordai-write-suggest-titles-cancel-btn" style="display: none;"><?php esc_html_e('Cancel','wordai'); ?></button>
  						
   						<select id="scwordai-suggested-titles-number" name="wordai-suggested-title-number">
   							<?php for( $i = 1; $i < 4; $i++ ) {
	                                $selected = isset( $data['wordai-suggested-title-number'] ) ? selected( $data['wordai-suggested-title-number'], $i, false) : selected( $i, 2 );
   									echo '<option value="'. esc_attr( $i ) .'" ' . esc_attr( $selected ) . '>'. esc_html( $i ) .'</option>';
   							 }?>
   						</select> 						   						
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
   					<div class="scwordai-suggested-title-wrapper" style="display: none;" >  					    
   					    <h5><?php esc_html_e('Generated Title(s)', 'wordai');?></h5>
   						<ul class="sc-wordai-suggested-titles-list">
   							<!--<li><input type="radio" class="suggested-title-radio" name="suggested-title-radio" /> First title</li>
   							<li><input type="radio" class="suggested-title-radio" name="suggested-title-radio" /> Second title</li>
   							<li><input type="radio" class="suggested-title-radio" name="suggested-title-radio" /> Third title</li>-->
   						</ul>   						
   						<button class="button button-secondary sc-wordai-suggested-title-update-btn"><?php esc_html_e('Update Title', 'wordai');?></button>   
   						<span class="update-suggested-title-msg"></span>						   						
   					</div>		
   				</td>
   			</tr>   			   			   			   			   			   			
   		</tbody>
   </table>  
    
</div>
<!-- End Suggest titles popup dialog -->
