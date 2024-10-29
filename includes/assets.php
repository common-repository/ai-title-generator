<?php
/*
* @Pakage AI Title Generator.
*/
if( !defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}

// Check if function already exists.
if (!function_exists('aitgen_admin_assets')) {
    // Admin Assets
    function aitgen_admin_assets() {
        // Check the current screen
        $screen = get_current_screen();

        // Check if current screen matches any of the specified locations
        // if ( is_admin() && $screen && ($screen->id === 'toplevel_page_ai-title-generator' || $screen->id === 'edit-product' || $screen->id === 'edit-post')) {
            // Enqueue Bootstrap CSS
            wp_enqueue_style('aitgen-admin-bootsrap-css', AITGEN_DIR_URI .'assets/css/bootstrap.min.css');
            wp_enqueue_style('aitgen-loader-style', AITGEN_DIR_URI .'assets/css/aitgen-loader.css');
            wp_enqueue_style('aitgen-admin-style', AITGEN_DIR_URI .'assets/css/aitgen-admin-style.css');

            // Enqueue Bootstrap JS
            wp_enqueue_script('aitgen-admin-bootsrap-js', AITGEN_DIR_URI .'assets/js/bootstrap.min.js', array('jquery'), 'v5.3.3', true);
            // Enqueue custom script
            wp_enqueue_script('aitgen-admin-script',  AITGEN_DIR_URI .'assets/js/aitgen-admin-script.js', array('jquery'), '1.0.0', true);

            // Localize scripts
            wp_localize_script('aitgen-admin-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('generate_title_nonce')));
            wp_localize_script('aitgen-admin-script', 'ajaxObj', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('generate_reGenTitle_nonce')));
            wp_localize_script('aitgen-admin-script', 'UpdateObj', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('update_reGenTitle_nonce')));
            wp_localize_script('aitgen-admin-script', 'postAjaxObj', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('postReGenTitle_nonce'), 'action' => 'regenerate_postTitle_action'));

            // Short Description
            wp_localize_script('aitgen-admin-script', 'shortDsc', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('generate_shortDesc_nonce'), 'action' => 'generate_shortDsc_action'));

            wp_localize_script('aitgen-admin-script', 'reGenShortDsc', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('reGenShortDesc_nonce') ));
            wp_localize_script('aitgen-admin-script', 'updateShortDsc', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('updateShortDesc_nonce') ));

            wp_localize_script('aitgen-admin-script', 'openApiObj', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('openApi_nonce'), 'action' => 'aitgen_openApiCheck_action'));
        // }
    }
}

