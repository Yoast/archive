import BlockInstruction from "../../core/blocks/BlockInstruction";
import { ExtendedBlockConfiguration } from "../../type-adapters/ExtendedBlockConfiguration";

/**
 * Variation instruction.
 */
class Variation extends BlockInstruction {
	/**
	 * Passes on the options as configuration.
	 *
	 * @returns The configuration.
	 */
	configuration(): Partial<ExtendedBlockConfiguration> {
		return {
			variations: [
				this.options,
			],
		};
	}

	/**
	 * Returns whether or not this instruction should be included in the tree.
	 *
	 * @returns Whether or not to render this instruction.
	 */
	renderable(): boolean {
		return false;
	}
}

BlockInstruction.register( "variation", Variation );
