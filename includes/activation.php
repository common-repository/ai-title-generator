<?php
/*
* @Pakage AI Title Generator.
*/

if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
// Check if function already exist or Not.
if( !function_exists('aitgen_api_credentials')){
    // Check if function already exist or Not.
    function aitgen_api_credentials() {
        
        global $wpdb;
        // Check if the table exists
        $table_name = "{$wpdb->prefix}aitgen_api_credentials";
        $table_exists = $wpdb->get_var($wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );

        // Create the table if it does not exist
        if ( $table_exists ) {

            $sql = "CREATE TABLE $table_name (
                    `id` int(10) NOT NULL,
                    `apiKey` VARCHAR(200) NOT NULL,
                    `status` VARCHAR(30) NULL,
                    PRIMARY KEY (`id`)
            ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}




