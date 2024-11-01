var scWordAIUploadImagesWithSavetoImageGallery;
var scWordAIGlobalAjax;
jQuery(document).ready(function($) {		
	// console.log('Localized WordAI Misc Script Data');	
	// console.log( sftcy_wordai_metabox_script_obj );	
	
	var scSuggestTitleXHR =	'';
	var scWordAIHideWaveAnimationWithSuggesTitleButtonActivation;	
	
	// Global Ajax call 
	scWordAIGlobalAjax = function(params, actionName ) {							
		return new Promise( ( resolve, reject ) => {
			    $.ajax({
				  type:"POST",				  
				  url: sftcy_wordai_metabox_script_obj.adminajax_url,
				  data : {			    
						action 	 : actionName,
						security : sftcy_wordai_metabox_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 					 
					 resolve( data ); 					 
					},
				  error: function( xhr, status, error ) { 					 
					 reject( error ); 
					}
				});
		}); // Promise						
	};
	
	
	// Write Suggest Titles button Click - Api call - inside suggest titles popup dialog
	$('body').on( "click", ".sc-wordai-write-suggest-titles-btn", function() {
		let promptAlertMsg							= $('.prompt-alert-msg');
		let waveAnimationRow						= $('.wave-animation-row');
		let scWordAiSuggesTitlesBtn					= $('.sc-wordai-write-suggest-titles-btn');
		let scWordAiSuggesTitlesCancelStreamingBtn	= $('.sc-wordai-write-suggest-titles-cancel-btn');
		let params 									= {};
		params.prompt								= $('.scwordai-suggest-title-prompt').val();		
		
		if ( $.trim( params.prompt).length == 0 ) {
			promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_metabox_script_obj.write_ur_prompt+'</span>');
		} 
		else {
			scWordAiSuggesTitlesBtn.fadeOut( 400, function() { scWordAiSuggesTitlesCancelStreamingBtn.fadeIn(); });			
			waveAnimationRow.fadeIn(300);
						
			scSuggestTitleXHR = $.ajax({
			  type:"POST",
			  cache: false,
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'sc_wordai_write_suggest_titles',
					security : sftcy_wordai_metabox_script_obj.nonce,
					params   : params
					},		  
			  success: function(data) { 
				//   console.log(data); 
				  let jsonData	= JSON.parse( data );
				  scWordAIHideWaveAnimationWithSuggesTitleButtonActivation();
				  
				  if ( jsonData.status == 'success' ) {
					promptAlertMsg.html('<span class="alert-success">'+sftcy_wordai_metabox_script_obj.generated_title_success+'</span>');
					$('.sc-wordai-suggested-titles-list').html( jsonData.listOfTitles );					  
					waveAnimationRow.fadeOut(300);  
					// Show fetched Titles  
					$('.scwordai-suggested-title-wrapper').fadeIn(300);  
				  }
				  else if ( jsonData.status == 'fail' ) {					  
					  promptAlertMsg.html( '<span class="alert-error">'+ jsonData.errorMessage +'</span>' );					  
					  waveAnimationRow.fadeOut(300);  
				  }
				  else {
					  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_metabox_script_obj.something_went_wrong+'</span>');
					  waveAnimationRow.fadeOut(300);  
				  }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 
				 scWordAIHideWaveAnimationWithSuggesTitleButtonActivation(); 
				}
			});
		}
				
	});
		
	// Write Suggest Titles button Click - Api call - Streaming - inside suggest titles popup dialog
	$('body').on( "click", ".sc-wordai-write-suggest-titles-streaming-btn", function() {
		let waveAnimationRow						= $('.wave-animation-row');
		let promptAlertMsg							= $('.prompt-alert-msg');
		let scWordAiSuggesTitlesBtn					= $('.sc-wordai-write-suggest-titles-streaming-btn');
		let scWordAiSuggesTitlesCancelStreamingBtn	= $('.sc-wordai-write-suggest-titles-cancel-btn');
		let params 									= {};
		params.prompt								= $('.scwordai-suggest-title-prompt').val();		
		
		let scChunkResponse							= '';
		let scListedChunkResponse   				= [];
		let scListedChunkProgressResponse   		= [];
		let scLIFormattedTitles						= '';
		let scEachSanitizedChunk					= '';
		let scCompleteResponse						= '';
		let scWordAIResponseErrorMessage			= '';
		
		if ( $.trim( params.prompt).length == 0 ) {
			promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_metabox_script_obj.write_ur_prompt+'</span>');
		} 
		else {		
			scWordAiSuggesTitlesBtn.fadeOut( 400, function() { scWordAiSuggesTitlesCancelStreamingBtn.fadeIn(); });
			waveAnimationRow.fadeIn(300);
			
			
			  scSuggestTitleXHR = $.ajax({
			  type:"POST",
			  cache: false,
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    					
				    action 	 : 'sc_wordai_write_suggest_titles',
					security : sftcy_wordai_metabox_script_obj.nonce,
					params   : params
					},	
			 xhrFields: {					
					onprogress: function (e) {
						waveAnimationRow.fadeOut();
						scCompleteResponse		= '';
						scLIFormattedTitles		= '';						
						//console.log(e);
						// console.log('Progress [Response Chunk]...');												
						scChunkResponse		=	e.target.response;						
						//console.log('Chunk Raw API Response:');
						//console.log(scChunkResponse);
						scCompleteResponse				= scWordAIProcessGroupOfChunkResponse(scChunkResponse); 
						//console.log('scCompleteResponse: ');
						//console.log(scCompleteResponse);						
						scWordAIResponseErrorMessage	= ( scCompleteResponse[0] !== undefined && scCompleteResponse[0].error !== undefined )? scCompleteResponse[0].errorData : ''; 
						scListedChunkResponse			= scCompleteResponse.split('\n');
						//console.log(scListedChunkResponse);
						
					    for ( let i = 0; i < scListedChunkResponse.length; i++ ) {
						  //console.log( scListedChunkResponse[i] );						  
						  scEachSanitizedChunk		=	scListedChunkResponse[i].replace(/^\d\.\s\"|^\"/g, '').replace(/\"$|\s$/g, '');							  
						  if ( scEachSanitizedChunk.length !== 0 ) {
							  let wordAISuggestTitleRadioInputId = 'WordAISuggesTitleRadioBox-' + (i+1);
							  scLIFormattedTitles	+= '<li><label for="' + wordAISuggestTitleRadioInputId + '"><input type="radio" id="' + wordAISuggestTitleRadioInputId + '" class="suggested-title-radio" name="suggested-title-radio" /> ' + $.trim( scEachSanitizedChunk ) + '</label></li>';
						  }	
					    }
												
						$('.scwordai-suggested-title-wrapper').fadeIn();
						$('.sc-wordai-suggested-titles-list').html( scLIFormattedTitles );						
					}
				},				
			  success: function(data) { 				  				  
				  scWordAIHideWaveAnimationWithSuggesTitleButtonActivation();
				//   console.log('Finally Done');
				  if ( scWordAIResponseErrorMessage.length != 0 ) {
					  promptAlertMsg.html( '<span class="alert-error">'+ scWordAIResponseErrorMessage +'</span>' );
				  }
				  else {
				  	promptAlertMsg.html('<span class="alert-success">'+sftcy_wordai_metabox_script_obj.generated_title_success+'</span>');
				  }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 				 				 
				 scWordAIHideWaveAnimationWithSuggesTitleButtonActivation();
				}
			});
		}				
	});
	
	// Hide wave animation - with Button activation - when requests complete
	scWordAIHideWaveAnimationWithSuggesTitleButtonActivation	=	function() {
		let scWordAiSuggesTitlesCancelStreamingBtn	= $('.sc-wordai-write-suggest-titles-cancel-btn');		
		let waveAnimationRow						= $('.wave-animation-row');
		let scWordAiSuggesTitlesBtn					= $('.sc-wordai-write-suggest-titles-btn');
		let scWordAiSuggesTitlesStreamingBtn		= $('.sc-wordai-write-suggest-titles-streaming-btn');
		if ( scSuggestTitleXHR.length !== 0 ) { scSuggestTitleXHR.abort(); }		  
		scWordAiSuggesTitlesCancelStreamingBtn.fadeOut( 100, function() {
			waveAnimationRow.fadeOut();
			scWordAiSuggesTitlesBtn.fadeIn();
			scWordAiSuggesTitlesStreamingBtn.fadeIn();
		});		
	}
	
	
	// Write Suggest Titles button Click - Api call - inside suggest titles popup dialog - Cancel Requests Button Click
	$('body').on("click", ".sc-wordai-write-suggest-titles-cancel-btn", function() {	
		scWordAIHideWaveAnimationWithSuggesTitleButtonActivation();
	});
	
	
	// Update Suggested Title - Button inside suggested title popup window - Replace the old Title
	$('body').on("click", ".sc-wordai-suggested-title-update-btn", function() {
		let updateSuggestedtitleFeedback	= $('.update-suggested-title-msg');			
		let postID			=	$(this).data('scwordai-updatebtn-postid');
		let selectedTitle	= '';
		updateSuggestedtitleFeedback.html('');	
		$('.suggested-title-radio').each( function() {
			if ( $(this).is(":checked") ) {
				selectedTitle			=	$(this).parent().text();
				// console.log(selectedTitle);
				// console.log(postID);
				let params	=	{};
				params.selectedTitle	= selectedTitle;
				params.postID			= postID;

				$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_metabox_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_update_suggest_title',
						security : sftcy_wordai_metabox_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 
					//  console.log(data); 
					 let jsonData	= JSON.parse( data );
					  if ( jsonData.status == 'success' ) {
						$('#post-'+postID+' .row-title' ).html(selectedTitle).addClass('alert-change');  
						setTimeout(function() { $('#post-'+postID+' .row-title' ).html(selectedTitle).removeClass('alert-change');}, 3000);  
						updateSuggestedtitleFeedback.html('<span class="alert-success">'+sftcy_wordai_metabox_script_obj.updated_title_success+'</span>');						
					  }
					  else if ( jsonData.status == 'fail' ) {					  
						  updateSuggestedtitleFeedback.html( '<span class="alert-error">'+ jsonData.errorMessage +'</span>' );					  					  					  
					  }
					  else {
						  updateSuggestedtitleFeedback.html('<span class="alert-error">'+sftcy_wordai_metabox_script_obj.something_went_wrong+'</span>');						  
					  }
					},
				  error: function( xhr, status, error ) { 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 					 					 
					}
				});								
			}		 			
		});
		
		$('.suggested-title-radio').promise().done(function() {		
				// If not suggested Title is selected - Remind
				if ( selectedTitle.length == 0 ) {
					updateSuggestedtitleFeedback.html('<span class="alert-remind">'+sftcy_wordai_metabox_script_obj.updated_title_not_selected+'</span>');
				}
		});			
		
		// Reset / Empty updated messages
		setTimeout(function() { updateSuggestedtitleFeedback.html(''); }, 4000 );				
	});			
	
	// Upload image to media Gallery - Save to Gallery Button based on Click - Set featured image - Manually
	$('body').on( "click", ".sc-wordai-upload-image-btn", function() {
		let promptAlertMsg			= $('.prompt-alert-msg');
		let scWordAiUploadImgBtn	= $('.sc-wordai-upload-image-btn');
		let saveImageToGalleryIcon	= $('.save-image-to-gallery-icon');
		let setFeatureImageStatus	= $('.sc-wordai-generated-image-set-feature-status').val();
		promptAlertMsg.html("");
		let params 					= {};
		params.prompt				= $('.scwordai-prompt').val();	
		params.imgURLs				= $('.sc-wordai-generated-images-urls').val();
		//params.postID				= wp.data.select("core/editor").getCurrentPostId();	
		//params.postType			= wp.data.select("core/editor").getCurrentPostType();			
		if ( $.trim( params.prompt).length == 0 ) {
			promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_metabox_script_obj.write_ur_prompt+'</span>');
		} 
		else {		
			$(this).attr("disabled", "disabled" );
			saveImageToGalleryIcon.fadeIn(300);
			
			$.ajax({
			  type:"POST",
			  cache: false,
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'sc_wordai_upload_image_to_wp_media',
					security : sftcy_wordai_metabox_script_obj.nonce,
					params   : params
					},		  
			  success: function(data) { 
				//   console.log(data); 				  
				  let jsonData			= JSON.parse( data );
				  if ( jsonData.status == 'success' ) {
					promptAlertMsg.html('<span class="alert-success">'+sftcy_wordai_metabox_script_obj.images_saved_to_gallery+'</span>');										
					scWordAiUploadImgBtn.removeAttr('disabled');					
					saveImageToGalleryIcon.fadeOut(300);  	
					// Set Featured Image  
					if ( setFeatureImageStatus == 1 ) {  
						switch( sftcy_wordai_metabox_script_obj.current_posttype.toLowerCase() ) {
							case 'product':
								try {
									wp.media.featuredImage.set( jsonData.firstImageAttachmentID ); // set featured image the first one generated
									$('#set-post-thumbnail').trigger('click');
									$('.button.media-button').trigger("click"); 								
								}
								catch(error) {
									console.log('Set Featured Image['+params.postType+'] Error: '+error );
								}
								break;
							case 'post':
							case 'page':
								try {
									wp.data.dispatch( 'core/editor' ).editPost({featured_media: jsonData.firstImageAttachmentID });
								}
								catch(error) {
									console.log('Manual set featured Image Error: '+error );
								}								
								break;
							default:
								console.log('Currently supporting Post/Page/product.');
								break;
						}						
					}					  					  
				  }
				  else if ( jsonData.status == 'fail' ) {					  
					  promptAlertMsg.html( '<span class="alert-error">'+ jsonData.errorMessage +'</span>' );					  					  					  
					  scWordAiUploadImgBtn.removeAttr('disabled');					  
					  saveImageToGalleryIcon.fadeOut(300);  
				  }
				  else {
					  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_metabox_script_obj.something_went_wrong+'</span>');
					  saveImageToGalleryIcon.fadeOut(300);  
				  }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 				 
				 scWordAiUploadImgBtn.removeAttr('disabled');
				 saveImageToGalleryIcon.fadeOut(300);  
				}
			});
		}
				
	});
	
	// Upload image to media button Click - Save to Gallery - Set Featured Image - Automatically
	scWordAIUploadImagesWithSavetoImageGallery = function() {	
		let promptAlertMsg			= $('.prompt-alert-msg');		
		let params 					= {};
		params.prompt				= $('.scwordai-prompt').val();	
		params.imgURLs				= $('.sc-wordai-generated-images-urls').val();	
		let setFeatureImageStatus	= $('.sc-wordai-generated-image-set-feature-status').val();
		
		$.ajax({
		  type:"POST",		  
		  url: sftcy_wordai_metabox_script_obj.adminajax_url,
		  data : {			    
				action 	 : 'sc_wordai_upload_image_to_wp_media',
				security : sftcy_wordai_metabox_script_obj.nonce,
				params   : params
				},		  
		  success: function(data) { 
			//console.log(data); 			 	   
			let jsonData			= JSON.parse( data );
			// console.log(jsonData);
			if ( jsonData.status == 'success' ) {					
					promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_metabox_script_obj.images_saved_to_gallery+'</span><br/>');			
					// Set Featured Image  
					if ( setFeatureImageStatus == 1 ) {  
						switch( sftcy_wordai_metabox_script_obj.current_posttype.toLowerCase() ) {
							case 'product':
								try {
									wp.media.featuredImage.set( jsonData.firstImageAttachmentID ); // set featured image the first one generated
									$('#set-post-thumbnail').trigger('click');
									$('.button.media-button').trigger("click"); 								
								}
								catch(error) {
									console.log('Set Featured Image['+params.postType+'] Error: '+error );
								}
								break;
							case 'post':
							case 'page':
								try {
									wp.data.dispatch( 'core/editor' ).editPost({featured_media: jsonData.firstImageAttachmentID });
								}
								catch(error) {
									console.log('Automatically set featured Image Error: '+error );
								}								
								break;
							default:
								console.log('Currently supporting Post/Page/product.');
								break;
						}						
					}					  					  
				  
					/*scWordAISetFeaturedImage( jsonData.firstImageAttachmentID ).then( (attachmentID) => {
							try {					
								$('.components-button.edit-post-sidebar__panel-tab:first').trigger( "click");  					
								$('.components-button.editor-post-featured-image__toggle, .components-button.editor-post-featured-image__preview').trigger("click");						$('.button.media-button').trigger("click"); 
								console.log('[scWordAIUploadImagesWithSavetoImageGallery] Clicked to set Featured Image.');
							}
							catch(error) {
								console.log('[scWordAIUploadImagesWithSavetoImageGallery] Set Featured Image Click Process Error: '+ error );
							}


					}).catch( (error) =>  {
							console.log('[scWordAIUploadImagesWithSavetoImageGallery] Error to set featured Image. ID: '+ error );
					});*/				
			  }
			  else if ( jsonData.status == 'fail' ) {					  
				  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );					  					  					  
			  }
			  else {
				  promptAlertMsg.append('<span class="alert-error">'+sftcy_wordai_metabox_script_obj.something_went_wrong_images_save+'</span><br/>');					  
			  }
			},
		  error: function( xhr, status, error ) { 
			 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 				 
			}
		});		
	}
	
	// Set Featured Image 
	/*function scWordAISetFeaturedImage( firstImageAttachmentID ) {
		return new Promise( (resolve,reject) => {
			try {
				//wp.media.featuredImage.frame().open();              // Open media Gallery
				wp.media.featuredImage.set( firstImageAttachmentID ); // set featured image the first one generated
				console.log('[scWordAISetFeaturedImage] Set Featured Image. ID: '+ firstImageAttachmentID );			
				resolve(firstImageAttachmentID);
			}
			catch(error) {
				console.log('[scWordAISetFeaturedImage]Set Featured Image Error for ID: '+ firstImageAttachmentID );
				console.log(error);
				reject(firstImageAttachmentID);
			}
		});
	}*/
	
	
	// Metabox content write button click to open popup dialog display
	$('body').on( "click", ".metabox-content-writer-btn", function(e) {
		// console.log('Metabox clicked');
		//e.preventDefault();
		//$('#metabox-button-click-dialog').dialog('open');
		$('#metabox-button-click-dialog').dialog('open').prev(".ui-dialog-titlebar").css({'background':'#0693e3', 'border': 'none', 'color': 'white'});		
		return false;
	});
	
	// Test OpenAI API
	$('.test-openai-btn').click(function() {		
		$.ajax({
		  type:"POST",
		  cache: false,
		  url: sftcy_wordai_metabox_script_obj.adminajax_url,
		  data : {			    
                action 	 : 'sc_wordai_api_test',
                security : sftcy_wordai_metabox_script_obj.nonce,			    
                },		  
		  success: function(data) { 
		  	 console.log(data); 
			},
		  error: function( xhr, status, error ) { 
		  	 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 
			}
		});
				
	});
	
	// Show API Key	- Show Checkbox
	$('body').on("change", ".wordai-api-key-show", function() {		
		let apiKeyInputElement = $('.wordai-api-key-data');
		if ( $(this).is(':checked') ) {			
			let params = {};
			params.checked = true;
			apiKeyInputElement.css({'opacity' : '0.10'});
			$.ajax({
			  type: 'POST',
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'wordai_api_key_show',
					security : sftcy_wordai_metabox_script_obj.nonce,			    
					postData : params,	
					},		  
			  success: function(data) { 
				//  console.log(data); 
				 let jsonData	= JSON.parse( data );
				 if ( jsonData.status == 'success' ) {
					  apiKeyInputElement.val( jsonData.apiKey );
					  apiKeyInputElement.css({'opacity' : '1'});
				 }
				 else {					  
					 apiKeyInputElement.css({'opacity' : '1'});
				 }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 		
				 apiKeyInputElement.css({'opacity' : '1'}); 
				}			
			});					
		}
		else {
			apiKeyInputElement.val('');
		}
	});
	
	// OpenAI API Key - Submit form
	$('body').on("submit", "#wordai-api-key-form", function() {		
		let postFormData = $(this).serialize();		
		let wordAIAPIKeySaveFeedback = $('.wordai-api-key-save-feedback-msg-td');
		wordAIAPIKeySaveFeedback.removeClass('success-msg alert-msg error-msg').html('');
		$.ajax({
		  type: 'POST',
		  url: sftcy_wordai_metabox_script_obj.adminajax_url,
		  data : {			    
                action 	 : 'wordai_api_key_data_save',
                security : sftcy_wordai_metabox_script_obj.nonce,			    
			    postData : postFormData,	
                },		  
		  success: function(data) { 
		  	//  console.log(data); 
			 let jsonData	= JSON.parse( data );
			 if ( jsonData.status == 'success' ) {
				  wordAIAPIKeySaveFeedback.addClass('success-msg').html(sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' + jsonData.feedbackMsg);
				  $('.wordai-api-key-show').prop( 'checked', true ); // When user enetered new key - shown as default as entered, so keep it checked
			 }
			 else if ( jsonData.status == 'warning' ) {
				  wordAIAPIKeySaveFeedback.addClass('alert-msg').html(sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' + jsonData.feedbackMsg);
			 }			  
			 else {
				  wordAIAPIKeySaveFeedback.addClass('error-msg').html(sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg);
			 }
			},
		  error: function( xhr, status, error ) { 
		  	 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 		
			 wordAIAPIKeySaveFeedback.addClass('error-msg').html(sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg);
			}			
		});		
		return false;
	});
	
	
	// OpenAI API settings parameter form data - Submit form
	$('body').on("submit", "#scwordai-apisettings-form", function() {		
		let postFormData = $(this).serialize();
		// console.log(postFormData);
		let scWordAIFeedback = $('.sc-wordai-api-settings-msg');		
		scWordAIFeedback.removeClass('success-msg alert-msg error-msg').html('');
		$.ajax({
			type: 'POST',
		  url: sftcy_wordai_metabox_script_obj.adminajax_url,
		  data : {			    
                action 	 : 'sc_wordai_apisettings_data',
                security : sftcy_wordai_metabox_script_obj.nonce,			    
			    postData : postFormData,	
                },		  
		  success: function(data) { 
		  	//  console.log(data); 
			 let jsonData	= JSON.parse( data );
			 if ( jsonData.status === 'success' ) {
				  scWordAIFeedback.html('<span class="success-msg">'+sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' + jsonData.feedbackMsg+'</span>');
			 }
			 else if ( jsonData.status === 'warning' ) {
				  scWordAIFeedback.html('<span class="alert-msg">'+sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' + jsonData.feedbackMsg+'</span>');
			 }			  
			 else {
				  scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg+'</span>');
			 }
			},
		  error: function( xhr, status, error ) { 
		  	 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 		
			 scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg+'</span>');
			}			
		});
		
		return false;
	});

	// OpenAI API settings parameter form data - Reset to default basic API parameter settings
	$('body').on("click", ".scwordai-apisettings-form-reset-data-btn", function() {
		let confMsg = confirm('Do you want to reset to default settings?');
		let scWordAIFeedback = $('.sc-wordai-api-settings-msg');
		if ( confMsg ) {						
			scWordAIFeedback.removeClass('success-msg alert-msg error-msg').html('');
			$.ajax({
			  type: 'POST',
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'sc_wordai_apisettings_reset_data',
					security : sftcy_wordai_metabox_script_obj.nonce,			    					
					},		  
			  success: function(data) { 
				//  console.log(data); 
				 let jsonData	= JSON.parse( data );
				  if ( jsonData.status == 'success' ) {
					  scWordAIFeedback.html('<span class="success-msg">'+sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' + jsonData.feedbackMsg+'</span>');
					  setTimeout(function() { window.location.reload(true); }, 2500 );
				  }
				  else if ( jsonData.status == 'warning' ) {
					  scWordAIFeedback.html('<span class="alert-msg">'+sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' + jsonData.feedbackMsg+'</span>');
				  }
				  else {
					  scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg+'</span>');
				  }				  
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 		
				 scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + sftcy_wordai_metabox_script_obj.failed_to_save+'</span>');
				}			
			});			
		}
	});
	
	// OpenAI API Content settings parameter form data - Submit form
	$('body').on("submit", "#scwordai-content-settings-form", function() {
		let postFormData = $(this).serialize();
		// console.log(postFormData);
		let scWordAIFeedback = $('.sc-wordai-api-settings-msg');		
		scWordAIFeedback.removeClass('success-msg alert-msg error-msg').html('');
		$.ajax({
			type: 'POST',
		  url: sftcy_wordai_metabox_script_obj.adminajax_url,
		  data : {			    
                action 	 : 'sc_wordai_content_settings_data',
                security : sftcy_wordai_metabox_script_obj.nonce,			    
			    postData : postFormData,	
                },		  
		  success: function(data) { 
		  	//  console.log(data); 
			 let jsonData	= JSON.parse( data );			 
			 if ( jsonData.status == 'success' ) {
				  scWordAIFeedback.html('<span class="success-msg">'+sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' + jsonData.feedbackMsg+'</span>');
			 }
			 else if ( jsonData.status == 'warning' ) {				  
				  scWordAIFeedback.html('<span class="alert-msg">'+sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' + jsonData.feedbackMsg+'</span>');
			 }			  
			 else {
				  scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg+'</span>');
			 }
			},
		  error: function( xhr, status, error ) { 
		  	 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 		
			 scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg+'</span>');
			}			
		});
		
		return false;
	});
	
	
	// OpenAI API Content settings parameter form data - Reset to default Content settings
	$('body').on("click", ".scwordai-content-apisettings-form-reset-data-btn", function() {
		let scWordAIFeedback = $('.sc-wordai-api-settings-msg');
		let confMsg	= confirm('Do you want to reset to default settings?');
		if ( confMsg ) {						
			scWordAIFeedback.removeClass('success-msg alert-msg error-msg').html('');
			$.ajax({
			  type: 'POST',
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'sc_wordai_content_settings_reset_data',
					security : sftcy_wordai_metabox_script_obj.nonce,			    					
					},		  
			  success: function(data) { 
				//  console.log(data); 
				 let jsonData	= JSON.parse( data );
				  if ( jsonData.status == 'success' ) {
					  scWordAIFeedback.html('<span class="success-msg">'+sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' +jsonData.feedbackMsg+'</span>');
					  setTimeout(function() { window.location.reload(true); }, 2500 );
				  }
				  else if ( jsonData.status == 'warning' ) {
					  scWordAIFeedback.html('<span class="alert-msg">'+sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' +jsonData.feedbackMsg+'</span>');
				  }
				  else {
					  scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' +jsonData.feedbackMsg+'</span>'); 
				  }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 		
				 scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' +sftcy_wordai_metabox_script_obj.failed_to_save+'</span>');
				}			
			});			
		}
	});
	
		
	// OpenAI API Image settings parameter form data - Submit form
	$('body').on("submit", "#scwordai-image-settings-form", function() {
		let postFormData		= $(this).serialize();
		// console.log(postFormData);
		let scWordAIFeedback	= $('.sc-wordai-api-settings-msg');
		scWordAIFeedback.removeClass('success-msg alert-msg error-msg').html('');
		$.ajax({
			type: 'POST',
		  url: sftcy_wordai_metabox_script_obj.adminajax_url,
		  data : {			    
                action 	 : 'sc_wordai_image_settings_data',
                security : sftcy_wordai_metabox_script_obj.nonce,			    
			    postData : postFormData,	
                },		  
		  success: function(data) { 
		  	//  console.log(data); 
			 let jsonData	= JSON.parse( data );
			  if ( jsonData.status == 'success' ) {
				  scWordAIFeedback.html('<span class="success-msg">' + sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' +  jsonData.feedbackMsg + '</span>');
				  setTimeout(function() { window.location.reload(true); }, 2000 );
			  }
			  else if ( jsonData.status == 'warning' ) {
				  scWordAIFeedback.html('<span class="alert-msg">'+sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' + jsonData.feedbackMsg + '</span>');				  
			  }			  
			  else {
				  scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg + '</span>');
			  }
			},
		  error: function( xhr, status, error ) { 
		  	 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 		
			 scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg + '</span>');
			}			
		});
		
		return false;
	});
	
	// OpenAI API Image settings parameter form data - Reset to default Image settings
	$('body').on("click", ".scwordai-image-apisettings-form-reset-data-btn", function() {
		let scWordAIFeedback = $('.sc-wordai-api-settings-msg');
		let confMsg	= confirm('Do you want to reset to default settings?');
		if ( confMsg ) {						
			scWordAIFeedback.removeClass('success-msg alert-msg error-msg').html('');
			$.ajax({
			  type: 'POST',
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'sc_wordai_image_settings_reset_data',
					security : sftcy_wordai_metabox_script_obj.nonce,			    					
					},		  
			  success: function(data) { 
				//  console.log(data); 
				 let jsonData	= JSON.parse( data );
				  if ( jsonData.status == 'success' ) {
					  scWordAIFeedback.html('<span class="success-msg">'+sftcy_wordai_metabox_script_obj.wordai_success_icon + ' ' + jsonData.feedbackMsg + '</span>');
					  setTimeout(function() { window.location.reload(true); }, 2500 );
				  }
				  else if ( jsonData.status == 'warning' ) {
					  scWordAIFeedback.html('<span class="alert-msg">'+sftcy_wordai_metabox_script_obj.wordai_info_icon + ' ' + jsonData.feedbackMsg + '</span>');					  
				  }				  
				  else {
					  scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg + '</span>');
				  }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 		
				 scWordAIFeedback.html('<span class="error-msg">'+sftcy_wordai_metabox_script_obj.wordai_error_icon + ' ' + jsonData.feedbackMsg + '</span>');
				}			
			});			
		}
	});
	
				 			
	// Suggest Titles Popup dialog config	
 	$('#suggest-titles-button-click-dialog').dialog({
		    modal: true,
			title: sftcy_wordai_metabox_script_obj.popup_dialog_suggest_title,
		    titleIsHtml: true, 
			dialogClass: 'suggest-titles-button-click-popup-dialog-class',
			autoOpen: false,
			draggable: true,
			//width: 'auto',	   	
		    minWidth: 600,
		    maxHeight: 500,			
			resizable: false,
			closeOnEscape: true,
			position: {
			  my: "center",
			  //my: "top",	
			  at: "center",
			  //at: "top",	
			  of: window
			},
			open: function () {
			  // close dialog by clicking the overlay behind it
			  $('.ui-widget-overlay').bind('click', function() {
				$('#suggest-titles-button-click-dialog').dialog('close');
			  });
			},
			create: function () {
			  // style fix for WordPress admin
			  $('.ui-dialog-titlebar-close').addClass('ui-button');		
			  // Write html content in dialog title	
			  let w = $(this).dialog("widget");
      		  let t = $(".ui-dialog-title", w);				
			  t.html( sftcy_wordai_metabox_script_obj.sc_wordai_icon + sftcy_wordai_metabox_script_obj.popup_dialog_suggest_title );									
			}
		  });					
	
	
		  // Suggest titles button click - Initial open popup dialog for suggest titles	
		  $('body').on( "click", ".sc-wordai-suggest-titles", function(e) {
			// console.log('Suggest titles button click');
			e.preventDefault();
			$('.prompt-alert-msg').html('');  
			let postID		= $(this).data('scwordai-post-id');
			// console.log(postID);
			//let chosenPostTitle	= $('#post-'+postID+' .title .row-title' ).html();
			//let chosenPostTitle	= $('#post-'+postID+' .row-title' ).html();  
			let chosenPostTitle	= $('#post-'+postID+' .row-title' ).text();    
			// console.log(chosenPostTitle);  
			$('.scwordai-suggest-title-prompt').val(chosenPostTitle);
			// Hide If previous Titles shown  
			$('.scwordai-suggested-title-wrapper').hide();  
			$('.sc-wordai-suggested-title-update-btn').attr('data-scwordai-updatebtn-postid', postID);
			$('#suggest-titles-button-click-dialog').dialog('open').promise().done( function() { 
				// $('#suggest-titles-button-click-dialog').css( 'max-height', 'none');
				$('#suggest-titles-button-click-dialog').css( 'max-height', 'none').prev(".ui-dialog-titlebar").css({'background':'#0693e3', 'border': 'none', 'color': 'white'});
			    } );   
		  });


	     // Save suggest title number on select dropdown
	     $('body').on("change", "#scwordai-suggested-titles-number", function() {
			 let suggestedTitleNumber	=	$(this).val();
			//  console.log('SuggestedTitleNumber:'+ suggestedTitleNumber );
			 $.ajax({
				type: 'POST',
			  url: sftcy_wordai_metabox_script_obj.adminajax_url,
			  data : {			    
					action 	 : 'sc_wordai_suggested_title_number_save',
					security : sftcy_wordai_metabox_script_obj.nonce,			    
					suggestedTitle : suggestedTitleNumber,	
					},		  
			  success: function(data) { 
				//  console.log(data); 
				 let jsonData	= JSON.parse( data );
				  if ( jsonData.status == 'success' ) {
					//   console.log('Saved suggested Title Number.')
				  }
				},
			  error: function( xhr, status, error ) { 
				 console.log(xhr); 
				 console.log(status); 
				 console.log(error); 						  
				}			
			});			 
		 });		
	
	
	     // On change Image model show-hide related fields
	     $('body').on('change', '#sc-wordai-openai-image-model-slug', function() {
			 let selectedImageModelSlug	=	$(this).val();
			//  console.log(selectedImageModelSlug	);
			 switch( selectedImageModelSlug ) {
				 case 'dall-e-2':					 
					 $('.sc-wordai-dalle3-row').fadeOut(function() {
						 $('.sc-wordai-dalle2-row').fadeIn(300);
					 });					 
					 break;
				 case 'dall-e-3':					 
					 $('.sc-wordai-dalle2-row').fadeOut(function() {
						 $('.sc-wordai-dalle3-row').fadeIn(300);
					 });					 
					 break;
			 }
		 });
	
	
	    // Placeholder texts for prompt input
	    let placeHolderTexts	=	[ "Write your prompt / hints words...", "Ex - White Horse", "Ex - Beautiful Sky", "Ex - Summer Holidays", "Ex - Stormy Weather", "Ex - Blue Tshirt", "Ex - Artificial intelligence", "Ex - Autumn Memories" ];
	    $('.scwordai-prompt').placeholderTypewriter({ text: placeHolderTexts, delay: 25, pause:700});
	
}); // End jQuery(document).ready(function($)



(function ($) {
  "use strict";

  $.fn.placeholderTypewriter = function (options) {

    // Plugin Settings
    var settings = $.extend({
      delay: 50,
      pause: 1000,
      text: []
    }, options);

    // Type given string in placeholder
    function typeString($target, index, cursorPosition, callback) {

      // Get text
      var text = settings.text[index];

      // Get placeholder, type next character
      var placeholder = $target.attr('placeholder');
      $target.attr('placeholder', placeholder + text[cursorPosition]);

      // Type next character
      if (cursorPosition < text.length - 1) {
        setTimeout(function () {
          typeString($target, index, cursorPosition + 1, callback);
        }, settings.delay);
        return true;
      }

      // Callback if animation is finished
      callback();
    }

    // Delete string in placeholder
    function deleteString($target, callback) {

      // Get placeholder
      var placeholder = $target.attr('placeholder');
      var length = placeholder.length;

      // Delete last character
      $target.attr('placeholder', placeholder.substr(0, length - 1));

      // Delete next character
      if (length > 1) {
        setTimeout(function () {
          deleteString($target, callback)
        }, settings.delay);
        return true;
      }

      // Callback if animation is finished
      callback();
    }

    // Loop typing animation
    function loopTyping($target, index) {

      // Clear Placeholder
      $target.attr('placeholder', '');

      // Type string
      typeString($target, index, 0, function () {

        // Pause before deleting string
        setTimeout(function () {

          // Delete string
          deleteString($target, function () {
            // Start loop over
            loopTyping($target, (index + 1) % settings.text.length)
          })

        }, settings.pause);
      })

    }

    // Run placeholderTypewriter on every given field
    return this.each(function () {
      loopTyping($(this), 0);
    });

  };

}(jQuery));