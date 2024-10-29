<?php 
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
global $wpdb;


// Check class already exist or not
if( !class_exists('AITGEN_ShortDesc_Handle')){
    
    class AITGEN_ShortDesc_Handle{

        // Your AJAX handler functions
        public function aitgen_shortDesc_action_callback() {
            // Verify the nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'generate_shortDesc_nonce')) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }
        
            $product_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
        
            // Get the product object
            $product = wc_get_product($product_id);
            $post = get_post($product_id);
        
            // Initialize variables
            $generated_desc_val = null;
        
            // Check if the product or post exists
            if ($product) {
                $generated_desc_val = !empty($product->get_description()) ? $product->get_description() : $product->get_title();
            } elseif ($post) {
                $generated_desc_val = !empty($post->post_description) ? $post->post_description : $post->post_title;
            }
        
            // Generate short description
            $generated_desc = aitgen_generateShortDescription($generated_desc_val);
        
            // Send JSON response
            wp_send_json_success(array(
                'msg' => 'success',
                'desc' => $generated_desc,
                'id' => $product_id
            ));
        
            wp_die();
        }
        

        public function aitgen_reGenShortDesc_callback() {

            // Verify the nonce
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'reGenShortDesc_nonce' ) ) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }

            // Get the ID from the AJAX request
            $product_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';

            // Get the product object
            $product = wc_get_product($product_id);
            $post = get_post($product_id);
        
            // Initialize variables
            $generated_desc_val = null;
    
            // Check if the product or post exists
            if ($product) {
                $generated_desc_val = !empty($product->get_description()) ? $product->get_description() : $product->get_title();
            } elseif ($post) {
                $generated_desc_val = !empty($post->post_description) ? $post->post_description : $post->post_title;
            }
            // Generate short description
            $generated_desc = aitgen_generateShortDescription($generated_desc_val);

            wp_send_json_success( array(
                'msg'  => 'success',
                'shortDesc' => $generated_desc,
                'id'       => $product_id
            ) );
            wp_die();
        }
        
        public function aitgen_update_shortDesc_callback() {
            // Verify the nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'updateShortDesc_nonce')) {
                wp_send_json_error(array('error' => 'Permission check failed'));
            }
        
            $product_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $dataType = isset($_POST['dataType']) ? sanitize_text_field($_POST['dataType']) : '';
            $updated_desc = isset($_POST['updated_desc']) ? sanitize_text_field($_POST['updated_desc']) : '';
        
            $updatePageUrl = '';
            $message = '';
            $error = '';
        
            // Check if the product ID is valid
            if ($product_id > 0) {
                // Update post/product data
                $updated_post_data = array(
                    'ID' => $product_id,
                    'post_excerpt' => $updated_desc, // Update only the short description
                );
    
                $updated_post_id = wp_update_post($updated_post_data);
        
                if (is_wp_error($updated_post_id)) {
                    $error = $updated_post_id->get_error_message();
                } else {
                    // Generate URL based on dataType
                    switch ($dataType) {
                        case 'product':
                        case 'post':
                        case 'page':
                            $updatePageUrl = admin_url('edit.php?post_type=' . $dataType);
                            break;
                        case 'product-single':
                            // Modify this according to your requirements
                            $updatePageUrl = '';
                            break;
                        case 'short-desc':
                            // Generate the URL for editing the post with the specified ID
                            $updatePageUrl = admin_url("post.php?post=${product_id}&action=edit");
                            break;
                        default:
                            $updatePageUrl = home_url(sanitize_title($updated_desc));
                            break;
                    }
                    $message = 'Short description updated successfully.';
                }
            } else {
                // Invalid product ID
                $error = 'Invalid product ID.';
            }
        
            wp_send_json_success(array(
                'msg' => $message ?: $error,
                'shortDesc' => $updated_desc,
                'id' => $product_id,
                'pageUrl' => $updatePageUrl
            ));
        
            wp_die();
        }
        
    }

}

