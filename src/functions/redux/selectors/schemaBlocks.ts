import { SchemaBlocksState, SchemaBlocksDefaultState } from "../SchemaBlocksState";
import { BlockValidationResult } from "../../../core/validation";
import { recursivelyFind } from "../../validators/recursivelyFind";
import logger from "../../logger";

export type ClientIdValidation = Record<string, BlockValidationResult>;

/**
 * The schema validation results.
 *
 * @param {object} state The current state.
 *
 * @returns {Record<string, BlockValidationResult>} The schema blocks validation results.
 */
export function getSchemaBlocksValidationResults( state: SchemaBlocksState ): ClientIdValidation {
	return state.validations || SchemaBlocksDefaultState.validations;
}

/**
 * Recursively traverses a BlockValidationResult's issues to finds the validation results for a specific clientId.
 * @param state    The entire Schema Blocks state.
 * @param clientId The ClientId of the block you want validation results for.
 * @returns The BlockValidationResult matching the clientId or null if none were found.
 */
export function getValidationResultForClientId( state: SchemaBlocksState, clientId: string ): BlockValidationResult {
	const stored = getSchemaBlocksValidationResults( state );
	logger.debug( "stored validations:", stored );
	const validationResults = Object.values( stored );

	return recursivelyFind( validationResults, ( result ) => result.clientId === clientId );
}

/**
 * Finds all validation results that match the names of given blocks.
 *
 * @param state      The entire Schema Blocks state.
 * @param blockNames The set of blocknames you're looking for.
 *
 * @returns The validation results for the list of given blocks.
 */
export function getValidationsForBlockNames( state: SchemaBlocksState, blockNames?: string[] ): BlockValidationResult[] {
	const validations = getSchemaBlocksValidationResults( state );
	return Object.values( validations ).filter( validation => blockNames.includes( validation.name ) );
}

