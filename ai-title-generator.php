<?php
/**
 * Plugin Name: AI Title Generator
 * Description: Generate / Regenerate your Title using AI on WordPress website.
 * Version: 1.0.1
 * Author: Spark Coder
 * Author URI: https://sparkcoder.com
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Slug: ai-title-generator
 * Text Domain: ai-title-generator
 * Domain Path: /languages
 */

if( ! defined( 'ABSPATH' ) ){
    exit; // Exit if directly access.
}
/*=======================
CONSTANT
=========================*/
define('AITGEN_DIR_PATH', plugin_dir_path( __FILE__ ));
define('AITGEN_DIR_URI', plugin_dir_url( __FILE__ ));

// Load Text Domain
function aitgen_plugin_textdomain() {
    load_plugin_textdomain('aitgen-title-changer', false, AITGEN_DIR_URI . '/languages');
}
// Hook into the init action and load the text domain
add_action('init', 'aitgen_plugin_textdomain');

require __DIR__ . '/vendor/autoload.php'; 

/*==================================
 // Check class exist or not
====================================*/
if( !class_exists('AITGEN_GENERATOR_CORE')){
    class AITGEN_GENERATOR_CORE{

        public function __construct(){
            /******* Includes Files *******/
            require( AITGEN_DIR_PATH.'includes/assets.php' );
            require( AITGEN_DIR_PATH.'views/admin/aitgen-conent.php' );

            // Dashboard Menu
            require( AITGEN_DIR_PATH.'views/admin/aitgen-admin-menu.php' );
            add_action( 'admin_menu', 'aitgen_settings_admin_menu_page' );

            /******* Includes Classes *******/
            require( AITGEN_DIR_PATH.'classes/AITGEN-ajax-handling.php' );  
            require( AITGEN_DIR_PATH.'classes/AITGEN-title-handling.php' );  
            require( AITGEN_DIR_PATH.'classes/AITGEN-short-desc-handling.php' );  
            require( AITGEN_DIR_PATH.'classes/AITGEN_dbManagement.php' );  

            /******* Hook *******/
            register_activation_hook( __FILE__, array( new AITGEN_DB_Management(), 'aitgen_api_credentials' ));
            // Assets load
            add_action( 'admin_enqueue_scripts', 'aitgen_admin_assets');
            // add_action( 'enqueue_block_editor_assets', 'aitgen_editor_assets' );
            // Load Content
            add_action('admin_footer', 'aitgen_title_generator_box_admin');

            add_filter('post_row_actions', 'aitgen_admin_postProduct_custom_button', 10, 2);
            // Hook into the page_row_actions filter to add the custom button for pages
            add_filter('page_row_actions', 'aitgen_admin_postProduct_custom_button', 10, 2);

            /*======= Ajax Request =====*/
            // API Key
            add_action('wp_ajax_aitgen_openApiCheck_action', array( new AITGEN_Ajax_Handle(), 'aitgen_check_openai_api_key') );

            // Title
            add_action('wp_ajax_aitgen_title_action', array( new AITGEN_Title_Handle(), 'aitgen_title_action_callback') );

            add_action('wp_ajax_regenerate_title_action', array( new AITGEN_Title_Handle(), 'aitgen_reGenTitle_callback') );

            add_action('wp_ajax_update_regenerate_title_action', array( new AITGEN_Title_Handle(), 'aitgen_update_product_title_callback') );

            // Short Description
            add_action('wp_ajax_aitgen_shortDsc_action', array( new AITGEN_ShortDesc_Handle(), 'aitgen_shortDesc_action_callback') );
            add_action('wp_ajax_reGenShortDsc_action', array( new AITGEN_ShortDesc_Handle(), 'aitgen_reGenShortDesc_callback') );
            add_action('wp_ajax_updateShortDsc_action', array( new AITGEN_ShortDesc_Handle(), 'aitgen_update_shortDesc_callback') );
        }
    }

    $AITGEN_GENERATOR_CORE = new AITGEN_GENERATOR_CORE();
}


// GPT API Handling
global $wpdb;

// Dynamic API key
$table_name = "{$wpdb->prefix}aitgen_api_credentials";
// Check if the table exists
$table_exists = $wpdb->get_var($wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );

$api_key = '';

if ($table_exists) {
    $apiKeyQry = $wpdb->get_row(
        $wpdb->prepare("SELECT apiKey FROM {$wpdb->prefix}aitgen_api_credentials WHERE status = %d", 1),
        ARRAY_A
    );
    if ($apiKeyQry) {
        $api_key = isset($apiKeyQry['apiKey']) ? sanitize_text_field($apiKeyQry['apiKey']) : null;
    }
}


// Use api client
use OpenAI\Client;
$client = OpenAI::client($api_key);

function aitgen_generateProductTitle( $title = null, $keywords = null ) {
    global $client;
    // Construct the prompt based on the provided parameters
    $prompt = 'Generate 5 professional and SEO friendly product title';

    if (!$title ) {
        return null;
    }
    if ($title) {
        $prompt .= " based on {$title}";
    }

    // Make the API call
    $result = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt],
        ],
        'max_tokens' => 4000,
        'temperature' => 0.7
    ]);

    // Extract and return the generated product title from the API response
    return $result['choices'][0]['message']['content'];

}


function aitgen_generateShortDescription( $description = null, $keywords = null ) {
    global $client;
    // Construct the prompt based on the provided parameters
    $prompt = 'Generate professional and SEO friendly short description';

    if (!$description ) {
        return null;
    }

    if ($description) {
        $prompt .= " based on {$description}";
    }

    // Make the API call
    $result = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt],
        ],
        'max_tokens' => 4000,
        'temperature' => 0.7
    ]);

    // Extract and return the generated product title from the API response
    return $result['choices'][0]['message']['content'];
}



// It's initial release version.



/**
 * Example of how you can add your own custom media
 * button in WordPress editor
 */

 function aitgen_short_description_button() {
    global $post;
    printf(
        '<a href="#" class="button aitgen_shortDescription" data-id="%s">' . 
        '<span class="wp-media-buttons-icon dashicons dashicons-image-rotate"></span> %s' . 
        '</a>',
        esc_attr( $post->ID ), 
        __( 'Generate Short Description by AI', 'textdomain' ) 
    );
}
add_action( 'media_buttons', 'aitgen_short_description_button' );





