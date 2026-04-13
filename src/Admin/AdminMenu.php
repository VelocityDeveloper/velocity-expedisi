<?php

namespace Expedisi\Admin;

class AdminMenu
{
    public function register()
    {
        add_action('admin_menu', [$this, 'add_main_menu'], 5);
        add_action('admin_menu', [$this, 'add_sub_menu'], 5);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_tarifdelete', [$this, 'ajax_tarif_delete']);
        add_action('wp_ajax_tarifsave', [$this, 'ajax_tarif_save']);
        add_action('wp_ajax_residelete', [$this, 'ajax_resi_delete']);
    }

    public function ajax_tarif_save()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "tarif";
        $id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $asal   = isset($_POST['asal']) ? sanitize_text_field($_POST['asal']) : '';
        $tujuan = isset($_POST['tujuan']) ? sanitize_text_field($_POST['tujuan']) : '';
        $jenis  = isset($_POST['jenis']) ? sanitize_text_field($_POST['jenis']) : 'nasional';
        $biaya  = isset($_POST['biaya']) ? sanitize_text_field($_POST['biaya']) : '';
        $biaya_volumetrik = isset($_POST['biaya_volumetrik']) ? sanitize_text_field($_POST['biaya_volumetrik']) : '';
        $min    = isset($_POST['min']) ? sanitize_text_field($_POST['min']) : '';

        if (!$asal || !$tujuan || !$biaya) {
            wp_send_json_error('Data tidak lengkap');
        }

        $data = [
            'asal'      => $asal,
            'tujuan'    => $tujuan,
            'jenis'     => $jenis,
            'biaya'     => $biaya,
            'biaya_volumetrik' => $biaya_volumetrik,
            'min'       => $min,
        ];

        if ($id > 0) {
            $wpdb->update($table_name, $data, ['id' => $id]);
            $data['id'] = $id;
            wp_send_json_success($data);
        } else {
            $wpdb->insert($table_name, $data);
            $data['id'] = $wpdb->insert_id;
            wp_send_json_success($data);
        }
    }

    public function ajax_tarif_delete()
    {
        global $wpdb;
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $wpdb->delete($wpdb->prefix . 'tarif', ['id' => $id]);
            wp_send_json_success(true);
        }
        wp_send_json_error(false);
    }

    public function ajax_resi_delete()
    {
        global $wpdb;
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $wpdb->delete($wpdb->prefix . 'resi', ['id' => $id]);
            $wpdb->delete($wpdb->prefix . 'resi_tracking', ['resi_id' => $id]);
            wp_send_json_success(true);
        }
        wp_send_json_error(false);
    }

    public function add_main_menu()
    {
        add_menu_page(
            'Velocity Expedisi',
            'Velocity Expedisi',
            'manage_options',
            'velocity-expedisi',
            [$this, 'render_tarif_page'],
            'dashicons-admin-generic',
            30
        );
    }

    public function add_sub_menu()
    {
        add_submenu_page(
            'velocity-expedisi',
            'Daftar Tarif',
            'Daftar Tarif',
            'manage_options',
            'velocity-expedisi-tarif',
            [$this, 'render_tarif_page']
        );

        add_submenu_page(
            'velocity-expedisi',
            'Setting Tarif',
            'Setting Tarif',
            'manage_options',
            'velocity-expedisi-tarif-settings',
            [$this, 'render_tarif_settings_page']
        );

        add_submenu_page(
            'velocity-expedisi',
            'Resi',
            'Resi',
            'manage_options',
            'velocity-expedisi-resi',
            [$this, 'render_resi_page']
        );
    }

    public function enqueue_scripts($hook = '')
    {
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if (!in_array($page, ['velocity-expedisi', 'velocity-expedisi-tarif', 'velocity-expedisi-tarif-settings', 'velocity-expedisi-resi'], true)) {
            return;
        }

        wp_enqueue_style(
            'velocity-expedisi-admin',
            VELOCITY_EXPEDISI_PLUGIN_URL . 'assets/admin/admin.css',
            [],
            '1.2.1'
        );

        if (!wp_style_is('bootstrap-5', 'enqueued')) {
            wp_enqueue_style(
                'bootstrap-5',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                [],
                '5.3.3'
            );
        }
        if (!wp_script_is('bootstrap-5-bundle', 'enqueued')) {
            wp_enqueue_script(
                'bootstrap-5-bundle',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
                [],
                '5.3.3',
                true
            );
        }

        wp_enqueue_script(
            'alpine-js',
            'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
            [],
            '3.x.x',
            true
        );
    }

    public function render_main_menu()
    {
        echo '<div class="wrap"><h1>Velocity Expedisi</h1></div>';
    }

    public function render_tarif_page()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "tarif";
        $type = isset($_GET['jenis']) ? sanitize_text_field($_GET['jenis']) : get_option('velocity_expedisi_type', 'nasional');
        if (!in_array($type, ['nasional', 'internasional'])) {
            $type = 'nasional';
        }

        ///ambil data
        $details = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE jenis = %s ORDER BY id DESC", $type));
        $view = plugin_dir_path(__FILE__) . 'views/tarif-page.php';
        if (file_exists($view)) {
            include $view;
        }
    }

    public function render_tarif_settings_page()
    {
        if (isset($_POST['velocity_expedisi_type'])) {
            update_option('velocity_expedisi_type', sanitize_text_field($_POST['velocity_expedisi_type']));
            echo '<div class="notice notice-success is-dismissible"><p>Pengaturan disimpan.</p></div>';
        }

        $type = get_option('velocity_expedisi_type', 'nasional');
        ?>
        <div class="wrap">
            <h1>Setting Tarif</h1>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row">Tipe Expedisi</th>
                        <td>
                            <select name="velocity_expedisi_type">
                                <option value="nasional" <?php selected($type, 'nasional'); ?>>Nasional (Kota)</option>
                                <option value="internasional" <?php selected($type, 'internasional'); ?>>Internasional (Negara)</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_resi_page()
    {
        global $wpdb;
        $table_resi = $wpdb->prefix . 'resi';
        $table_tracking = $wpdb->prefix . 'resi_tracking';

        $type = isset($_GET['jenis']) ? sanitize_text_field($_GET['jenis']) : get_option('velocity_expedisi_type', 'nasional');
        if (!in_array($type, ['nasional', 'internasional'])) {
            $type = 'nasional';
        }

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Handle Form Submission: Simpan/Edit Resi
        if (isset($_POST['save_resi']) && check_admin_referer('save_resi_action', 'resi_nonce')) {
            $data = [
                'no_resi'           => sanitize_text_field($_POST['no_resi']),
                'jenis'             => sanitize_text_field($_POST['jenis']),
                'nama_pengirim'     => sanitize_text_field($_POST['nama_pengirim']),
                'hp_pengirim'       => sanitize_text_field($_POST['hp_pengirim']),
                'kota_pengirim'     => sanitize_text_field($_POST['kota_pengirim']),
                'negara_pengirim'   => sanitize_text_field($_POST['negara_pengirim']),
                'nama_penerima'     => sanitize_text_field($_POST['nama_penerima']),
                'hp_penerima'       => sanitize_text_field($_POST['hp_penerima']),
                'kota_penerima'     => sanitize_text_field($_POST['kota_penerima']),
                'negara_penerima'   => sanitize_text_field($_POST['negara_penerima']),
                'nama_barang'       => sanitize_text_field($_POST['nama_barang']),
                'jenis_barang'      => sanitize_text_field($_POST['jenis_barang']),
                'jumlah_barang'     => sanitize_text_field($_POST['jumlah_barang']),
                'berat_barang'      => sanitize_text_field($_POST['berat_barang']),
                'berat_volumetrik'  => sanitize_text_field($_POST['berat_volumetrik']),
                'jenis_packing'     => sanitize_text_field($_POST['jenis_packing']),
            ];

            if ($_POST['action_type'] === 'add') {
                $wpdb->insert($table_resi, $data);
                $new_id = $wpdb->insert_id;
                echo '<div class="notice notice-success is-dismissible"><p>Resi berhasil ditambahkan.</p></div>';
                wp_redirect(admin_url('admin.php?page=velocity-expedisi-resi&action=edit&id=' . $new_id . '&jenis=' . $data['jenis']));
                exit;
            } else {
                $wpdb->update($table_resi, $data, ['id' => $id]);
                echo '<div class="notice notice-success is-dismissible"><p>Resi berhasil diperbarui.</p></div>';
            }
        }

        // Handle Form Submission: Tambah Track
        if (isset($_POST['add_track']) && check_admin_referer('add_track_action', 'track_nonce')) {
            $track_data = [
                'resi_id'    => intval($_POST['resi_id']),
                'waktu'      => sanitize_text_field($_POST['waktu']),
                'status'     => sanitize_text_field($_POST['status']),
                'keterangan' => sanitize_textarea_field($_POST['keterangan']),
                'kurir'      => sanitize_text_field($_POST['kurir']),
            ];
            $wpdb->insert($table_tracking, $track_data);
            echo '<div class="notice notice-success is-dismissible"><p>Status tracking berhasil ditambahkan.</p></div>';
        }

        // Handle Deletion: Resi
        if ($action === 'delete' && $id > 0 && check_admin_referer('delete_resi_' . $id)) {
            $wpdb->delete($table_resi, ['id' => $id]);
            $wpdb->delete($table_tracking, ['resi_id' => $id]);
            echo '<div class="notice notice-success is-dismissible"><p>Resi berhasil dihapus.</p></div>';
            wp_redirect(admin_url('admin.php?page=velocity-expedisi-resi&jenis=' . $type));
            exit;
        }

        // Handle Deletion: Track
        if ($action === 'delete_track' && isset($_GET['track_id']) && check_admin_referer('delete_track_' . $_GET['track_id'])) {
            $wpdb->delete($table_tracking, ['id' => intval($_GET['track_id'])]);
            echo '<div class="notice notice-success is-dismissible"><p>Status tracking berhasil dihapus.</p></div>';
        }

        // Routing View
        if ($action === 'add' || $action === 'edit') {
            $view = plugin_dir_path(__FILE__) . 'views/resi-form.php';
        } else {
            $resi_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_resi WHERE jenis = %s ORDER BY created_at DESC", $type));
            $view = plugin_dir_path(__FILE__) . 'views/resi-page.php';
        }

        if (file_exists($view)) {
            include $view;
        }
    }
}
