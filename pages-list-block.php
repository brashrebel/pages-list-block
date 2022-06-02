<?php
/**
 * Plugin Name:     Pages List Block
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

function plb_smartcode_string() {
	return '{{vm.pages_list}}';
}

function plb_replace_email_body_text( $emailBody, $subscriber ) {

	// Get this subscriber's listing IDs from the custom field
	$listing_ids_field = fluentcrm_get_subscriber_meta( $subscriber->id, 'listing_ids', '' );
	// Turn the string of IDs into an array
	$listing_ids = explode( "|", $listing_ids_field );
	$list_display = '';
	
	// Loop over the listing IDs and populate the display variable with the desired output
	if ( ! empty( $listing_ids ) ) {
		foreach ( $listing_ids as $listing ) {
			$post = get_post( $listing );
			$list_display .= $post->post_title . "<br/>";
		}
	} else {
		$list_display = "You currently have no listings on VisitMaine.net.";
	}

	// This is the smartcode string which, if found in the email body, will be replaced with our output
	$find = plb_smartcode_string();
	// Here we are replacing the smartcode string with our output
	$contents = str_replace( $find, $list_display, $emailBody );
	return $contents;
}
//add_filter( 'fluentcrm_email_body_text', 'plb_replace_email_body_text', 10, 2 );
add_filter( 'fluentcrm_parse_campaign_email_text', 'plb_replace_email_body_text', 10, 2 );

function plb_add_smartcode_to_list( $codes ) {
	$codes[plb_smartcode_string()] = 'Pages List';
	return $codes;
}
add_filter( 'fluentcrm_general_smartcodes', 'plb_add_smartcode_to_list', 10, 1 );