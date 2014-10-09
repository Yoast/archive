<form action="" method="post" accept-charset="UTF-8">
	<?php
	echo wp_nonce_field( 'yoast-support-request' );
	?>
	<table class="form-table">
		<?php if (isset( $filtered_plugins ) && count( $filtered_plugins ) >= 1): ?>
		<tr>
			<th>
				<label for="yoast-support-plugin"><?php echo _e( 'I have question about', 'yoast-support-framework' ); ?>:</label>
			</th>
			<td>
				<select name="yoast_support[plugin]" id="yoast-support-plugin">
					<option value="--">-- <?php _e( 'Please select an item', 'yoast-support-framework' ); ?> --</option>
					<?php foreach( $filtered_plugins as $plugin ): ?>
						<option value="<?php echo $plugin['id']; ?>"><?php echo $plugin['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th scope="row">
				<label for="yoast-support-question"><?php echo __( 'Your question', 'yoast-support-framework' ); ?>:</label><br />
				<small>(<?php echo __( 'Please provide all information', 'yoast-support-framework' ); ?></small>
			</th>
			<td>
				<textarea cols="50" rows="15" id="yoast-support-question" name="yoast_support[question]" placeholder="<?php echo $yoast_support->support_message(); ?>"></textarea>
			</td>
		</tr>
	</table>

	<p><input type="submit" class="button-primary" name="getsupport" value="Get support"></p>
</form>