// Meta Box popup window Events
var sftcyWordAIInsertTitleFunc;
var sftcyWordAIInsertContentFunc;	
var sftcyWordAIInsertExcerptFunc;
var sftcyWordAIInsertTagstFunc;	

jQuery(document).ready(function($) {			
	// console.log('Localized WordAI Admin View Script Data');	
	// console.log( sftcy_wordai_admin_view_script_obj );	
				
	// Metabox button click Popup Config
 	$('#metabox-button-click-dialog').dialog({
			title: sftcy_wordai_admin_view_script_obj.metabox_popup_dialog_title,
			dialogClass: 'metabox-button-click-popup-dialog-class',
			autoOpen: false,
			draggable: true,
			//width: 'auto',	   	
		    //minWidth: 800,
		    minWidth: 1000,
		    maxHeight: 1000,
			modal: true,
			//resizable: false,
		    resizable: true,
			closeOnEscape: true,
			position: {
			  //my: "center",
			  my: "top",	
			  //at: "center",
			  at: "top",	
			  of: window
			},
			open: function () {
			  // close dialog by clicking the overlay behind it
			  $('.ui-widget-overlay').bind('click', function() {
				$('#metabox-button-click-dialog').dialog('close');
			  });
			},
			create: function () {
			  // style fix for WordPress admin
			  $('.ui-dialog-titlebar-close').addClass('ui-button');		
			  // Write html content in dialog title	
			  let w = $(this).dialog("widget");
      		  let t = $(".ui-dialog-title", w);				
			  t.html( sftcy_wordai_admin_view_script_obj.sc_wordai_icon + sftcy_wordai_admin_view_script_obj.metabox_popup_dialog_title );													
			}
		  });				
	
						
	// Insert Title
	$('body').on("click", ".sc-wordai-title-insert-btn", function() {
		sftcyWordAIInsertTitleFunc();
	});
	
	// Insert Content	
	$('.sc-wordai-content-insert-btn').click(function() {								
		sftcyWordAIInsertContentFunc();
	});

	// Insert Excerpt	
	$('.sc-wordai-excerpt-insert-btn').click(function() {								
		sftcyWordAIInsertExcerptFunc();
	});

	// Insert Tags	
	$('.sc-wordai-tags-insert-btn').click(function() {								
		sftcyWordAIInsertTagstFunc();
	});		
		
	// Copy to clipboard
	if ( typeof ClipboardJS !== 'undefined' ) {
		new ClipboardJS('.sc-wordai-title-copy-btn, .sc-wordai-content-copy-btn, .sc-wordai-excerpt-copy-btn, .sc-wordai-tags-copy-btn');
	}
	
	// Title Copied | Inserted
	$('.sc-wordai-title-copy-btn').click(function() {
		let copyBtn = $(this);
		copyBtn.html(sftcy_wordai_admin_view_script_obj.copied);
		setTimeout( function() { copyBtn.html(sftcy_wordai_admin_view_script_obj.copy_title); }, 6000 );
	});
	$('.sc-wordai-title-insert-btn').click(function() {
		let insertBtn = $(this );
		insertBtn.html(sftcy_wordai_admin_view_script_obj.inserted);		
		setTimeout( function() { insertBtn.html(sftcy_wordai_admin_view_script_obj.insert_title); }, 6000 );
	});
		
	// Content Copied | Inserted
	$('.sc-wordai-content-copy-btn').click(function() {
		let copyBtn = $(this);
		copyBtn.html(sftcy_wordai_admin_view_script_obj.copied);
		setTimeout( function() { copyBtn.html(sftcy_wordai_admin_view_script_obj.copy_content); }, 6000 );
	});
	$('.sc-wordai-content-insert-btn').click(function() {
		let insertBtn = $(this );
		insertBtn.html(sftcy_wordai_admin_view_script_obj.inserted);
		setTimeout( function() { insertBtn.html(sftcy_wordai_admin_view_script_obj.insert_content); }, 6000 );
	});
	
	// Excerpt Copied | Inserted
	$('.sc-wordai-excerpt-copy-btn').click(function() {
		let copyBtn = $(this);
		copyBtn.html(sftcy_wordai_admin_view_script_obj.copied);
		setTimeout( function() { copyBtn.html(sftcy_wordai_admin_view_script_obj.copy_excerpt); }, 6000 );
	});
	$('.sc-wordai-excerpt-insert-btn').click(function() {
		let insertBtn = $(this );
		insertBtn.html(sftcy_wordai_admin_view_script_obj.inserted);
		setTimeout( function() { insertBtn.html(sftcy_wordai_admin_view_script_obj.insert_excerpt); }, 6000 );
	});
	
	// Tags Copied | Inserted
	$('.sc-wordai-tags-copy-btn').click(function() {
		let copyBtn = $(this);
		copyBtn.html(sftcy_wordai_admin_view_script_obj.copied);
		setTimeout( function() { copyBtn.html(sftcy_wordai_admin_view_script_obj.copy_tags); }, 6000 );
	});
	$('.sc-wordai-tags-insert-btn').click(function() {
		let insertBtn = $(this );
		insertBtn.html(sftcy_wordai_admin_view_script_obj.inserted);
		setTimeout( function() { insertBtn.html(sftcy_wordai_admin_view_script_obj.insert_tags); }, 6000 );
	});
	
	
	// Block Editor: Insert Title method
	sftcyWordAIInsertTitleFunc	=	function() {
			let postTitle	=	$('.sc-wordai-generated-title').val();		    
			if ( sftcy_wordai_admin_view_script_obj.current_posttype == 'post' || sftcy_wordai_admin_view_script_obj.current_posttype == 'page' ) {		
				//wp.data.dispatch('core/editor').editPost({title: postTitle});
				scWordaiBlockEditorContentInsert( postTitle, 'title' );	
			}
			else if ( sftcy_wordai_admin_view_script_obj.current_posttype == 'product' ) {	
				if (tinymce.activeEditor) {
					$('#title').siblings('label').addClass('screen-reader-text');
					$('#title').val( postTitle );												
				}
			}
			else {
				console.log('Post Type Not Supported!');
			}		
	}
	
	// Block Editor: Insert Content method
	sftcyWordAIInsertContentFunc =	function() {
		//let postContent		= $('.sc-wordai-generated-content').val();	
		let postContent		= $('.sc-wordai-replace-withbr-response-format-content').val(); // Get content with br		
		//console.log(postContent);						
        if ( sftcy_wordai_admin_view_script_obj.current_posttype == 'post' || sftcy_wordai_admin_view_script_obj.current_posttype == 'page' ) {					
			scWordaiBlockEditorContentInsert( postContent, 'content' );	
			// Save the post - Delay the content generation
			//wp.data.dispatch( 'core/editor' ).savePost();			
		}
		else if ( sftcy_wordai_admin_view_script_obj.current_posttype === 'product' ) {	
			if (tinymce.activeEditor) {								
				// tinymce.activeEditor.execCommand('mceInsertContent', false, postContent );
				var activeEditor = tinyMCE.get('content');
				activeEditor.setContent(postContent);
			}
		}
		else {
			console.log('Post Type Not Supported!')
		}		
	}

	// Block Editor: Insert Excerpt method
	sftcyWordAIInsertExcerptFunc	= function() {		
		let postExcerpt			= $('.sc-wordai-replace-withbr-response-format-excerpt').val();						
        if ( sftcy_wordai_admin_view_script_obj.current_posttype == 'post' || sftcy_wordai_admin_view_script_obj.current_posttype == 'page' ) {
			$('.components-button.edit-post-sidebar__panel-tab:first').trigger( "click"); 	
			scWordaiBlockEditorContentInsert( postExcerpt, 'excerpt' );			
			// Save the post - Delay the content generation
			//wp.data.dispatch( 'core/editor' ).savePost();									
		}
		else if ( sftcy_wordai_admin_view_script_obj.current_posttype == 'product' ) {	
			if (tinymce.activeEditor) {								
				//tinymce.activeEditor.execCommand('mceInsertContent', false, postExcerpt );
				var activeEditor = tinyMCE.get('excerpt');
				activeEditor.setContent(postExcerpt);
			}
		}
		else {
			console.log('Post Type Not Supported!');
		}		
	};
	//  Block Editor: Insert content on different request
	let scWordaiBlockEditorContentInsert = function( content, contentType ) {
		switch( contentType ) {
			case 'title':				    
				    wp.data.dispatch('core/editor').editPost({title: content }).then(function() {
						// console.log('Post Title: Inserted!');
					});
				break;
			case 'content':											    
				    wp.data.dispatch( 'core/block-editor' ).insertBlocks( wp.blocks.createBlock( 'core/paragraph', { content: content }) ).then(function() {
						wp.data.dispatch('core/edit-post').switchEditorMode('html').then(function() {
							wp.data.dispatch('core/edit-post').switchEditorMode('visual').then(function() {
								// console.log('Post Content: Inserted!');
							});							
						});
					});					
				break;
			case 'excerpt':				    
						// await wp.data.dispatch('core/block-editor').insertBlocks( wp.blocks.createBlock( 'core/post-excerpt', { content: content }) );
						wp.data.dispatch('core/block-editor').insertBlocks( wp.blocks.createBlock( 'core/post-excerpt', {}) ).then(function() {
							wp.data.dispatch('core/editor').editPost({excerpt: content}).then(function() {
								wp.data.dispatch('core/edit-post').switchEditorMode('html').then(function() {
									wp.data.dispatch('core/edit-post').switchEditorMode('visual').then(function() {
										// console.log('Post Excerpt: Inserted!');
									});							
								});	
							});								
						});
				break;
			default:
				console.log('Inserting for Item['+contentType+'] not supported.');
				break;
		}
	}
	
	
	// Block Editor: Insert Tags method
	sftcyWordAIInsertTagstFunc	= function() {		
		let postTags		= $('.sc-wordai-generated-tags').val();			
		// console.log('Tags: '+ postTags );					
        if ( sftcy_wordai_admin_view_script_obj.current_posttype === 'post' || sftcy_wordai_admin_view_script_obj.current_posttype === 'page' ) {				
			 let params		= {};
			 let actionName = 'sc_wordai_save_tags';
			 params.tags	= postTags;
			 params.postID	= wp.data.select("core/editor").getCurrentPostId();
			 
			 scWordAIGlobalAjax(params, actionName).then( function(data) {
				 //console.log(data);
				 let jData	= JSON.parse(data);
				 //console.log(jData);
				//  console.log(jData.tagIDs);
				 wp.data.dispatch( 'core/editor' ).editPost({tags: jData.tagIDs});
			 })
			 .catch( function(error) {
				 console.log(error);
			 });			
		}
		else if ( sftcy_wordai_admin_view_script_obj.current_posttype === 'product' ) {	
			//$('#new-tag-product_tag').attr('value', postTags );
			$('#new-tag-product_tag').val( postTags );
			$('.button.tagadd').trigger('click');			 									
			$('.scwordai-prompt').focus();
		}
		else {
			console.log('Post Type Not Supported!');
		}		
	};		
			
	// API Settings Page
	$('body').on( "change", ".sc-temperature-input", function() { $(this).next().text( this.value ); });
	$('body').on( "change", ".sc-wordai-frequency-penalty-input", function() { $(this).next().text( this.value ); });
	$('body').on( "change", ".sc-wordai-presence-penalty-input", function() { $(this).next().text( this.value ); });
	
	
	
}); // End jQuery(document).ready(function($)
