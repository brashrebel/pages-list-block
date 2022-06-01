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

function plb_replace_email_body_text( $emailBody ) {

	$find = plb_smartcode_string();
	$contents = str_replace( $find, "OUTPUT WILL GO HERE", $emailBody );
	return $contents;
}
add_filter( 'fluentcrm_email_body_text', 'plb_replace_email_body_text', 10, 1 );

add_filter( 'fluentcrm_parse_campaign_email_text', 'plb_replace_email_body_text', 10, 1 );

function plb_add_smartcode_to_list( $codes ) {
	$codes[plb_smartcode_string()] = 'Pages List';
	return $codes;
}
add_filter( 'fluentcrm_general_smartcodes', 'plb_add_smartcode_to_list', 10, 1 );