<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
?>

<h2 class="tcb-modal-title"><?php echo __( 'Save your element for later use', 'thrive-cb' ) ?></h2>

<span data-fn="close" class="click tcb-modal-close"><?php tcb_icon( 'close' ) ?></span>
<div class="error-container"></div>

<p class="tcb-modal-desc"><?php echo __( 'Save your content and use it on other pages across your website', 'thrive-cb' ) ?></p>

<div class="save-container">
	<div class="tcb-center-checkbox">

		<label class="tcb-symbol-switch">
			<span class="sp template-text"><?php echo __( 'Template', 'thrive-cb' ) ?></span>
			<input type="checkbox" name="change_element" id="change_element">
			<span class="slider round"></span>
			<span class="sp symbol-text"><?php echo __( 'Symbol', 'thrive-cb' ) ?></span>
		</label>

	</div>

	<p class="element-description"><?php echo __( 'Content templates can be used across your website but they are edited only locally.', 'thrive-cb' ) ?></p>

	<div class="tvd-input-field">
		<input type="text" id="content-title" name="title" placeholder="<?php echo __( 'Enter template name', 'thrive-cb' ); ?>">
	</div>

	<div class="tvd-input-field align-left category-container">
		<label class="tcb-symbol-check" for="add_category">
			<input id="add_category" type="checkbox" class="" name="add_category">
			<span class="checkmark"></span>
			<span class="add-to-cat"><?php echo __( 'Add template to a category', 'thrive-cb' ); ?></span>
		</label>

		<div class="category_selection" style="display: none;">
			<div class="">
				<select id="tcb-save-template-categ-suggest"></select>
				<input type="hidden" id="tcb-save-template-category-id" value=""/>
			</div>
		</div>

	</div>
</div>

<div class="tcb-modal-footer clearfix align-left">
	<button type="button" class="tve-button medium cancel-btn tcb-modal-cancel">
		<?php echo __( 'Cancel', 'thrive-cb' ) ?>
	</button>

	<button type="button" class="tcb-right tve-button medium green tcb-modal-save">
		<?php echo __( 'Save', 'thrive-cb' ) ?>
	</button>
</div>


