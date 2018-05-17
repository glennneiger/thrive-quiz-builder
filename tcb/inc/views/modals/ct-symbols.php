<?php
/**
 * FileName  ct-symbols.php.
 * @project: thrive-visual-editor
 * @developer: Dragos Petcu
 * @company: BitStone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

?>

<h2 class="tcb-modal-title"><?php echo __( 'Insert Template or Symbol', 'thrive-cb' ) ?></h2>

<span data-fn="close" class="click tcb-modal-close"><?php tcb_icon( 'close' ) ?></span>

<div class="status tpl-ajax-status"><?php echo __('Fetching data', 'thrive-cb'); ?>...</div>
<div class="error-container"></div>

<div class="tvd-input-field">
	<input type="text" class="search-symbols" id="tcm-symbol-search" name="tcm-symbol-search" placeholder="<?php echo __( 'Search Templates...', 'thrive-cb' ); ?>">
	<span class="search"></span>
</div>

<div class="tve-templates-wrapper">
	<div class="tve-header-tabs">
		<div class="tab-item active" data-content="ct" data-tab="template"><?php echo __( 'Templates', 'thrive-cb' ); ?></div>
		<div class="tab-item" data-content="symbol" data-tab="symbol"><?php echo __( 'Symbols', 'thrive-cb' ); ?></div>
	</div>
	<hr class="symbols-hr" />
	<div class="tve-tabs-content">
		<div class="tve-tab-content active" data-content="ct">
			<div class="tve-content-templates-wrapper" style="background: #f0f3f3 url(<?php echo tve_editor_css('images/symbol_animation_02.gif')?>) center no-repeat; height: 100%">
				<div class="text-no-templates" style="display: none;">
					<?php echo __("Oups! We couldn't find anything called ") ?><span class="search-word"></span><?php echo __('. Maybe search for something else ?'); ?>
				</div>
				<div class="tve-default-templates-list"></div>
			</div>
		</div>
		<div class="tve-tab-content" data-content="symbol">
			<div style="background: #f0f3f3 url(<?php echo tve_editor_css('images/symbol_animation_02.gif')?>) center no-repeat; height: 100%" class="tve-symbols-wrapper">
				<div class="text-no-symbols">
					<?php echo __("Oups! We couldn't find anything called ") ?><span class="search-word"></span><?php echo __('. Maybe search for something else ?'); ?>
				</div>
				<div class="symbols-container" id="symbols-template"></div>
			</div>
		</div>
		<div class="tve-template-preview"></div>
	</div>
</div>

<div class="tcb-modal-footer clearfix">
	<button type="button" disabled="false" class="tcb-right tve-button medium green tcb-modal-save">
		<?php echo __( 'Choose Template', 'thrive-cb' ) ?>
	</button>
</div>