<table class="form-table ph-helpers-field-form">
	<tbody>
	<?php foreach($fields as $field): ?>
		<?php
		if($field instanceof \PhHelpers\Field\CustomSaveInterface){
			$value = $field->load($post->ID);
		}else{
			$value = get_post_meta( $post->ID, $field->getSlug(), true );
		}
		$field->setValue($value);
		?>

		<tr>
			<td class="ph-helpers-field-label">
				<label><?php echo $field->getLabel() ?></label>
			</td>
			<td>
				<?php echo $field->html() ?>
				
				<?php if($post->post_status != 'auto-draft' && $field->hasErrors()): ?>
					<div class="wrap">
						<?php foreach($field->getErrors() as $error){ ?>
							<div class="error notice inline">
								<p><?php echo $error; ?></p>
							</div>
						<?php } ?>
					</div>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
