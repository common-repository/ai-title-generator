<?php
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
// Image / Title Generating options for post / product list in admin / dashboard menu
if( !function_exists('aitgen_admin_postProduct_custom_button')){
    function aitgen_admin_postProduct_custom_button($actions, $post) {

        // Check if the post type is 'product', 'post'
        if ($post->post_type === 'product' || $post->post_type === 'post') {
            // Add your custom buttons
            $custom_buttons = array(
                '<a href="" class="aitgen_generate_title" data-title="'.$post->post_title.'" data-type="'.esc_attr($post->post_type).'" data-id="'.esc_attr($post->ID).'" >'.esc_html__('AI Title Generator', 'ai-title-generator').'</a>',
            );

            // Find the position of 'clone' in the $actions array
            $clone_position = array_search('clone', array_keys($actions));

            // Insert the custom buttons after 'clone'
            $i = 1;
            foreach ($custom_buttons as $custom_button) {
                $actions = array_slice($actions, 0, $clone_position + 2 + $i, true) +
                    array('custom_button_'.$i => $custom_button) +
                    array_slice($actions, $clone_position + 2 + $i, null, true);
                $i++;
            }
        }
        return $actions;
    }
}

// Popup Box for admin 
if( !function_exists('aitgen_title_generator_box_admin')){
    function aitgen_title_generator_box_admin() {

        $current_screen = get_current_screen();
        // Check if the current screen is related to the 'product' post type
        if ($current_screen && $current_screen->post_type === 'product' || $current_screen && $current_screen->post_type === 'post' || $current_screen ) {
            aitgen_titleRegenerate_modal();
            aitgen_shortDescriptionRegenerate_modal();
        }
    }
}
// Title Regenerator Modal
if( !function_exists('aitgen_titleRegenerate_modal')){
    function aitgen_titleRegenerate_modal(){
        ?>
            <div class="modal fade" id="aitgen_customAlert" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog aitgen-modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title"><?php esc_html_e( 'Generated Content', 'ai-title-generator' ) ?></h5>
                            <button type="button" class="btn-close py-1" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="fs-6"><?php esc_html_e( 'Title:', 'ai-title-generator' ) ?> <input type="text" style="width: 100%" class="generated_title"></p>

                            <div class="aitgen_suggested_title_wrapper">
                                <ul class="aitgen_suggested_title_list">
                                </ul>
                                <!-- Loader -->
                                <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                </div>
                                <!-- ./Loader -->
                            </div>

                            <div class="modal-footer">
                                <span class="aitgen_update_msg text-secondary d-none"><?php esc_html_e( 'Updated', 'ai-title-generator' ) ?></span>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'ai-title-generator' ) ?></button>
                                <button type="button" class="btn btn-secondary aitgen_regenerate_title"><?php esc_html_e( 'Regenerate', 'ai-title-generator' ) ?></button>
                                <button type="button" class="btn btn-success aitgen_update_title" data-bs-dismiss="modal"><?php esc_html_e( 'Confirm', 'ai-title-generator' ) ?></button>
                            </div>
                    </div>
                </div>
            </div>
        <?php
    }
}



// Short Description Regenerator Modal
if( !function_exists('aitgen_shortDescriptionRegenerate_modal')){
    function aitgen_shortDescriptionRegenerate_modal(){
        ?>
            <div class="modal fade" id="aitgen_shortDesc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog aitgen-modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title"><?php esc_html_e( 'Generated Short Description', 'ai-title-generator' ) ?></h5>
                            <button type="button" class="btn-close py-1" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <p class="fs-6"><?php esc_html_e( 'Short Description:', 'ai-title-generator' ) ?></p>
                            <div class="aitgen_short_description_wrapper">
                                <textarea  cols="30" rows="7" class="form-control aitgen_short_description" id="aitgen_short_description">
                                </textarea>
                                <!-- Loader -->
                                <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                </div>
                                <!-- ./Loader -->
                            </div>
                            <div class="modal-footer">
                                <span class="aitgen_update_msg text-secondary d-none"><?php esc_html_e( 'Updated', 'ai-title-generator' ) ?></span>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'ai-title-generator' ) ?></button>
                                <button type="button" class="btn btn-secondary aitgen_regenerate_shortDesc"><?php esc_html_e( 'Regenerate', 'ai-title-generator' ) ?></button>
                                <button type="button" class="btn btn-success aitgen_update_shortDsc" data-bs-dismiss="modal"><?php esc_html_e( 'Update', 'ai-title-generator' ) ?></button>
                            </div>
                    </div>
                </div>
            </div>
        <?php
    }
}

/*===== Loader Content ======
============================*/

// backend full window loader
add_action('admin_footer', 'aitgen_blur_container_to_wpcontent');
if( !function_exists('aitgen_blur_container_to_wpcontent')){
    function aitgen_blur_container_to_wpcontent() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                // Create the content to be inserted
                var blurContainer = '<div class="blur-container"><div class="loader"></div></div>';
    
                // Append the content to the #wpcontent container
                $('#wpcontent').append(blurContainer);
            });
        </script>
        <?php
    }
    
}


