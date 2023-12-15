<?php
/*
Plugin Name: Velocity Expedisi
Plugin URI: https://velocitydeveloper.com/
Description: Plugin expedisi dari Velocity Developer.
Version: 1.0
Author: Velocity Developer
Author URI: https://velocitydeveloper.com/
License: GPL2
*/

if (!defined('VELOCITY_EXPEDISI_PLUGIN_URL'))	
define('VELOCITY_EXPEDISI_PLUGIN_URL', plugin_dir_url(__FILE__)); 

if (!defined('VELOCITY_EXPEDISI_DIR_PATH'))	
define('VELOCITY_EXPEDISI_DIR_PATH', plugin_dir_path(__FILE__)); 

///load file
require_once(plugin_dir_path(__FILE__).'inc/tarif.php');
require_once(plugin_dir_path(__FILE__).'inc/resi.php');

///register css & js
if( ! function_exists( 'vdc_enqueue_script_style') ) {
	function vdc_enqueue_script_style() {
        wp_enqueue_script( 'slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', 1);

        wp_enqueue_style( 'slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', 1);
        wp_enqueue_style( 'slick-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', 1);
	}
	add_action( 'wp_enqueue_scripts', 'vdc_enqueue_script_style', 20 );
}

/**
 * Register admin css.
 */
function vdc_admin_scripts($hook) {
    $page = isset($_GET['page']) ? $_GET['page'] : '';    
    if ($page == 'daftar-tarif-page') {
        
        if (file_exists(get_template_directory() . '/js/theme.min.js')) {            
            $the_theme     	= wp_get_theme();
            $theme_version 	= $the_theme->get( 'Version' );
            wp_enqueue_style( 'justg-styles', get_template_directory_uri() . '/css/theme.min.css', array(), $theme_version );
            wp_enqueue_script( 'justg-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $theme_version, true );
        }

        wp_enqueue_script( array( 'jquery') );
    }
    
}
add_action( 'admin_enqueue_scripts', 'vdc_admin_scripts' );

///
function vdc_load_module() {
	if ( class_exists( 'FLBuilder' ) ) {
		require_once(plugin_dir_path(__FILE__).'modul/heroslider/heroslider.php');
		require_once(plugin_dir_path(__FILE__).'modul/carouselimage/carouselimage.php');
	}
}
add_action( 'init', 'vdc_load_module' );


//regsiter page template
add_filter( 'template_include', 'vdc_register_page_template' );
function vdc_register_page_template( $template ) {

    if ( is_singular() ) {
        $page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
        if('print-resi' === $page_template){
            $template = plugin_dir_path(__FILE__) . 'page-print.php';
        }
    }

    return $template;
}
add_filter( "theme_page_templates", 'vdc_templates_page' );
function vdc_templates_page($post_templates) {
    $post_templates['print-resi'] = __( 'Print resi', 'velocity' );
    return $post_templates;
}
// Create Page
function velocity_create_page() {
	$post_id = -1;
	$slug = 'print-resi';
	$title = 'Print Resi';
	if( null == get_page_by_title( $title ) ) {
		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	'1',
				'post_name'			=>	$slug,
				'post_title'		=>	$title,
				'post_status'		=>	'publish',
				'post_type'			=>	'page',
				'page_template'		=> 'print-resi',
			)
		);
	} else {
    	$post_id = -2;
	}
}
add_filter( 'after_setup_theme', 'velocity_create_page' );

/**
 * The code that runs during plugin activation.
 * This action is documented in inc/classes/class-velocity-toko-activator.php
 */
function activate_vdc()
{
	// Inisialisasi table
	$tarif = new Saelog_Tarif();
	$tarif->create_db();
}
register_activation_hook(__FILE__, 'activate_vdc');