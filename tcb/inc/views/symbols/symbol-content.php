<?php
/**
 * FileName  default.php.
 * @project: thrive-visual-editor
 * @developer: Dragos Petcu
 * @company: BitStone
 */
?>
<?php $symbol_id = get_the_ID(); ?>
<?php $symbol_title = get_the_title(); ?>
<?php $content = TCB_Symbol_Template::render_content(); ?>
<?php TCB_Symbol_Template::body_open(); ?>
	<div class="tve-leads-conversion-object">
		<div id="tve-leads-editor-replace">
			<div class="tve-symbol-container">
				<div class="tve_flt" id="tve_flt">
					<div class="symbol-extra-info">
						<p class="sym-l"><?php echo __( "Currently Editing Symbol \"{$symbol_title}\"" ); ?></p>
						<p class="sym-r"><?php echo __( "Note that this symbol doesn't have any width settings. <br />It will expand to the full width of the content area of your theme." ); ?></p>
					</div>
					<div id="tve_editor" class="tve_editable thrv_symbol thrv_symbol_empty thrv_symbol_<?php echo $symbol_id ?>" data-symbol-id="<?php echo $symbol_id ?>"><?php if ( $content !== '' ) echo $content; ?></div>
				</div>
			</div>
		</div>
	</div>
<?php TCB_Symbol_Template::body_close(); ?>