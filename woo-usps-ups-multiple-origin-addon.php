<?php
/**
Plugin Name: USPS and UPS Unique Origin Addon
Description: Enable multiple shipping origin locations, requires USPS calculator plugin
Author: John Bland
Version: 1.2
Author URI: http://johnisbland.com
*/

add_filter('woocommerce_package_rates','platinum_overwrite_usps',100,2);
function platinum_overwrite_usps($rates,$package) {

	$usps = NULL;
	$ups = NULL;

	if ( class_exists( 'WF_Shipping_USPS' ) ) {
		$usps = new WF_Shipping_USPS();
	}
	if ( class_exists( 'WF_Shipping_UPS' ) ) {
		$ups = new WF_Shipping_UPS();
	}

	if ( count( $package['contents'] ) > 1 ) {
		$originPostcode = reset( $package['package_grouping_value'] );
	} else {
		$originPostcode = $package['package_grouping_value'];
	}


	if ( $usps != NULL ) {
		$usps->origin = $originPostcode;
		$usps->calculate_shipping( $package );
		$uspsRates = $usps->rates;

	    foreach ($rates as $rate) {

	    	foreach ( $uspsRates as $uspsRate ) {
	    		if ( $rate->id == $uspsRate->id ) {
	    			$rate->cost = $uspsRate->cost;
	    		}
	    	}

	    }
	}

	if ( $ups != NULL ) {
		$ups->origin_postcode = $originPostcode;
		$ups->calculate_shipping( $package );
		$upsRates = $ups->rates;

	    foreach ($rates as $rate) {

	    	foreach ( $upsRates as $upsRate ) {
	    		if ( $rate->id == $upsRate->id ) {
	    			$rate->cost = $upsRate->cost;
	    		}
	    	}

	    }
	}
    return $rates;
}
