import React from "react";
import PropTypes from "prop-types";

export {
	StyledSection,
	StyledSectionBase,
	StyledHeading,
	Textfield,
	Input,
	Label,
	Progressbar,
	Section,
	Textarea,
} from "@yoast/components";


// Old form button for BC reasons. Replaced by @yoast/components/buttons/Button

/**
 * Represents the button HTML element.
 *
 * @param {Object} props The properties to use.
 * @returns {JSX} A representation of the button HTML element based on the passed props.
 * @constructor
 */
export const Button = ( props ) => {
	return (
		<button className={ props.className } type="button" onClick={ props.onClick }  { ...props.optionalAttributes }>{ props.text }</button>
	);
};

/**
 * Adds validation for the properties.
 *
 * @type {{text: string, className: string, onClick: function, optionalAttributes: object}}
 */
Button.propTypes = {
	text: PropTypes.string.isRequired,
	className: PropTypes.string,
	onClick: PropTypes.func.isRequired,

	optionalAttributes: PropTypes.object,
};

