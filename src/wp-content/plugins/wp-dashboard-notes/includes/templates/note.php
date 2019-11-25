		<div class='wp-dashboard-note-wrap regular-note' data-note-type='regular' data-color-text='<?php echo $note_meta['color_text']; ?>' data-note-color='<?php echo $note_meta['color']; ?>'>

			<div class='wp-dashboard-note' contenteditable='true'>
				<?php echo $content; ?>
			</div>

			<div class='wp-dashboard-note-options'>

				<span class='status'></span>
				<div class='wpdn-extra'>
					<span class='wpdn-option-visibility'>
						<?php
						if ( 'private' == $note_meta['visibility'] && $note_meta ) :
							$status['icon']       = 'dashicons-admin-users';
							$status['title']      = __( 'Just me', 'wp-dashboard-notes' );
							$status['visibility'] = 'private';
						else :
							$status['icon']       = 'dashicons-groups';
							$status['title']      = __( 'Everyone', 'wp-dashboard-notes' );
							$status['visibility'] = 'public';
						endif; ?>

						<span class='wpdn-toggle-visibility' title='<?php _e( 'Visibility:', 'wp-dashboard-notes' ); ?> <?php echo $status['title']; ?>' data-visibility='<?php echo $status['visibility']; ?>'>
							<div class='wpdn-visibility visibility-publish dashicons <?php echo $status['icon']; ?>'></div>
						</span>

						<span class='wpdn-color-note' title='<?php _e( 'Give me a color!', 'wp-dashboard-notes' ); ?>'>
							<span class='wpdn-color-palette'>

								<?php foreach ( $colors as $name => $color ) : ?>
									<span class='color color-<?php echo $name;?>' data-select-color-text='<?php echo $name; ?>'	data-select-color='<?php echo $color; ?>' style='background-color: <?php echo $color; ?>'></span>
								<?php endforeach; ?>

							</span>
							<div class='dashicons dashicons-art wpdn-note-color'></div>
						</span>

						<span title='<?php _e( 'Convert to list note', 'wp-dashboard-notes'); ?>'>
							<div class='wpdn-note-type dashicons dashicons-list-view'></div>
						</span>

						<span class='wpdn-add-note' title='<?php _e( 'Add a new note', 'wp-dashboard-notes' ); ?>'>
							<div class='dashicons dashicons-plus'></div>
						</span>

						<span style='float: right; margin-right: 10px;' class='wpdn-delete-note' title='<?php _e( 'Delete note', 'wp-dashboard-notes' ); ?>'>
							<div class='dashicons dashicons-trash'></div>
						</span>

					</span>
				</div>
			</div>

		</div>
