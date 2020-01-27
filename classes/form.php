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

		$var_esc = esc_attr( $var );

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

			$key_esc = esc_attr( $key );
			$for     = $var_esc . '-' . $key_esc;
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$key_esc` value is already escaped.
			echo '<input type="radio" id="' . $for . '" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" value="' . $key_esc . '" ' . checked( $val, $key_esc, false ) . disabled( $this->is_control_disabled( $var ), true, false ) . ' />',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$screen_reader_text_html` value contains html.
			'<label for="', $for, '">', esc_html( $value ), $screen_reader_text_html,'</label>';
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

		$var_esc = esc_attr( $var );

		$this->label(
			$label,
			[
				'for'   => 'wpseo_' . $var,
				'class' => 'select',
			]
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$var_esc` value is already escaped.
		$id_field_id = 'wpseo_' . $var_esc . '_id';

		echo '<span>';
		echo '<input',
		' class="textinput"',
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$var_esc` value is already escaped.
		' id="wpseo_', $var_esc, '"',
		' type="text" size="36"',
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$var_esc` value is already escaped.
		' name="', esc_attr( $this->option_name ), '[', $var_esc, ']"',
		' value="', esc_attr( $val ), '"',
		' readonly="readonly"',
		' /> ';
		echo '<input',
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$var_esc` value is already escaped.
		' id="wpseo_', $var_esc, '_button"',
		' class="wpseo_image_upload_button button"',
		' type="button"',
		' value="', esc_attr__( 'Upload Image', 'yoastseo-amp' ), '"',
		' data-target-id="', esc_attr( $id_field_id ), '"',
		disabled( $this->is_control_disabled( $var ), true, false ),
		' /> ';
		echo '<input',
		' class="wpseo_image_remove_button button"',
		' type="button"',
		' value="', esc_attr__( 'Clear Image', 'yoastseo-amp' ), '"',
		disabled( $this->is_control_disabled( $var ), true, false ),
		' />';
		echo '<input',
		' type="hidden"',
		' id="', esc_attr( $id_field_id ), '"',
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$var_esc` value is already escaped.
		' name="', esc_attr( $this->option_name ), '[', $var_esc, '_id]"',
		' value="', esc_attr( $id_value ), '"',
		' />';
		echo '</span>';
		echo '<br class="clear"/>';
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

		echo '<div class="switch-container', esc_attr( $help_class ), '">',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: The `$help` value is already escaped.
			'<span class="switch-light-visual-label' . esc_attr( $strong_class ) . '" id="', esc_attr( $var . '-label' ), '">', esc_html( $label ), '</span>' . $help,
		'<label class="', esc_attr( $class ), '"><b class="switch-yoast-seo-jaws-a11y">&nbsp;</b>',
		'<input type="checkbox" aria-labelledby="', esc_attr( $var . '-label' ), '" id="', esc_attr( $var ), '" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" value="on"', checked( $val, 'on', false ), disabled( $this->is_control_disabled( $var ), true, false ), '/>',
		'<span aria-hidden="true">
			<span>', esc_html( $off_button ) ,'</span>
			<span>', esc_html( $on_button ) ,'</span>
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
		echo '<textarea cols="' . esc_attr( $attr['cols'] ) . '" rows="' . esc_attr( $attr['rows'] ) . '" class="textinput ' . esc_attr( $attr['class'] ) . '" id="' . esc_attr( $var ) . '" name="' . esc_attr( $this->option_name ) . '[' . esc_attr( $var ) . ']"', disabled( $this->is_control_disabled( $var ), true, false ), '>' . esc_textarea( $val ) . '</textarea><br class="clear" />';
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
