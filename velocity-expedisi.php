<?php
/*
Plugin Name: Velocity Expedisi
Plugin URI: https://velocitydeveloper.com/
Description: Plugin expedisi dari Velocity Developer.
Version: 1.2.1
Author: Velocity Developer
Author URI: https://velocitydeveloper.com/
License: GPL2
*/

if (!defined('VELOCITY_EXPEDISI_PLUGIN_URL'))
    define('VELOCITY_EXPEDISI_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('VELOCITY_EXPEDISI_DIR_PATH'))
    define('VELOCITY_EXPEDISI_DIR_PATH', plugin_dir_path(__FILE__));

// Load Composer autoloader
if (file_exists(VELOCITY_EXPEDISI_DIR_PATH . 'vendor/autoload.php')) {
    require_once VELOCITY_EXPEDISI_DIR_PATH . 'vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $prefix = 'Expedisi\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize plugin
$plugin = new \Expedisi\Core\Plugin();
$plugin->run();

///register css & js
if (! function_exists('vd_enqueue_script_style')) {
    function vd_enqueue_script_style()
    {
        wp_enqueue_script('alpine-js', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], '3.x.x', true);
        wp_enqueue_script('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', 1);

        wp_enqueue_style('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', 1);
        wp_enqueue_style('slick-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', 1);
    }
    add_action('wp_enqueue_scripts', 'vd_enqueue_script_style', 20);
}

/**
 * Create database table on plugin activation
 */
function vd_create_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_tarif = $wpdb->prefix . 'tarif';
    $table_resi = $wpdb->prefix . 'resi';
    $table_resi_tracking = $wpdb->prefix . 'resi_tracking';

    $sql = "CREATE TABLE $table_tarif (
        id int unsigned NOT NULL auto_increment,
        asal varchar(255) NOT NULL,
        tujuan varchar(255) NOT NULL,
        jenis varchar(20) NOT NULL DEFAULT 'nasional',
        biaya varchar(115) NOT NULL,
        biaya_volumetrik varchar(115) NOT NULL,
        `min` varchar(115) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;
    CREATE TABLE $table_resi (
        id bigint(20) unsigned NOT NULL auto_increment,
        no_resi varchar(255) NOT NULL,
        jenis varchar(50) NOT NULL,
        nama_pengirim varchar(255) NOT NULL,
        hp_pengirim varchar(255) NOT NULL,
        kota_pengirim varchar(255) DEFAULT '',
        negara_pengirim varchar(255) DEFAULT '',
        nama_penerima varchar(255) NOT NULL,
        hp_penerima varchar(255) NOT NULL,
        kota_penerima varchar(255) DEFAULT '',
        negara_penerima varchar(255) DEFAULT '',
        nama_barang varchar(255) NOT NULL,
        jenis_barang varchar(255) NOT NULL,
        jumlah_barang varchar(255) NOT NULL,
        berat_barang varchar(255) NOT NULL,
        berat_volumetrik varchar(255) NOT NULL,
        jenis_packing varchar(255) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY  no_resi (no_resi)
    ) $charset_collate;
    CREATE TABLE $table_resi_tracking (
        id bigint(20) unsigned NOT NULL auto_increment,
        resi_id bigint(20) unsigned NOT NULL,
        waktu datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        status varchar(255) NOT NULL,
        keterangan text NOT NULL,
        kurir varchar(255) DEFAULT '',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function vd_ensure_tables()
{
    vd_create_tables();
}
add_action('admin_init', 'vd_ensure_tables');
register_activation_hook(__FILE__, 'vd_create_tables');
