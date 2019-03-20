<div class="form-field">
	<label for="<?php echo $slug ?>-image-id"><?php $label ?></label>
	<input type="hidden" id="<?php echo $slug ?>-image-id" name="<?php echo $slug ?>" value="<?php echo $value ?>" class="custom_media_url" />
	<div id="<?php echo $slug ?>-image-wrapper">
		<?php if ( $value && $value != '' ) { ?>
			<?php
			// a little hack to allow unsupported filetypes like svg which would result in height=1 and width=1
			$image = wp_get_attachment_image ( intval($value), 'thumbnail' );
			$image = str_replace('width="1"', 'width="150"', $image);
			$image = str_replace('height="1"', 'height="150"', $image);
			$image = str_replace('/>', 'style="width: 100px; height: 100px;" />', $image);
			echo $image;
			?>
		<?php } ?>
	</div>
	<p>
		<input type="button" class="button button-secondary ct_tax_media_button" id="<?php echo $slug ?>_ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add PDF', 'hero-theme' ); ?>" />
		<input type="button" class="button button-secondary ct_tax_media_remove" id="<?php echo $slug ?>_ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove PDF', 'hero-theme' ); ?>" />
	</p>
</div>

<script>
jQuery(document).ready( function($) {

    $('#<?php echo $slug ?>_ct_tax_media_button').click(function(e) {
        e.preventDefault();

        var custom_uploader = wp.media({
            title: 'Upload Media',
            button: {
                text: 'Uplaod Custom Media'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#<?php echo $slug ?>-image-id').val(attachment.id);
            $('#<?php echo $slug ?>-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
            $('#<?php echo $slug ?>-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
        })
        .open();
    });

    $('body').on('click','#<?php echo $slug ?>_ct_tax_media_remove',function(){
		$('#<?php echo $slug ?>-image-id').val('');
		$('#<?php echo $slug ?>-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
	});

    /*
	$(document).ajaxComplete(function(event, xhr, settings) {
		if(typeof settings.data !== typeof undefined){
			var queryStringArr = settings.data.split('&');
			if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
				var xml = xhr.responseXML;
				var $response = $(xml).find('term_id').text();
				if($response!=""){
					// Clear the thumb image
					$('#<?php echo $slug ?>-image-wrapper').html('');
				}
			}
		}
	});*/
});
</script>
