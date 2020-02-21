<?php
/**
 * YoastSEO_AMP_Glue plugin file.
 *
 * @package     YoastSEO_AMP_Glue\Admin
 * @author      Joost de Valk
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 */

/**
 * Class YoastSEO_AMP_Form
 */
class YoastSEO_AMP_Form extends Yoast_Form {

	/**
	 * The options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * YoastSEO_AMP_Form constructor.
	 */
	public function __construct() {
		$this->options = YoastSEO_AMP_Options::get();
	}

	/**
	 * Create a toggle switch input field using two radio buttons.
	 *
	 * @param string $var    The variable within the option to create the radio buttons for.
	 * @param array  $values Associative array of on/off keys and their values to be used as
	 *                       the label elements text for the radio buttons. Optionally, each
	 *                       value can be an array of visible label text and screen reader text.
	 * @param string $label  The visual label for the radio buttons group, used as the fieldset legend.
	 * @param string $help   Inline Help that will be printed out before the visible toggles text.
	 */
	public function toggle_switch( $var, $values, $label, $help = '' ) {
		if ( ! is_array( $values ) || $values === [] ) {
			return;
		}
		$val = $this->get_option_value( $var, false );
		if ( $val === true ) {
			$val = 'on';
		}
		if ( $val === false ) {
			$val = 'off';
		}

		$help_class = ! empty( $help ) ? ' switch-container__has-help' : '';

		printf( '<div class="%s">', esc_attr( 'switch-container' . $help_class ) );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$label` contains html.
		echo '<fieldset id="', esc_attr( $var ), '" class="fieldset-switch-toggle"><legend>', $label, '</legend>', $help;

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The method returns html value.
		echo $this->get_disabled_note( $var );
		echo '<div class="switch-toggle switch-candy switch-yoast-seo">';

		foreach ( $values as $key => $value ) {
			$screen_reader_text_html = '';

			if ( is_array( $value ) ) {
				$screen_reader_text      = $value['screen_reader_text'];
				$screen_reader_text_html = '<span class="screen-reader-text"> ' . esc_html( $screen_reader_text ) . '</span>';
				$value                   = $value['text'];
			}

			printf(
				'<input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s %5$s/>
				<label for="%1$s">%6$s%7$s</label>',
				esc_attr( $var . '-' . $key ),                     // #1: field ID.
				esc_attr( $this->option_name . '[' . $var . ']' ), // #2: field name.
				esc_attr( $key ),                                  // #3: field value.
				checked( $val, $key, false ),                      // #4.
				disabled( $this->is_control_disabled( $var ), true, false ), // #5.
				esc_html( $value ),                                // #6: label text.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: screen reader text is escaped properly just above.
				$screen_reader_text_html                           // #7.
			);
		}

		echo '<a></a></div></fieldset><div class="clear"></div></div>' . PHP_EOL . PHP_EOL;
	}

	/**
	 * Media input.
	 *
	 * @param string $var   Option name.
	 * @param string $label Label message.
	 */
	public function media_input( $var, $label ) {
		$val      = $this->get_option_value( $var, '' );
		$id_value = $this->get_option_value( $var . '_id', '' );

		$this->label(
			$label,
			[
				'for'   => 'wpseo_' . $var,
				'class' => 'select',
			]
		);

		echo '<span>';
		printf(
			'<input class="textinput" id="%1$s" type="text" size="36" name="%2$s" value="%3$s" readonly="readonly" /> ',
			esc_attr( 'wpseo_' . $var ),
			esc_attr( $this->option_name . '[' . $var . ']' ),
			esc_attr( $val )
		);

		printf(
			'<input id="%1$s" class="wpseo_image_upload_button button" type="button" value="%2$s" data-target-id="%3$s" %4$s /> ',
			esc_attr( 'wpseo_' . $var . '_button' ),
			esc_attr__( 'Upload Image', 'yoastseo-amp' ),
			esc_attr( 'wpseo_' . $var . '_id' ),
			disabled( $this->is_control_disabled( $var ), true, false )
		);

		printf(
			'<input class="wpseo_image_remove_button button" type="button" value="%1$s" %2$s />',
			esc_attr__( 'Clear Image', 'yoastseo-amp' ),
			disabled( $this->is_control_disabled( $var ), true, false )
		);

		printf(
			'<input type="hidden" id="%1$s" name="%2$s" value="%3$s" />',
			esc_attr( 'wpseo_' . $var . '_id' ),
			esc_attr( $this->option_name . '[' . $var . '_id]' ),
			esc_attr( $id_value )
		);
		echo '</span><br class="clear"/>';
	}

	/**
	 * Create a light switch input field using a single checkbox.
	 *
	 * @param string $var     The variable within the option to create the checkbox for.
	 * @param string $label   The label element text for the checkbox.
	 * @param array  $buttons Array of two visual labels for the buttons (defaults Disabled/Enabled).
	 * @param bool   $reverse Reverse order of buttons (default true).
	 * @param string $help    Inline Help that will be printed out before the visible toggles text.
	 * @param bool   $strong  Whether the visual label is displayed in strong text. Default is false.
	 */
	public function light_switch( $var, $label, $buttons = [], $reverse = true, $help = '', $strong = false ) {
		$val = $this->get_option_value( $var, false );

		if ( $val === true ) {
			$val = 'on';
		}

		$class = 'switch-light switch-candy switch-yoast-seo';

		if ( $reverse ) {
			$class .= ' switch-yoast-seo-reverse';
		}

		if ( empty( $buttons ) ) {
			$buttons = [ __( 'Disabled', 'yoastseo-amp' ), __( 'Enabled', 'yoastseo-amp' ) ];
		}

		list( $off_button, $on_button ) = $buttons;

		$help_class = ! empty( $help ) ? ' switch-container__has-help' : '';

		$strong_class = ( $strong ) ? ' switch-light-visual-label__strong' : '';

		printf(
			'<div class="%1$s"><span class="%2$s" id="%3$s">%4$s</span>%5$s
			<label class="%6$s"><b class="switch-yoast-seo-jaws-a11y">&nbsp;</b>
			<input type="checkbox" aria-labelledby="%3$s" id="%7$s" name="%8$s" value="on" %9$s %10$s/>',
			esc_attr( 'switch-container' . $help_class ),               // #1: div class.
			esc_attr( 'switch-light-visual-label' . $strong_class ),    // #2: span class.
			esc_attr( $var . '-label' ),                                // #3: span ID.
			esc_html( $label ),                                         // #4: text in span.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: `$help` value is already escaped.
			$help,                                                      // #5.
			esc_attr( $class ),                                         // #6: label class.
			esc_attr( $var ),                                           // #7: input field ID.
			esc_attr( $this->option_name . '[' . $var . ']' ),          // #8: input field name.
			checked( $val, 'on', false ),                               // #9.
			disabled( $this->is_control_disabled( $var ), true, false ) // #10.
		);

		echo '<span aria-hidden="true">
			<span>', esc_html( $off_button ), '</span>
			<span>', esc_html( $on_button ), '</span>
			<a></a>
		</span>
		</label><div class="clear"></div></div>';
	}

	/**
	 * Create a textarea.
	 *
	 * @param string       $var   The variable within the option to create the textarea for.
	 * @param string       $label The label to show for the variable.
	 * @param string|array $attr  The CSS class or an array of attributes to assign to the textarea.
	 */
	public function textarea( $var, $label, $attr = [] ) {
		if ( ! is_array( $attr ) ) {
			$attr = [
				'class' => $attr,
			];
		}

		$defaults = [
			'cols'  => '',
			'rows'  => '',
			'class' => '',
		];
		$attr     = wp_parse_args( $attr, $defaults );
		$val      = $this->get_option_value( $var, '' );

		$this->label(
			$label,
			[
				'for'   => $var,
				'class' => 'textinput',
			]
		);

		printf(
			'<textarea cols="%1$s" rows="%2$s" class="%3$s" id="%4$s" name="%5$s" %6$s>%7$s</textarea><br class="clear" />',
			(int) $attr['cols'],                               // #1: number of columns.
			(int) $attr['rows'],                               // #2: number of rows.
			esc_attr( 'textinput ' . $attr['class'] ),         // #3: CSS classes.
			esc_attr( $var ),                                  // #4: field ID.
			esc_attr( $this->option_name . '[' . $var . ']' ), // #5: field name.
			disabled( $this->is_control_disabled( $var ), true, false ), // #6.
			esc_textarea( $val )                               // #7: field content.
		);
	}

	/**
	 * Retrieves the option value.
	 *
	 * @param string      $option_value The option value to retrieve.
	 * @param string|null $default      The default value.
	 *
	 * @return mixed|string The option value.
	 */
	protected function get_option_value( $option_value, $default = null ) {
		if ( isset( $this->options[ $option_value ] ) ) {
			return $this->options[ $option_value ];
		}

		return $default;
	}
}
