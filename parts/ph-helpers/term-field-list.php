<table class="form-table ph-helpers-field-form">
	<tbody>
	<?php foreach($fields as $field): ?>

		<?php
		if($term){
			if($field instanceof \PhHelpers\Field\CustomSaveInterface){
				$value = $field->load($term->term_id, 'term');
			}else{
				$value = get_term_meta( $term->term_id, $field->getSlug(), true );
			}
			$field->setValue($value);
		}
		?>

		<tr>
			<td class="ph-helpers-field-label">
				<label><?php echo $field->getLabel() ?></label>
			</td>
			<td>
				<?php echo $field->html() ?>

				<?php if($field->hasErrors()): ?>
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
