(function($){

	$(".field-autocomplete-multiple, .field-autocomplete").each(function(){
		var $element = $(this);
		var $input = $element.find('.regular-text');
		var $hidden = $element.find('.hidden-postlist');
		var $list = $element.find('.entry-list');
		var post_type = $(this).attr('data-post-type');

		// allows extensions to update the source to filter data
		$(this).on('set_autocomplete_src', function(e, source){
			$input.autocomplete('option', { source: source });
		});

		var renderList = function(){
			$.post('/wp-admin/admin-ajax.php?action=ph_helpers_postlist&post_type='+post_type+'&ids='+$hidden.val(), function(data){
				var data = $.parseJSON(data);
				$list.html('');

				data.forEach(function(element){
					$list.append('<div class="entry" data-id="'+element.ID+'">'+element.post_title+'<span class="delete" data-id="'+element.ID+'">&times;</span></div>');
				});
			});
		}

		$input.autocomplete({
			source: "/wp-admin/admin-ajax.php?action=ph_helpers_autocomplete&post_type="+post_type,
			minChars: 2,
			select: function(event, ui){

				if($element.hasClass('field-autocomplete-multiple')){
					var value = $hidden.val().split(',');
					if(value.indexOf(ui.item.value.toString()) < 0){
						value.push(ui.item.value);
						value = value.filter(Boolean);
						$hidden.val(value.join());
					}
				}else{
						$hidden.val(ui.item.value);
				}

				renderList();
				$(this).val(''); return false;
			}
		});

		$list.click(function(event){
			if($(event.target).attr('class') == 'delete'){
				var id = $(event.target).attr('data-id');

				var value = $hidden.val().split(',');

				var index = value.indexOf(id);
				if (index > -1) {
					value.splice(index, 1);
				}

				value = value.filter(Boolean);
				$hidden.val(value.join());

				renderList();
			}
		});

		renderList();
	});
})(jQuery);
