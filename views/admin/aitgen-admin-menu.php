<?php
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
// Check if class exist or not
function aitgen_settings_admin_menu_page() {
    add_menu_page(
        __('AI Title Generator', 'ai-title-generator'), 
        __('AI Title Generator', 'ai-title-generator'), 
        'manage_options',
        'ai-title-generator', 
        'aitgen_menu_callback',
         'dashicons-image-rotate',
        30 
    );
}

// The callback function to render the menu page
if( !function_exists('aitgen_menu_callback')){
    function aitgen_menu_callback() { ?>
        <div id="wpbody" role="main">
            <div id="wpbody-content">
                    <div class="wrap">
                        <div class="aitgen_custom_wrapper">
                            <!-- Start Custom Wrapper -->
                            <div class="container-fluid">
    
                                <div class="row">
                                    <div class="float-none m-4 ms-2">
                                        <h1><?php esc_html_e( 'AI Title Generator', 'ai-title-generator' ); ?></h1>
                                    </div>
                                </div>
                                <div class="row">
    
                                    <?php 
                                        global $wpdb;
                                        
                                        // Check if the table exists
                                        $table_name = "{$wpdb->prefix}aitgen_api_credentials";
                                        $table_exists = $wpdb->get_var($wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
    
                                        $existing_key = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aitgen_api_credentials");
                            
                                        $apiKey = isset($existing_key->apiKey) ? sanitize_text_field($existing_key->apiKey) : '';
                                        $status = isset($existing_key->status) ? sanitize_text_field($existing_key->status) : 0;
                                    
                                    ?>
                                    <!-- Tab Content -->
                                    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                                        <div class="tab-content aitgen-content">
                                            <div class="tab-pane fade show active" id="openApiLicense-content">
                                                <h2 class="mt-2 pb-1"><?php esc_html_e( 'ChatGPT API Key', 'ai-title-generator'); ?></h2>

                                                <hr class="pb-3">
                                                <div class="col-auto col-sm-4 mt-2">
                                                    <input type="text" class="form-control form-control-md <?php echo $status == 1 ? 'is-valid' : ''; ?>" id="openApiLicenseKey" placeholder="API Key" value="<?php echo esc_attr($apiKey); ?>" >
                                                    <p class="aitgen_helper_text fs-6 text-secondary mb-0">
                                                        <?php esc_html_e( "Enter OpenAI's API Key here to full functional the plugin.", "ai-title-generator" ); ?>
                                                    </p>
                                                </div>
            
                                                <div class="row g-3 mt-2">
                                                    <div class="col-auto">
                                                        <button type="button" id="openApiCheck" class="btn btn-primary <?php echo esc_attr( $status == 1 ? 'btn-success' : 'btn-primary' ); ?> mb-2"><?php echo esc_html( $status == 1 ? 'Verified' : 'Verify' ); ?></button>
                                                    </div>
                                                </div>

                                                <div class="aitgen_api_instruction mt-4">
                                                    <h5 class="api_instruction_title"><?php esc_html_e( "How to Generate OpenAI API Key ?", "ai-title-generator" ); ?></h5>

                                                    <div class="api_instruction_list">
                                                        <ol>
                                                            <li><?php esc_html_e( 'Go to OpenAI\'s Platform website at', 'ai-title-generator' ); ?> <a href="<?php echo esc_url( 'https://platform.openai.com' ); ?>" target="_blank"><?php esc_html_e( 'OpenAI', 'ai-title-generator' ); ?></a> <?php esc_html_e( 'and sign in with an OpenAI account.', 'ai-title-generator' ); ?></li>
                                                            <li><?php esc_html_e( 'Click your profile icon at the top-right corner of the page and select "View API Keys."', 'ai-title-generator' ); ?></li>
                                                            <li><?php esc_html_e( 'Click "Create New Secret Key" to generate a new API key.', 'ai-title-generator' ); ?></li>
                                                        </ol>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </main>
    
                                </div>
                            </div>
                        </div>
                        <!--/ End Custom Wrapper -->
                    </div>
                <!--/ End WP Wrap -->
                <div class="clear"></div>
            </div><!-- wpbody-content -->
            <div class="clear"></div>
        </div>
        <!--End Main -->
    <?php
    }    
}



















