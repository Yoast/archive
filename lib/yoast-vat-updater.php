<?php

class Yoast_VAT_Updater {

	/**
	 * The EDD option we use for tax rates
	 *
	 * @var string
	 */
	private $edd_tax_rate_option = 'edd_tax_rates';

	/**
	 * Used for storing and altering the EDD tax rates
	 *
	 * @var array
	 */
	private $edd_tax_rate_option_value = array();

	/**
	 * The EDD tax rates sorted by country
	 *
	 * @var array
	 */
	private $edd_tax_rate_by_country = array();

	/**
	 * The JSONVAT details by country
	 *
	 * @var array
	 */
	private $vat_details = array();

	/**
	 * The version of the JSON vat we're updating to
	 *
	 * @var string
	 */
	private $current_version;

	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( $this->retrieve_vat_rates() ) {
			if ( WP_DEBUG ) {
				echo 'Updating VAT rates.';
			}
			$this->retrieve_edd_vat_details();
			$this->fix_edge_cases();
			$this->compare_vat_details();
			$this->update_vat_details();
		} else {
			if ( WP_DEBUG ) {
				echo 'VAT rates didn\'t change.';
			}
		}
	}

	/**
	 * Fix weird cases where we need to apply the VAT of another region to this region
	 */
	private function fix_edge_cases() {
		// Monaco uses the same VAT as France
		$this->vat_details[ 'MC' ] = $this->vat_details[ 'FR' ];
	}

	/**
	 * Compare our current rates with the one from JSONVAT.com
	 */
	private function compare_vat_details() {
		foreach( $this->edd_tax_rate_by_country as $country => $settings ) {
			if ( $this->vat_details[ $country ] !== $settings['rate'] ) {
				$this->update_local_vat_details( $country, $settings, $this->vat_details[ $country ] );
			}
		}
	}

	/**
	 * Update our copy of the EDD tax rates with the new rate
	 *
	 * @param string $country
	 * @param array  $settings
	 * @param int    $new_rate
	 */
	private function update_local_vat_details( $country, $settings, $new_rate ) {
		if ( $this->edd_tax_rate_option_value[ $settings['index'] ]['country'] == $country ) {
			$this->edd_tax_rate_option_value[ $settings['index'] ]['rate'] = (string) $new_rate;
		}
	}

	/**
	 * Update EDD with our new settings
	 */
	private function update_vat_details() {
		update_option( $this->edd_tax_rate_option, $this->edd_tax_rate_option_value );
		set_transient( 'yst_jsonvat_version', $this->current_version );
	}

	/**
	 * Retrieve the current tax rates from Easy Digital Downloads' settings
	 */
	private function retrieve_edd_vat_details() {
		$this->edd_tax_rate_option_value = get_option( $this->edd_tax_rate_option );
		if ( ! is_array( $this->edd_tax_rate_option_value ) ) {
			wp_die( "You're trying to update VAT rates but you don't have any countries in your EDD installs tax settings, or you might not even have EDD installed." );
		}
		$i = 0;
		foreach( $this->edd_tax_rate_option_value as $edd_tax_rate ) {
			$this->edd_tax_rate_by_country[ $edd_tax_rate['country'] ] = array(
				'rate' => $edd_tax_rate['rate'],
				'index' => $i,
			);
			$i++;
		}
	}

	/**
	 * Retrieve the tax rates from JSONvat.com
	 *
	 * @return bool Indicating whether or not there is an update
	 */
	private function retrieve_vat_rates() {
		$resp = wp_remote_get( 'http://jsonvat.com/' );
		if ( 200 === wp_remote_retrieve_response_code( $resp ) ) {
			$json 		 = wp_remote_retrieve_body( $resp );
			$vat_details = json_decode( $json );

			// Compare the version we've retrieved with the version we know.
			$last_known_version = (string) get_transient( 'yst_jsonvat_version' );
			if ( $last_known_version !== $vat_details->version ) {
				$this->parse_vat_response( $vat_details );
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Parse the response from JSONvat.com and update the transient to the last known version.
	 *
	 * @param object $vat_details Object retrieved from JSONvat.com
	 */
	private function parse_vat_response( $vat_details ) {
		foreach( $vat_details->rates as $rate ) {
			$last_period = '0000-00-00';
			$current_date = date( 'Y-m-d' );
			foreach( $rate->periods as $period ) {
				if ( $period->effective_from > $last_period && $period->effective_from < $current_date ) {
					$this->vat_details[ $rate->country_code ] = $period->rates->standard;
					$last_period = $period->effective_from;
				}
			}
		}
		$this->current_version = $vat_details->version;
	}
}