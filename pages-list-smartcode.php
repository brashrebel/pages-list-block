<?php
/**
 * Plugin Name:     Pages List Smartcode
 * Description:     Custom plugin to add lists of pages listings are featured on to FluentCRM email campaigns.
 * Version:         0.1
 * Author:          Kyle Maurer
 * Author URI:      https://kyleblog.net
 * Text Domain:     pages-list-block
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pls_smartcode_string() {
	return '{{vm.pages_list}}';
}

function pls_replace_email_body_text( $emailBody, $subscriber ) {

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

				$list_display .= '<h2>' . $title . '</h2>';

				$pages = wp_get_post_terms( $listing, $name );
				// Create an unordered list of the terms in each taxonomy
				if ( ! empty( $pages ) ) {
					$list_display .= '<ul>';
					foreach ( $pages as $page ) {
						$list_display .= '<li>' . $page->name . '</li>';
					}
					$list_display .= '</ul>';
				} else {
					$list_display .= 'This listing is not featured on any of these pages.';
				}
			}
		}
	} else {
		$list_display = "You currently have no listings on VisitMaine.net.";
	}

	// This is the smartcode string which, if found in the email body, will be replaced with our output
	$find = pls_smartcode_string();
	// Here we are replacing the smartcode string with our output
	$contents = str_replace( $find, $list_display, $emailBody );
	return $contents;
}
add_filter( 'fluentcrm_parse_campaign_email_text', 'pls_replace_email_body_text', 10, 2 );

function pls_add_smartcode_to_list( $codes ) {
	$codes[pls_smartcode_string()] = 'Pages List';
	return $codes;
}
add_filter( 'fluentcrm_general_smartcodes', 'pls_add_smartcode_to_list', 10, 1 );