<?php
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}

// Check class already exist or not
if( !class_exists('AITGEN_DB_Management')){
    class AITGEN_DB_Management{
    
        // Check License Credentials
        public function aitgen_api_credentials() {
            global $wpdb;
    
            // Check if the table exists
            $table_name = "{$wpdb->prefix}aitgen_api_credentials";
            $table_exists = $wpdb->get_var($wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
    
            // Create the table if it does not exist
            if ( !$table_exists ) {
                $charset_collate = $wpdb->get_charset_collate();
    
                $sql = "CREATE TABLE $table_name (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `apiKey` VARCHAR(200) NOT NULL,
                        `status` VARCHAR(30) NULL,
                        PRIMARY KEY (`id`)
                ) $charset_collate;";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }
    } // End of Class.
    
}

