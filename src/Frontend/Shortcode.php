<?php
namespace Expedisi\Frontend;
class Shortcode
{
    public function register()
    {
        add_shortcode('cek_tarif', [$this, 'cek_tarif']);
        add_shortcode('cek_resi', [$this, 'cek_resi']);

        add_action('wp_ajax_cek_tarif', [$this, 'ajax_cek_tarif']);
        add_action('wp_ajax_nopriv_cek_tarif', [$this, 'ajax_cek_tarif']);

        add_action('wp_ajax_cek_resi', [$this, 'ajax_cek_resi']);
        add_action('wp_ajax_nopriv_cek_resi', [$this, 'ajax_cek_resi']);
    }

    public function ajax_cek_tarif()
    {
        global $wpdb;
        $asal   = isset($_POST['asal']) ? sanitize_text_field($_POST['asal']) : '';
        $tujuan = isset($_POST['tujuan']) ? sanitize_text_field($_POST['tujuan']) : '';
        $type   = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'nasional';

        if (!$asal || !$tujuan) {
            wp_send_json_error('Data tidak lengkap');
        }

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}tarif WHERE asal = %s AND tujuan = %s AND jenis = %s",
            $asal,
            $tujuan,
            $type
        ));

        wp_send_json_success($results);
    }

    public function ajax_cek_resi()
    {
        global $wpdb;
        $no_resi = isset($_POST['no_resi']) ? sanitize_text_field($_POST['no_resi']) : '';

        if (!$no_resi) {
            wp_send_json_error('Nomor resi tidak boleh kosong');
        }

        $resi = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}resi WHERE no_resi = %s",
            $no_resi
        ));

        if (!$resi) {
            wp_send_json_error('Resi tidak ditemukan');
        }

        $tracking = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}resi_tracking WHERE resi_id = %d ORDER BY waktu DESC",
            $resi->id
        ));

        wp_send_json_success([
            'resi' => $resi,
            'tracking' => $tracking
        ]);
    }

    public function cek_resi($atts)
    {
        global $wpdb;

        // Enqueue Bootstrap for styling if not already present
        if (!wp_style_is('bootstrap-5', 'enqueued')) {
            wp_enqueue_style(
                'bootstrap-5',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                [],
                '5.3.3'
            );
        }

        $no_resi = isset($_GET['no_resi']) ? sanitize_text_field($_GET['no_resi']) : '';
        $resi = null;
        $tracking = [];

        if ($no_resi) {
            $resi = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}resi WHERE no_resi = %s",
                $no_resi
            ));

            if ($resi) {
                $tracking = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}resi_tracking WHERE resi_id = %d ORDER BY waktu DESC",
                    $resi->id
                ));
            }
        }

        ob_start();
        include VELOCITY_EXPEDISI_DIR_PATH . 'src/Frontend/views/track-resi.php';
        return ob_get_clean();
    }

    public function cek_tarif($atts)
    {
        global $wpdb;

        $atts = shortcode_atts([
            'type' => get_option('velocity_expedisi_type', 'nasional'),
        ], $atts);

        $type = $atts['type'];

        // Enqueue Bootstrap for styling if not already present
        if (!wp_style_is('bootstrap-5', 'enqueued')) {
            wp_enqueue_style(
                'bootstrap-5',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                [],
                '5.3.3'
            );
        }

        $asal   = isset($_GET['asal']) ? sanitize_text_field($_GET['asal']) : '';
        $tujuan = isset($_GET['tujuan']) ? sanitize_text_field($_GET['tujuan']) : '';
        $berat  = isset($_GET['berat']) ? (float)$_GET['berat'] : 0;
        
        $tarif_result = [];

        if ($asal && $tujuan) {
            $tarif_result = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}tarif WHERE asal = %s AND tujuan = %s AND jenis = %s",
                $asal,
                $tujuan,
                $type
            ));
        }

        ob_start();
        include VELOCITY_EXPEDISI_DIR_PATH . 'src/Frontend/views/cek-tarif.php';
        return ob_get_clean();
    }

}
