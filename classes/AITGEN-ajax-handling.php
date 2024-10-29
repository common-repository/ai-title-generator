<?php 
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
global $wpdb;

// Check class already exist or not
if( !class_exists('AITGEN_Ajax_Handle')){
    
    class AITGEN_Ajax_Handle{
        // Api key validator
        public function aitgen_check_openai_api_key() {

            global $wpdb;

            // Verify the nonce
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'openApi_nonce' ) ) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }

            // Retrieve the API key from the POST data
            $api_key = isset($_POST['apiKey']) ? sanitize_text_field($_POST['apiKey']) : '';
            // Insert Valid API Key to Database.
            $table_name = "{$wpdb->prefix}aitgen_api_credentials";
            $table_exists = $wpdb->get_var($wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );

            $existing_key = $wpdb->get_row(
                $wpdb->prepare("SELECT apiKey FROM {$wpdb->prefix}aitgen_api_credentials WHERE apiKey = %s", $api_key),
                ARRAY_A
            );
            // Set the OpenAI API endpoint
            $url = 'https://api.openai.com/v1/chat/completions';
        
            // Set the request headers
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            );
        
            // Specify the conversation messages with a short user message
            $data = array(
                'model' => 'gpt-3.5-turbo',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => 'Generate a concise response.'
                    ),
                ),
                'max_tokens' => 3,
                'temperature' => 0.7,
            );
        
            // Send a request to the OpenAI API
            $response = wp_remote_post($url, array(
                'headers' => $headers,
                'body' => wp_json_encode($data),
            ));
        
            if (is_wp_error($response)) {
                // Handle error
                wp_send_json_error(array('error' => $response->get_error_message()));
            }
        
            // Retrieve the response body and status code
            $body = wp_remote_retrieve_body($response);
            $status_code = wp_remote_retrieve_response_code($response);

            $status = null;
            if( $status_code == 200 ){
                if ($existing_key) {
                    // API key already exists, update the status or other fields as needed
                    $wpdb_result = $wpdb->update(
                        $table_name,
                        array('status' => 1), // Update the status or other fields
                        array('apiKey' => $api_key)
                    );
                } else {
                    // API key doesn't exist, insert a new record
                    $wpdb_result = $wpdb->insert(
                        $table_name,
                        array(
                            'apiKey' => $api_key,
                            'status' => 1
                        )
                    );
                }

             $status = true;

            }else {
                $status = false;
            }
        
            // Send a JSON response with the status
            wp_send_json_success(
                array('status' => $status)
            );

            wp_die();
        }

    }

}

