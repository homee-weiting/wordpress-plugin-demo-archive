<?php
/**
 * Plugin Name: test-woo-extension
 * Author: weiting
 * Developer: weiting
 * Developer URI: Some URI
 * Plugin URI: Plugin URI
 * @package WooCommerce\test
 */


defined( 'ABSPATH' ) || exit;


global $wpdb;
// print_r($wpdb->tables());

/**
 * Activation and deactivation hooks for WordPress
 */
function myPrefix_extension_activate() {
    // Your activation logic goes here.
}
register_activation_hook( __FILE__, 'myPrefix_extension_activate' );
 
function myPrefix_extension_deactivate() {
    // Your deactivation logic goes here.
 
    // Don't forget to:
    // Remove Scheduled Actions
    // Remove Notes in the Admin Inbox
    // Remove Admin Tasks
}
register_deactivation_hook( __FILE__, 'myPrefix_extension_deactivate' );

if ( ! class_exists( 'My_Extension' ) ) :
    /**
     * My Extension core class
     */
    class My_Extension {
 
        /**
         * The single instance of the class.
         */
        protected static $_instance = null;
 
        /**
         * Constructor.
         */
        protected function __construct() {
            $this->includes();
            $this->init();
        }
 
        /**
         * Main Extension Instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
 
        /**
         * Cloning is forbidden.
         */
        public function __clone() {
            // Override this PHP function to prevent unwanted copies of your instance.
            //   Implement your own error or use `wc_doing_it_wrong()`
        }
 
        /**
         * Unserializing instances of this class is forbidden.
         */
        public function __wakeup() {
            // Override this PHP function to prevent unwanted copies of your instance.
            //   Implement your own error or use `wc_doing_it_wrong()`
        }
 
        /**
        * Function for loading dependencies.
        */
        private function includes() {
            // $loader = include_once dirname( __FILE__ ) . '/' . 'vendor/autoload.php';
 
            // if ( ! $loader ) {
            //     throw new Exception( 'vendor/autoload.php missing please run `composer install`' );
            // }
 
            // require_once dirname( __FILE__ ) . '/' . 'includes/my-extension-functions.php';
        }
 
        /**
         * Function for getting everything set up and ready to run.
         */
        private function init() {
 
            // Examples include: 
 
            // Set up cache management.
            // new My_Extension_Cache();
 
            // Initialize REST API.
            // new My_Extension_REST_API();
 
            // Set up email management.
            // new My_Extension_Email_Manager();
 
            // Register with some-action hook
            // add_action('some-action', 'my-extension-function');
        }
    }
endif;

function my_extension_initialize() {
	
    // This is also a great place to check for the existence of the WooCommerce class
    if ( ! class_exists( 'WooCommerce' ) ) {
    // You can handle this situation in a variety of ways,
    //   but adding a WordPress admin notice is often a good tactic.
        return;
    }
 
    $GLOBALS['my_extension'] = My_Extension::instance();
}
add_action( 'plugins_loaded', 'my_extension_initialize', 10 );

/**
 * Register the JS.
 */
function add_extension_register_script() {
	
	if ( ! class_exists( 'Automattic\WooCommerce\Admin\PageController' ) || ! \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page() ) {
		return;
	}

	$script_path       = '/build/index.js';
	$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
	$script_asset      = file_exists( $script_asset_path )
		? require( $script_asset_path )
		: array( 'dependencies' => array(), 'version' => filemtime( $script_path ) );
	$script_url = plugins_url( $script_path, __FILE__ );

	wp_register_script(
		'test-woo-extension',
		$script_url,
		$script_asset['dependencies'],
		$script_asset['version'],
		true
	);

	// wp_register_style(
	// 	'test-woo-extension',
	// 	plugins_url( '/build/index.css', __FILE__ ),
	// 	// Add any dependencies styles may have, such as wp-components.
	// 	array(),
	// 	filemtime( dirname( __FILE__ ) . '/build/index.css' )
	// );

	wp_enqueue_script( 'test-woo-extension' );
	wp_enqueue_style( 'test-woo-extension' );
}

add_action( 'admin_enqueue_scripts', 'add_extension_register_script' );

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\Onboarding;


function register_navigation_items() {
	
	Features::enable('navigation');
	if (
		! method_exists( Screen::class, 'register_post_type' ) ||
		! method_exists( Menu::class, 'add_plugin_item' ) ||
		! method_exists( Menu::class, 'add_plugin_category' ) ||
		! Features::is_enabled( 'navigation' )
	) {
		return;
	}
	
    // Register a standalone menu item. 
    Menu::add_plugin_item(
        array(
            'id'         => 'my-extension',
            'title'      => 'My Extension',
            'capability' => 'manage_woocommerce',
            'url'        => 'wc-my-extension'
        )
    );
 
    // Register a navigation category with a child item.
    Menu::add_plugin_category(
        array(
            'id'     => 'my-extension-category',
            'title'  => 'My Extension Category',
            'parent' => 'woocommerce',
        )
    );
 
    Menu::add_plugin_item(
        array(
            'id'         => 'my-extension-cat-page',
            'title'      => 'My Extension Cat Page',
            'capability' => 'manage_woocommerce',
            'url'        => 'my-extension-slug-cat-page',
            'parent'     => 'my-extension-category',
        )
    );
}
add_action( 'admin_menu', 'register_navigation_items' );


function add_extension_register_page() {
    if ( ! function_exists( 'wc_admin_register_page' ) ) {
        return;
    }
 
    wc_admin_register_page( array(
        'id'       => 'my-example-page',
        'title'    => 'My Example Page',
        'parent'   => 'woocommerce',
        'path'     => '/example',
        'nav_args' => array(
            'order'  => 10,
            'parent' => 'woocommerce',
        ),
    ) );
}
add_action( 'admin_menu', 'add_extension_register_page' );


// function custom_store_management_link() {
//     wp_enqueue_script(
//         'add-my-custom-link',
//         plugins_url( '/dist/add-my-custom-link.js', __FILE__ ),
//         array( 'wp-hooks' ),
//         10
//     );
// }
// add_action( 'admin_enqueue_scripts', 'custom_store_management_link' );


// function gutenberg_examples_01_register_block() {
//     register_block_type( __DIR__ );
// }
// add_action( 'init', 'gutenberg_examples_01_register_block' );

// add_action('init', 'jsforwp_register_block_assets');
 
// function jsforwp_register_block_assets() {
//     // creating a variable for our js file path
//     $block_path = 'src/index.js';
//     // registering the editor script that contains our blocks

// 	$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
// 	$script_asset      = file_exists( $script_asset_path )
// 		? require( $script_asset_path )
// 		: array( 'dependencies' => array(), 'version' => filemtime( $script_path ) );
// 	// $script_url = plugins_url( $script_path, __FILE__ );

// 	wp_register_script(
// 		'jsforwp-callout-block',
// 		plugins_url( $block_path , __FILE__ ),
// 		$script_asset['dependencies'],
// 		$script_asset['version'],
// 		true
// 	);

//     // wp_register_script(
//     //     'jsforwp-callout-block',
//     //     plugins_url( $block_path , __FILE__ ),
//     //     [ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
//     //     //filemtime( plugin_dir_path( $block_path , __FILE__ ) )
//     // );
	
 
//     // registering our block and passing it the hande of our editor script and our style files. 
//     register_block_type( 'jsforwp/callout-block', array(
//         'editor_script' => 'jsforwp-callout-block',
//         'style' => 'jsforwp-callout-block-styles',
//     ) );
 
// }


add_action( 'woocommerce_admin_process_product_object', 'action_save_product_meta' );
function action_save_product_meta( $product ) {
    echo $product->get_id();
	echo "product hook";
}

add_action( 'woocommerce_after_single_product', 'visual_hooks' );
function visual_hooks() {
	global $product;
	$product_id = $product->get_id();
	$user_id = get_current_user_id();
	$format = "<body><div class='trust-badges'>
	Product ID $product_id $user_id
	</div>
	<script type='text/javascript'>
		var myId = $product_id;
		console.log('run javascript');
		console.log(myId);
	</script>
	<iframe id='unity-iframe' src='https://stg-homee-service-public.s3.amazonaws.com/unity_build/index.html' title='unity' width='800' height='500'></iframe>
		</body>";
	
	$resulting_string = sprintf($format, $product_id);
	echo $resulting_string;
}


