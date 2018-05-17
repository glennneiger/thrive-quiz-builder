<?php
/**
 * Created by PhpStorm.
 * User: Ovidiu
 * Date: 3/15/2018
 * Time: 8:40 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

global $wpdb;

$users = tqb_table_name( 'users' );

$sql = "ALTER TABLE {$users} DROP COLUMN ip_address;";
$wpdb->query( $sql );

return true;
