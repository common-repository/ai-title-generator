<?php 
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
global $wpdb;


// Check class already exist or not
if( !class_exists('AITGEN_Title_Handle')){
    
    class AITGEN_Title_Handle{

        // Your AJAX handler function
        public function aitgen_title_action_callback() {
        
            // Verify the nonce
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'generate_title_nonce' ) ) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }

            $product_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
            // Get the product object
            $product = wc_get_product($product_id);
            $post    = get_post($product_id);

            // Initialize variables
            $generated_title = '';
        
            // Check if the product or post exists
            if ( $product ) {
                $generated_title = $generatedTitle ? $generatedTitle : $product->get_name();
            } elseif ($post) {
                $generated_title = $generatedTitle ? $generatedTitle : $post->post_title;
            }
        
            // Get product tags
            $product_keywords = NULL; 

            wp_send_json_success( array(
                'msg'      => 'success',
                'title'    => $generated_title,
                'id'       => $product_id
            ) );
            
            wp_die();
        }


        public function aitgen_reGenTitle_callback() {

            // Verify the nonce
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'generate_reGenTitle_nonce' ) ) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }

            // Get the ID from the AJAX request
            $product_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
            $generatedTitle = isset($_POST['generatedTitle']) ? sanitize_text_field($_POST['generatedTitle']) : '';

            // Get the product object
            $product = wc_get_product($product_id);
            $post = get_post($product_id);


            // Initialize variables
            $product_name = '';
            $post_title = '';
        
            // Check if the product or post exists
            if ( $product ) {
                $product_name = $generatedTitle ? $generatedTitle : $product->get_name();
            } elseif ($post) {
                $post_title = $generatedTitle ? $generatedTitle : $post->post_title;
            }
        
            // Get product tags
            $product_keywords = NULL; 
        
            // Generate title based on product or post name
            $generated_title = aitgen_generateProductTitle($product_name ?: $post_title, $product_keywords);

            $generated_title = explode("\n", $generated_title);

            wp_send_json_success( array(
                'msg'  => 'success',
                'title'    => $generated_title,
                'id'       => $product_id
            ) );
            wp_die();
        }


        public function aitgen_postReGenTitle_callback() {
            // Verify the nonce
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'postReGenTitle_nonce' ) ) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }

            // Get the ID from the AJAX request
            $product_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
            $generatedTitle = isset($_POST['generatedTitleVal']) ? sanitize_text_field($_POST['generatedTitleVal']) : '';
        
            // Get the product object
            $product = wc_get_product($product_id);
            $post = get_post($product_id);
        
            // Initialize variables
            $product_name = '';
            $post_title = '';
        
            // Check if the product or post exists
            if ($product) {
                $product_name = $generatedTitle ? $generatedTitle : $product->get_name();
            } elseif ($post) {
                $post_title = $generatedTitle ? $generatedTitle : $post->post_title;
            }
        
            // Get product tags
            $product_keywords = NULL;
        
            // Generate title based on product or post name
            $generated_title = aitgen_generateProductTitle($product_name ?: $post_title, $product_keywords);
            $generated_title = explode("\n", $generated_title);

            
            wp_send_json_success( array(
                'msg'  => 'success',
                'title'    => $generated_title,
                'id'       => $product_id
            ) );
            
            // Always use die() to prevent extra output
            wp_die();
        }
        

        public function aitgen_update_product_title_callback() {

            // Verify the nonce
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'update_reGenTitle_nonce' ) ) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }

            $product_id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $dataType       = isset($_POST['dataType']) ? sanitize_text_field($_POST['dataType']) : '';
            $updated_title  = isset($_POST['updated_title']) ? sanitize_text_field($_POST['updated_title']) : '';

            $updatePageUrl = null;
            $success = null;
            $error = null;

            // Check if the product ID is valid
            if ($product_id > 0) {
                // Get existing product data
                $existing_product = get_post($product_id);
        
                // Update only the specified fields
                $updated_product_data = array(
                    'ID'         => $product_id,
                    'post_title' => $updated_title ? $updated_title : $existing_product->post_title,
                );
                // Update the product
                $updated_product_id = wp_update_post($updated_product_data);
        
                if (is_wp_error($updated_product_id)) {
                    $error[] = $updated_product_id->get_error_message();
                } else {
                    $updated_slug = sanitize_title($updated_title);
                    wp_update_post(array('ID' => $product_id, 'post_name' => $updated_slug));

                    if( $dataType ==="product" || $dataType ==="post" || $dataType ==="page" ){
                        // Get the product listing page URL
                        $updatePageUrl = admin_url('edit.php?post_type='.$dataType.'');
                    }elseif($dataType ==="product-single"){
                        $updatePageUrl =''; //home_url('product/'.$updated_slug.'');
                    }else{
                        $updatePageUrl = home_url($updated_slug);
                    }
                   // Update successful
                    $success = 'Product title updated successfully.';
                }
            } else {
                // Invalid product ID
                $error = 'Invalid product ID.';
            }

            wp_send_json_success( array(
                'msg'    => $success ?? $error,
                'title'  => $updated_title,
                'id'     => $product_id,
                'pageUrl'=> $updatePageUrl
            ) );
        
            wp_die();
        }        

    }

}

