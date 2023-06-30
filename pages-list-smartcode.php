<?php
/**
 * Plugin Name:     Pages List Smartcode
 * Description:     Custom plugin to add lists of pages which listings are featured on to FluentCRM email campaigns.
 * Version:         0.2
 * Author:          Kyle Maurer
 * Author URI:      https://kyleblog.net
 * Text Domain:     pages-list-block
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output for pages list smartcode
 */
function pls_list_pages( $subscriber ) {

	// Get this subscriber's listing IDs from the custom field
	$listing_ids_field = fluentcrm_get_subscriber_meta( $subscriber->id, 'listing_ids', '' );
	// Turn the string of IDs into an array
	$listing_ids = explode( "|", $listing_ids_field );
	$list_display = '';

	// Loop over the listing IDs and populate the display variable with the desired output
	if ( ! empty( $listing_ids ) ) {
		foreach ( $listing_ids as $listing ) {
			$post = get_post( $listing );
			$list_display .= "<h1>" . $post->post_title . "</h1>";
			// Manually building an array of the taxonomies we want with nice looking titles
			$taxonomies = array(
				'Cities' => 'cities',
				'Regions' => 'regions',
				'Business Categories' => 'business_category',
				'Nearby Cities' => 'nearby_cities',
				'Wedding Categories' => 'wedding_category',
				'Camping Categories' => 'camping_category',
			);
			// Loop over that list of taxonomies and display each title
			foreach ( $taxonomies as $title => $name ) {

				$pages = wp_get_post_terms( $listing, $name );
				// Create an unordered list of the terms in each taxonomy
				if ( ! empty( $pages ) ) {
					$list_display .= '<h2>' . $title . '</h2>';
					$list_display .= '<ul>';
					foreach ( $pages as $page ) {
						$list_display .= '<li>' . $page->name . '</li>';
					}
					$list_display .= '</ul>';
				}
			}
		}
	} else {
		$list_display = "You currently have no listings on VisitMaine.net.";
	}
	return $list_display;
}

/**
 * Register smartcode
 * https://developers.fluentcrm.com/modules/smart-code/
 */
add_action('fluentcrm_loaded', function () {
    $key = 'visit_maine';
    $title = 'Visit Maine';
    $shortCodes = [
        'pages_list' => 'List Customer Pages',
    ];
    $callback = function ($code, $valueKey, $defaultValue, $subscriber) {
        if ($valueKey == 'pages_list') {
            return pls_list_pages( $subscriber );
        }
        return $defaultValue; // default value works in case of invalid value key
    };

    FluentCrmApi('extender')->addSmartCode($key, $title, $shortCodes, $callback);
});