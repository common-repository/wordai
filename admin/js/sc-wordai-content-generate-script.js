var scWordAIProcessGroupOfChunkResponse;
jQuery(document).ready(function($) {			
	// console.log('Localized WordAI Content Generate Script Data');	
	// console.log( sftcy_wordai_content_generate_script_obj );	
		
	var scWordAIContentGenerationTotalItems			= 0;
	var scWordAIContentGenerationTotalItemsTracker	= 0;
	var scWordAITotalImageGenerationTracker			= 0;
	
	var scTitleXHR									= ''; 		
	var scContentXHR								= ''; 	
	var scExcerptXHR								= '';
	var scTagsXHR									= '';
	var scImagesXHR									= ''; 	
		
	// Generate : Title
	function scWordAIWriteTitle( hintsData ) {		
		let params 					= {};
		params.prompt				= hintsData;	
		return new Promise( ( resolve, reject ) => {
			scTitleXHR	=	$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_write_titles',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 							 
					 resolve( data ); 
					//  console.log(data); 
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;  							 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise						
	}
								
	
	// Generate : Content
	function scWordAIWriteContent( hintsData ) {
		let params 					= {};
		params.prompt				= hintsData;	
		return new Promise( ( resolve, reject ) =>  {
			scContentXHR	=	$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_write_content',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 				 
					 resolve( data ); 
					 console.log(data); 
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;				 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise			
	}
	
	// Generate : Excerpt
	function scWordAIWriteExcerpt( hintsData ) {
		let params 					= {};
		params.prompt				= hintsData;	
		return new Promise( ( resolve, reject ) =>  {
			scExcerptXHR	=	$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_write_excerpt',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 				 
					 resolve( data ); 
					//  console.log(data); 
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;				 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise			
	}
	
	// Generate : Tags
	function scWordAIWriteTags( hintsData ) {		
		let params 					= {};
		params.prompt				= hintsData;	
		return new Promise( ( resolve, reject ) => {
			scTagsXHR	=	$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_write_tags',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 							 
					 resolve( data ); 
					//  console.log(data); 
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;  							 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise						
	}
	
	/*
	// Generate : Images
	function scWordAIGenerateImages( hintsData ) {	
		let promptAlertMsg				= $('.prompt-alert-msg');
		let imagesWrapper				= $('.scwordai-image');
		let imagesSaveToGalleryStatus	= $('.sc-wordai-generated-images-save-to-gallery-status').val();
		let params 						= {};
		params.prompt					= hintsData;	
		imagesWrapper.fadeOut(300);
		return new Promise( ( resolve, reject ) => {			
			scImagesXHR	=	$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_generate_image',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) { 
					  scWordAIContentGenerationTotalItemsTracker++;  				 
					  resolve( data ); 
					  console.log(data); 
					  let jsonData	= JSON.parse( data );
					  if ( jsonData.status == 'success' ) {
						promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_image_success+'</span><br/>');
						$('.sc-wordai-generated-image-wrapper').html(''); // Remove previous images  
						$('.sc-wordai-generated-images-urls').val(jsonData.openAIImgURLs); // commae separated image urls  
						let generatedImgUrlsArr	=  jsonData.openAIImgURLs.split(',');
						for ( let i=0; i < generatedImgUrlsArr.length; i++ ) {
							console.log('Img URL: ' + generatedImgUrlsArr[i]);
							let imgDiv	= '<div><img src="'+generatedImgUrlsArr[i]+'" alt=""></div>';
							$('.sc-wordai-generated-image-wrapper').append(imgDiv);
						}  
						imagesWrapper.fadeIn(300);   
						// Save generated images automatically   
						if ( imagesSaveToGalleryStatus == 1 ) {  
							scWordAIUploadImagesWithSavetoImageGallery();  
						}
					  }
					  else if ( jsonData.status == 'fail' ) {					  
						  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );
					  }
					  else {
						  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_content_generate_script_obj.something_went_wrong_images+'</span><br/>');
					  }														  					  					  					  
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++; 				 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise
	}	
	***/
	
	// Generate : Images
	function scWordAIGenerateImages( hintsData ) {	
		let promptAlertMsg					= $('.prompt-alert-msg');
		let imagesWrapper					= $('.scwordai-image');
		let imagesSaveToGalleryStatus		= $('.sc-wordai-generated-images-save-to-gallery-status').val();
		let scWordAIWillGenerateImageNumber	= parseInt( $('.sc-wordai-will-generate-images-number').val() );
		let params 							= {};
		params.prompt						= hintsData;			
		return new Promise( ( resolve, reject ) => {			
			scImagesXHR	=	$.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_generate_image',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		  
				  success: function(data) {
					  scWordAITotalImageGenerationTracker++;
					  // When all requested number of images are generated then Images Item completed
					  if ( scWordAIWillGenerateImageNumber == scWordAITotalImageGenerationTracker ) {
					  	scWordAIContentGenerationTotalItemsTracker++;  				 
					  }					  
					//   console.log(data); 
					  let jsonData	= JSON.parse( data );
					  if ( jsonData.status == 'success' ) {
						//console.log('Returned Img Url: ');  
						//console.log(jsonData.openAIImgURLs);  						
						let previousCreatedImgsUrlsArr	= [];  
						let allCreatedImgsUrlsArr		= [];  
						if ( $('.sc-wordai-generated-images-urls').val().length != '' ) {  
							previousCreatedImgsUrlsArr	= $('.sc-wordai-generated-images-urls').val().split(',');    
						}
						allCreatedImgsUrlsArr			= previousCreatedImgsUrlsArr.concat( jsonData.openAIImgURLs ); 
						// console.log('All Created Images:' );  
						// console.log( allCreatedImgsUrlsArr );  
						$('.sc-wordai-generated-images-urls').val( allCreatedImgsUrlsArr.toString() ); // comma separated image urls  
						 for ( let i=0; i <  jsonData.openAIImgURLs.length; i++ ) { 
							//console.log('Img URL: ' +  jsonData.openAIImgURLs[i]);
							let imgDiv	= '<div class="each-img-wrapper-div"><img src="'+ jsonData.openAIImgURLs[i]+'" alt=""></div>';
							$('.sc-wordai-generated-image-wrapper').append(imgDiv);
						}  
						imagesWrapper.fadeIn(300);   
					  }
					  else if ( jsonData.status == 'fail' ) {					  
						  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );
					  }
					  else {
						  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_content_generate_script_obj.something_went_wrong_images+'</span><br/>');
					  }			
					  
					  resolve( data ); 
					},
				  error: function( xhr, status, error ) { 
					  scWordAITotalImageGenerationTracker++;
					  // When all requested images are generated
					  if ( scWordAIWillGenerateImageNumber == scWordAITotalImageGenerationTracker ) {
					  	scWordAIContentGenerationTotalItemsTracker++;  				 
					  }
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise
	}	
	
	
	
	// Generate[Streaming] : Title
	function scWordAIStreamingTitle( hintsData ) {		
		let titleWrapper					= $('.scwordai-title');
		let waveAnimationRow			    = $('.wave-animation-row');
		let promptAlertMsg					= $('.prompt-alert-msg');
		let params 							= {};
		params.prompt						= hintsData;	
		let scChunkResponse					= '';
		let scCompleteResponse				= '';
		let scWordAIResponseErrorMessage	= '';
		let scListedChunkProgressResponse   = [];
		return new Promise( ( resolve, reject ) => {
			scTitleXHR	= $.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    					
						action 	 : 'sc_wordai_write_titles',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},	
				  xhrFields: {
						onprogress: function (e) {
							waveAnimationRow.fadeOut(300); 
							titleWrapper.fadeIn(300);
							scCompleteResponse				= '';
							//console.log(e);
							// console.log('Progress [Response Chunk]...');												
							scChunkResponse		 			=	e.target.response;							
							// console.log(scChunkResponse);
							scCompleteResponse				= scWordAIProcessGroupOfChunkResponse(scChunkResponse);
							// console.log( 'Chunk Complete Response:'+ scCompleteResponse );							
							scWordAIResponseErrorMessage	= ( scCompleteResponse[0] !== undefined && scCompleteResponse[0].error !== undefined )? scCompleteResponse[0].errorData : ''; 
							//scCompleteResponse				= scCompleteResponse.replace(/^\"/g, '').replace(/\"$/g, '');
							scCompleteResponse				= (scWordAIResponseErrorMessage.length === 0) ? scCompleteResponse.replace(/^\"/g, '').replace(/\"$/g, '') : '';							
							$('.sc-wordai-generated-title').val( scCompleteResponse );
						}				  
				  },	
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 							 
					//  console.log(data);  
					 resolve( data ); 		
					  if ( scWordAIResponseErrorMessage.length !== 0 ) {
						 titleWrapper.fadeOut(300); 
						 promptAlertMsg.html( '<span class="alert-error">'+ scWordAIResponseErrorMessage +'</span>' );
					  }
					  else {					  
						 promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_title_success+'</span><br/>');  
					  }
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;  							 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise						
	}
		
	
	// Generate[Streaming] : Content
	function scWordAIStreamingContent( hintsData ) {
		let contentWrapper					= $('.scwordai-content');
		let waveAnimationRow			    = $('.wave-animation-row');	
		let promptAlertMsg					= $('.prompt-alert-msg');
		let params 							= {};
		params.prompt						= hintsData;	
		let scChunkResponse					= '';
		let scCompleteResponse				= '';
		let scWordAIResponseErrorMessage	= '';
		let scListedChunkProgressResponse   = [];
				
		return new Promise( ( resolve, reject ) =>  {
			scContentXHR = $.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    
						action 	 : 'sc_wordai_write_content',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},		 
				  xhrFields: {
						onprogress: function (e) {
							waveAnimationRow.fadeOut(300); 
							contentWrapper.fadeIn(300);
							scCompleteResponse				= '';
							//console.log(e);
							// console.log('Progress [Response Chunk]...');												
							scChunkResponse		 			=	e.target.response;							
							// console.log(scChunkResponse);
							scCompleteResponse				= scWordAIProcessGroupOfChunkResponse(scChunkResponse);							
							//console.log( scCompleteResponse );							
							scWordAIResponseErrorMessage	= ( scCompleteResponse[0] !== undefined && scCompleteResponse[0].error !== undefined )? scCompleteResponse[0].errorData : ''; 
							$('.sc-wordai-generated-content').val( scCompleteResponse );
							$('.sc-wordai-replace-withbr-response-format-content').val(scCompleteResponse.replace(/\n/g, "<br>") );  
						}				  
				  },					
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 				 
					 resolve( data ); 
					 //console.log(data); 
					 if ( scWordAIResponseErrorMessage.length != 0 ) {
						contentWrapper.fadeOut(300); 
						promptAlertMsg.html( '<span class="alert-error">'+ scWordAIResponseErrorMessage +'</span>' );
					 }
					 else {					  					  
						promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_content_success+'</span><br/>'); 
					 }
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;				 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise			
	}
	
	
	// Generate[Streaming] : Excerpt
	function scWordAIStreamingExcerpt( hintsData ) {		
		let excerptWrapper					= $('.scwordai-excerpt');
		let waveAnimationRow			    = $('.wave-animation-row');
		let promptAlertMsg					= $('.prompt-alert-msg');
		let params 							= {};
		params.prompt						= hintsData;	
		let scChunkResponse					= '';
		let scCompleteResponse				= '';
		let scWordAIResponseErrorMessage	= '';
		let scListedChunkProgressResponse   = [];
		return new Promise( ( resolve, reject ) => {
			scExcerptXHR	= $.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    					
						action 	 : 'sc_wordai_write_excerpt',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},	
				  xhrFields: {
						onprogress: function (e) {
							waveAnimationRow.fadeOut(300); 
							excerptWrapper.fadeIn(300);
							scCompleteResponse				= '';
							//console.log(e);
							//console.log('Progress [Response Chunk]...');												
							scChunkResponse		 			=	e.target.response;							
							//console.log(scChunkResponse);
							scCompleteResponse				= scWordAIProcessGroupOfChunkResponse(scChunkResponse);							
							//console.log( scCompleteResponse );							
							scWordAIResponseErrorMessage	= ( scCompleteResponse[0] !== undefined && scCompleteResponse[0].error !== undefined )? scCompleteResponse[0].errorData : ''; 
							$('.sc-wordai-generated-excerpt').val( scCompleteResponse );
							$('.sc-wordai-replace-withbr-response-format-excerpt').val(scCompleteResponse.replace(/\n/g, "<br>") );  
						}				  
				  },	
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 							 
					//  console.log(data);  
					 resolve( data ); 		
					  if ( scWordAIResponseErrorMessage.length != 0 ) {
						 excerptWrapper.fadeOut(300); 
						 promptAlertMsg.html( '<span class="alert-error">'+ scWordAIResponseErrorMessage +'</span>' );
					  }
					  else {					  
						 promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_excerpt_success+'</span><br/>');  
					  }
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;  							 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise						
	}
	
	// Generate[Streaming] : Tags
	function scWordAIStreamingTags( hintsData ) {		
		let tagsWrapper						= $('.scwordai-tags');
		let waveAnimationRow			    = $('.wave-animation-row');
		let promptAlertMsg					= $('.prompt-alert-msg');
		let params 							= {};
		params.prompt						= hintsData;	
		let scChunkResponse					= '';
		let scCompleteResponse				= '';
		let scWordAIResponseErrorMessage	= '';
		let scListedChunkProgressResponse   = [];
		return new Promise( ( resolve, reject ) => {
			scTagsXHR	= $.ajax({
				  type:"POST",
				  cache: false,
				  url: sftcy_wordai_content_generate_script_obj.adminajax_url,
				  data : {			    					
						action 	 : 'sc_wordai_write_tags',
						security : sftcy_wordai_content_generate_script_obj.nonce,
						params   : params
						},	
				  xhrFields: {
						onprogress: function (e) {
							waveAnimationRow.fadeOut(300); 
							tagsWrapper.fadeIn(300);
							scCompleteResponse				= '';
							//console.log(e);
							// console.log('Progress [Response Chunk]...');												
							scChunkResponse		 			= e.target.response;							
							// console.log(scChunkResponse);
							scCompleteResponse				= scWordAIProcessGroupOfChunkResponse(scChunkResponse);							
							// console.log( scCompleteResponse );							
							scWordAIResponseErrorMessage	= ( scCompleteResponse[0] !== undefined && scCompleteResponse[0].error !== undefined )? scCompleteResponse[0].errorData : ''; 
							$('.sc-wordai-generated-tags').val(scCompleteResponse.replace(/\n/g, "").replace(/\\/, "") );							
						}				  
				  },	
				  success: function(data) { 
					 scWordAIContentGenerationTotalItemsTracker++; 							 
					//  console.log(data);  
					 resolve( data ); 		
					  if ( scWordAIResponseErrorMessage.length != 0 ) {
						 tagsWrapper.fadeOut(300); 
						 promptAlertMsg.html( '<span class="alert-error">'+ scWordAIResponseErrorMessage +'</span>' );
					  }
					  else {					  
						 promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_tags_success+'</span><br/>');  
					  }
					},
				  error: function( xhr, status, error ) { 
					 scWordAIContentGenerationTotalItemsTracker++;  							 
					 reject( error ); 
					 console.log(xhr); 
					 console.log(status); 
					 console.log(error); 				 
					}
				});
		}); // Promise						
	}
	
	
	
	// Get Content Generation Checked Option Items
	function scWordAIContentGenerateCheckedOptions() {
		let scWordAICheckedOptions	= [];
		$('.sc-wordai-content-generate-options').each(function() {
			if ( $(this).is(':checked') ) {
				scWordAICheckedOptions.push( $(this).val() );
			}
		});
		return scWordAICheckedOptions;
	}
	
	// Hide wave animation - with Button activation - when requests finally complete
	function scWordAIHideWaveAnimationWithButtonActivation() {
		let waveAnimationRow						= $('.wave-animation-row');
		let scWordAIContentGenerateButton			= $('.sc-wordai-generate-content-btn');
		let scWordAIContentGenerateStreamingButton	= $('.sc-wordai-generate-content-streaming-btn');
		let scWordAIContentGenerateCancelButton		= $('.sc-wordai-generate-cancel-requests-content-btn');
		
		// console.log('scWordAIContentGenerationTotalItemsTracker: '+ scWordAIContentGenerationTotalItemsTracker );
		if ( scWordAIContentGenerationTotalItems == scWordAIContentGenerationTotalItemsTracker ) { 
			scWordAIProcessAutoInsertItemsIntoEditor();
			waveAnimationRow.fadeOut(300); 
			scWordAIContentGenerateCancelButton.fadeOut(100, function() { scWordAIContentGenerateButton.fadeIn(); scWordAIContentGenerateStreamingButton.fadeIn(); });
		}										
	}
	
	// Auto Insert Items Title | Content | Excerpt | Tags
    function scWordAIProcessAutoInsertItemsIntoEditor() {
		 // Get checked Items initially requested
		 let scWordAICheckedItems = scWordAIContentGenerateCheckedOptions()
		 let titleItemValue = $('.sc-wordai-generate-content-post-title').val();
		 let contentItemValue = $('.sc-wordai-generate-content-post-content').val();
		 let excerptItemValue = $('.sc-wordai-generate-content-post-excerpt').val();
		 let tagsItemValue = $('.sc-wordai-generate-content-post-tags').val();
		 // Auto Insert Title - If it was checked 
		 if ( $('.sc-wordai-generated-title-auto-insert-status').val() == 1 && scWordAICheckedItems.includes( titleItemValue ) ) {
			sftcyWordAIInsertTitleFunc();
		  } 
		// Auto Insert Content  - If it was checked
		if ( $('.sc-wordai-generated-content-auto-insert-status').val() == 1 && scWordAICheckedItems.includes( contentItemValue ) ) {
			sftcyWordAIInsertContentFunc();
		}   									 
		// Auto Insert Excerpt  - If it was checked
		if ( $('.sc-wordai-generated-excerpt-auto-insert-status').val() == 1 && scWordAICheckedItems.includes( excerptItemValue ) ) {
			sftcyWordAIInsertExcerptFunc();
		}   									 
		// Auto Insert Tags - If it was checked
		if ( $('.sc-wordai-generated-tags-auto-insert-status').val() == 1 && scWordAICheckedItems.includes( tagsItemValue ) ) {
			sftcyWordAIInsertTagstFunc();
		}   				
	}	
	
	// Abort XHR Requests - User Request Abort
	$('body').on( "click", ".sc-wordai-generate-cancel-requests-content-btn", function() {
		let waveAnimationRow						= $('.wave-animation-row');
		let scWordAIContentGenerateButton			= $('.sc-wordai-generate-content-btn');
		let scWordAIContentGenerateStreamingButton	= $('.sc-wordai-generate-content-streaming-btn');
		
		if ( scTitleXHR.length !== 0 ) { scTitleXHR.abort(); }				
		if ( scContentXHR.length !== 0 ) { scContentXHR.abort(); }		
		if ( scImagesXHR.length !== 0 ) { scImagesXHR.abort(); }		
						
		$(this).fadeOut( 100, function() {
			waveAnimationRow.fadeOut(100);
			scWordAIContentGenerateButton.fadeIn(); 
			scWordAIContentGenerateStreamingButton.fadeIn();
		});
	});
	
	// Process Group of Chunks raw API Response Data
	scWordAIProcessGroupOfChunkResponse 	= function( scChunkResponse ) {					
				let scListedChunkProgressResponse	= [];
				let scWordAIProcessedResponse		= '';		        
		        let scWordAIResponseErrorMessage	= '';
				// Catch Error  
		        try {
					let scWordAICheckErrorJson		= JSON.parse( scChunkResponse );					
					//console.log( scWordAICheckErrorJson );						
					if ( scWordAICheckErrorJson.wordAIAPIKeyMissing != undefined  ) {							
						return [ {error: 'error', errorData: scWordAICheckErrorJson.wordAIAPIKeyMissing} ];
					}		
					
					if ( scWordAICheckErrorJson.error.message != undefined ) {
						return [ {error: 'error', errorData: scWordAICheckErrorJson.error.message} ];
					}												
				}
		        catch {}		        		
				// Process valid response	
				scListedChunkProgressResponse			= scChunkResponse.split('\n\n');
				// console.log( scListedChunkProgressResponse );
				// console.log('FOR LOOP LENGTH: '+ scListedChunkProgressResponse.length );
				try {
					// First chunk contain always empty response like {"role":"assistant","content":""}
					for( let i = 1; i < scListedChunkProgressResponse.length; i++ ) {
						// console.log('FOR LOOP: ');
						// console.log( scListedChunkProgressResponse[i] );
						// console.log( scListedChunkProgressResponse[i].length );
						// if ( $.trim( scListedChunkProgressResponse[i] ).length != 0 ) {					
						if ( scListedChunkProgressResponse[i] ) {					
							let jsonParsedData			=  JSON.parse( scListedChunkProgressResponse[i] );								
							// console.log('scWordAIProcessGroupOfChunkResponse Invoked - Inside Process Data FOR LOOP: ');							
							// console.log( jsonParsedData.choices[0].delta.content );
							scWordAIProcessedResponse	+= ( jsonParsedData.choices[0].delta.content != undefined )? jsonParsedData.choices[0].delta.content : '';
							// console.log( scWordAIProcessedResponse );
						}
					} // for
				}
				catch(e) {
					// console.log('Catch Error( scWordAIProcessGroupOfChunkResponse ): ');
					console.log(scListedChunkProgressResponse[i]);
					console.log(e);
				}
				finally {
					return scWordAIProcessedResponse;
				}				
	}
	
	
	// Generate Content Button Click - [ Title | Content | Excerpt | Tags | Images ]
	$('body').on( "click", ".sc-wordai-generate-content-btn", function() {
		scWordAIContentGenerationTotalItems				= 0;
		scWordAIContentGenerationTotalItemsTracker		= 0;
		scWordAITotalImageGenerationTracker				= 0;
		
		let scWordAIContentGenerateButton				= $(this);
		let scWordAICheckedItems						= scWordAIContentGenerateCheckedOptions();
		scWordAIContentGenerationTotalItems				= scWordAICheckedItems.length;
		// console.log( scWordAICheckedItems );
		// console.log( 'scWordAIContentGenerationTotalItems: '+ scWordAIContentGenerationTotalItems);
		let scWordAIContentGenerateCancelButton			= $('.sc-wordai-generate-cancel-requests-content-btn');
		let promptAlertMsg								= $('.prompt-alert-msg');
		let waveAnimationRow							= $('.wave-animation-row');
		let titleWrapper								= $('.scwordai-title');
		let contentWrapper								= $('.scwordai-content');
		let excerptWrapper								= $('.scwordai-excerpt');
		let tagsWrapper									= $('.scwordai-tags');
		let imagesWrapper								= $('.scwordai-image');
		
		let params 										= {};
		params.prompt									= $('.scwordai-prompt').val();		

		if ( $.trim( params.prompt).length == 0 ) {
			promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_content_generate_script_obj.write_ur_prompt+'</span>');
		} 
		else {		
			promptAlertMsg.html("");
			if ( scWordAICheckedItems.length > 0 ) {
				scWordAIContentGenerateButton.fadeOut( 400, function() { scWordAIContentGenerateCancelButton.fadeIn(); });
				waveAnimationRow.fadeIn(300);
				for ( let i = 0; i < scWordAICheckedItems.length; i++ ) {					
					switch( scWordAICheckedItems[i] ) {
						case 'title':
							titleWrapper.fadeOut(300);
							scWordAIWriteTitle( params.prompt ).then( (data) => {
								  let jsonData	= JSON.parse( data );
								  if ( jsonData.status == 'success' ) {
									promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_title_success+'</span><br/>');
									$('.sc-wordai-generated-title').val( jsonData.responseText );
									titleWrapper.fadeIn(300);  		
								  }
								  else if ( jsonData.status == 'fail' ) {					  
									  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );
								  }
								  else {									  
								  	  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_content_generate_script_obj.something_went_wrong_title+'</span><br/>');
								  }					
								  scWordAIHideWaveAnimationWithButtonActivation(); 
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
							});
							break;
						case 'content':			
							contentWrapper.fadeOut(300);
							scWordAIWriteContent( params.prompt ).then( (data) => {
								 let jsonData	= JSON.parse( data );
								 if ( jsonData.status == 'success' ) {
									promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_content_success+'</span><br/>');
									$('.sc-wordai-replace-withbr-response-format-content').val(jsonData.responseTextWithBR);  
									$('.sc-wordai-generated-content').val( jsonData.responseText );					
									contentWrapper.fadeIn(300); 
								  }
								  else if ( jsonData.status == 'fail' ) {					  
									  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );
								  }
								  else {
									  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_content_generate_script_obj.something_went_wrong_content+'</span><br/>');
								  }				
								  scWordAIHideWaveAnimationWithButtonActivation();
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
							});							
							break;
						case 'excerpt':			
							excerptWrapper.fadeOut(300);
							scWordAIWriteExcerpt( params.prompt ).then( (data) => {
								 let jsonData	= JSON.parse( data );
								 if ( jsonData.status == 'success' ) {
									promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_excerpt_success+'</span><br/>');
									$('.sc-wordai-replace-withbr-response-format-excerpt').val(jsonData.responseTextWithBR);  
									$('.sc-wordai-generated-excerpt').val( jsonData.responseText );					
									excerptWrapper.fadeIn(300); 
								  }
								  else if ( jsonData.status == 'fail' ) {					  
									  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );
								  }
								  else {
									  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_content_generate_script_obj.something_went_wrong_content+'</span><br/>');
								  }				
								  scWordAIHideWaveAnimationWithButtonActivation();
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
							});							
							break;	
						case 'tags':
							tagsWrapper.fadeOut(300);
							scWordAIWriteTags( params.prompt ).then( (data) => {
								  let jsonData	= JSON.parse( data );
								  if ( jsonData.status == 'success' ) {
									promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_tags_success+'</span><br/>');
									$('.sc-wordai-generated-tags').val( jsonData.responseText );
									tagsWrapper.fadeIn(300);  		
								  }
								  else if ( jsonData.status == 'fail' ) {					  
									  promptAlertMsg.append( '<span class="alert-error">'+ jsonData.errorMessage +'</span><br/>' );
								  }
								  else {									  
								  	  promptAlertMsg.html('<span class="alert-error">'+sftcy_wordai_content_generate_script_obj.something_went_wrong_title+'</span><br/>');
								  }					
								  scWordAIHideWaveAnimationWithButtonActivation(); 
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
							});
							break;							
						case 'images':			
							let imagesWrapper					= $('.scwordai-image');							
							$('.sc-wordai-generated-image-wrapper').html(''); // Remove/Clean previous images 
							imagesWrapper.fadeOut(300);                       // Hide if displayed earlier
							let imagesSaveToGalleryStatus		= $('.sc-wordai-generated-images-save-to-gallery-status').val();
							let scWordAIWillGenerateImageNumber	= parseInt( $('.sc-wordai-will-generate-images-number').val() );
							// console.log('Total Images Will Generate: '+ scWordAIWillGenerateImageNumber );
																					
							for ( let i = 0; i < scWordAIWillGenerateImageNumber; i++ ) {
								scWordAIGenerateImages( params.prompt ).then( (data) => {									
								   // When all requested number of images are generated
								//    console.log('Image Generated. Track Number: ' + scWordAITotalImageGenerationTracker );	
								   if ( scWordAIWillGenerateImageNumber == scWordAITotalImageGenerationTracker ) {
									    promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_image_success+'</span><br/>');
										// Save generated images automatically   
										if ( imagesSaveToGalleryStatus == 1 ) { scWordAIUploadImagesWithSavetoImageGallery(); }									   
										scWordAIHideWaveAnimationWithButtonActivation();
								   }																		  								
								})
								.catch( (error) => {
									console.log(error);
								   // When all requested number of images are generated
								   if ( scWordAIWillGenerateImageNumber == scWordAITotalImageGenerationTracker ) {
										// Save generated images automatically   
										if ( imagesSaveToGalleryStatus == 1 ) { scWordAIUploadImagesWithSavetoImageGallery(); }										scWordAIHideWaveAnimationWithButtonActivation();
								   }																		  								
								});
					        }							
							break;
					} // switch								
				} // for loop
			} // if ( scWordAICheckedItems.length > 0 )
			else {
				promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_content_generate_script_obj.check_ur_generate_options+'</span>');
			}			
		}
										
	});
			
	// Generate Content Button Click - Streaming - [ Title | Content | Excerpt | Tags | Images ]
	$('body').on( "click", ".sc-wordai-generate-content-streaming-btn", function() {
		scWordAIContentGenerationTotalItems				= 0;
		scWordAIContentGenerationTotalItemsTracker		= 0;
		
		let scWordAIContentGenerateButton				= $(this);				
		let scWordAICheckedItems						= scWordAIContentGenerateCheckedOptions();
		scWordAIContentGenerationTotalItems				= scWordAICheckedItems.length;
		// console.log( scWordAICheckedItems );
		// console.log( 'scWordAIContentGenerationTotalItems: '+ scWordAIContentGenerationTotalItems);
		let scWordAIContentGenerateCancelButton			= $('.sc-wordai-generate-cancel-requests-content-btn');
		let promptAlertMsg								= $('.prompt-alert-msg');
		let waveAnimationRow							= $('.wave-animation-row');
		
		let titleWrapper								= $('.scwordai-title');
		let titleCopyBtn                                = $('.sc-wordai-title-copy-btn');
		let titleInsertBtn                              = $('.sc-wordai-title-insert-btn');		
		
		let contentWrapper								= $('.scwordai-content');
		let contentCopyBtn                              = $('.sc-wordai-content-copy-btn');
		let contentInsertBtn                            = $('.sc-wordai-content-insert-btn');
		
		let excerptWrapper								= $('.scwordai-excerpt');
		let excerptCopyBtn                              = $('.sc-wordai-excerpt-copy-btn');
		let excerptInsertBtn                            = $('.sc-wordai-excerpt-insert-btn');
		
		let tagsWrapper									= $('.scwordai-tags');
		let tagsCopyBtn                                 = $('.sc-wordai-tags-copy-btn');
		let tagsInsertBtn                               = $('.sc-wordai-tags-insert-btn');
				
		let imagesWrapper								= $('.scwordai-image');		
		let params 										= {};
		params.prompt									= $('.scwordai-prompt').val();		

		if ( $.trim( params.prompt).length == 0 ) {
			promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_content_generate_script_obj.write_ur_prompt+'</span>');
		} 
		else {		
			promptAlertMsg.html("");
			if ( scWordAICheckedItems.length > 0 ) {				
				scWordAIContentGenerateButton.fadeOut( 400, function() { scWordAIContentGenerateCancelButton.fadeIn(); });
				waveAnimationRow.fadeIn(300);
				for ( let i = 0; i < scWordAICheckedItems.length; i++ ) {					
					switch( scWordAICheckedItems[i] ) {
						case 'title':
							titleWrapper.fadeOut(300);
							titleCopyBtn.prop( "disabled", true );
							titleInsertBtn.prop( "disabled", true );																					
							scWordAIStreamingTitle( params.prompt ).then( (data) => {
								scWordAIHideWaveAnimationWithButtonActivation(); 
								titleCopyBtn.prop( "disabled", false );
								titleInsertBtn.prop( "disabled", false );																					
								
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
								titleCopyBtn.prop( "disabled", false );
								titleInsertBtn.prop( "disabled", false );																					
								
							});
							break;
						case 'content':			
							contentWrapper.fadeOut(300);
							contentCopyBtn.prop( "disabled", true );
							contentInsertBtn.prop( "disabled", true );														
							scWordAIStreamingContent( params.prompt ).then( (data) => {
								  scWordAIHideWaveAnimationWithButtonActivation();
								  contentCopyBtn.prop( "disabled", false );
								  contentInsertBtn.prop( "disabled", false );															
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
								contentCopyBtn.prop( "disabled", false );
								contentInsertBtn.prop( "disabled", false );							
								
							});							
							break;
						case 'excerpt':
							excerptWrapper.fadeOut(300);
							excerptCopyBtn.prop( "disabled", true );
							excerptInsertBtn.prop( "disabled", true );																					
							scWordAIStreamingExcerpt( params.prompt ).then( (data) => {
								scWordAIHideWaveAnimationWithButtonActivation(); 
								excerptCopyBtn.prop( "disabled", false );
								excerptInsertBtn.prop( "disabled", false );																													
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
								excerptCopyBtn.prop( "disabled", false );
								excerptInsertBtn.prop( "disabled", false );
							});
							break;			
						case 'tags':
							tagsWrapper.fadeOut(300);
							tagsCopyBtn.prop( "disabled", true );
							tagsInsertBtn.prop( "disabled", true );																												
							scWordAIStreamingTags( params.prompt ).then( (data) => {
								 scWordAIHideWaveAnimationWithButtonActivation(); 
								tagsCopyBtn.prop( "disabled", false );
								tagsInsertBtn.prop( "disabled", false );									
							})
							.catch( (error) => {
								console.log(error);
								scWordAIHideWaveAnimationWithButtonActivation();
								tagsCopyBtn.prop( "disabled", false );
								tagsInsertBtn.prop( "disabled", false );	
							});
							break;							
						case 'images':													
							let imagesWrapper					= $('.scwordai-image');							
							$('.sc-wordai-generated-image-wrapper').html(''); // Remove/Clean previous images 
							imagesWrapper.fadeOut(300);                       // Hide if displayed earlier
							let imagesSaveToGalleryStatus		= $('.sc-wordai-generated-images-save-to-gallery-status').val();
							let scWordAIWillGenerateImageNumber	= parseInt( $('.sc-wordai-will-generate-images-number').val() );
							// console.log('Total Images Will Generate: '+ scWordAIWillGenerateImageNumber );
																					
							for ( let i = 0; i < scWordAIWillGenerateImageNumber; i++ ) {
								scWordAIGenerateImages( params.prompt ).then( (data) => {									
								//    console.log('Image Generated. Track Number: ' + scWordAITotalImageGenerationTracker );		
								   // When all requested number of images are generated
								   if ( scWordAIWillGenerateImageNumber == scWordAITotalImageGenerationTracker ) {
									    promptAlertMsg.append('<span class="alert-success">'+sftcy_wordai_content_generate_script_obj.generated_image_success+'</span><br/>');
										// Save generated images automatically   
										if ( imagesSaveToGalleryStatus == 1 ) { scWordAIUploadImagesWithSavetoImageGallery(); }									   
										scWordAIHideWaveAnimationWithButtonActivation();
								   }																		  								
								})
								.catch( (error) => {
									console.log(error);
								   // When all requested number of images are generated
								   if ( scWordAIWillGenerateImageNumber == scWordAITotalImageGenerationTracker ) {
										// Save generated images automatically   
										if ( imagesSaveToGalleryStatus == 1 ) { scWordAIUploadImagesWithSavetoImageGallery(); }										scWordAIHideWaveAnimationWithButtonActivation();
								   }																		  								
								});
					        }														
							break;
					} // switch								
				} // for loop
			} // if ( scWordAICheckedItems.length > 0 )
			else {
				promptAlertMsg.html('<span class="alert-remind">'+sftcy_wordai_content_generate_script_obj.check_ur_generate_options+'</span>');
			}			
		}
										
	});
						
	
}); // End jQuery(document).ready(function($)
