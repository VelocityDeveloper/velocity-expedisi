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
        add_action('wp_ajax_tarifimport', [$this, 'ajax_tarif_import']);
        add_action('wp_ajax_tarifexport', [$this, 'ajax_tarif_export']);
        add_action('wp_ajax_residelete', [$this, 'ajax_resi_delete']);
        add_action('wp_ajax_resiimport', [$this, 'ajax_resi_import']);
        add_action('wp_ajax_resiexport', [$this, 'ajax_resi_export']);
    }

    public function ajax_tarif_export()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "tarif";
        $jenis = isset($_GET['jenis']) ? sanitize_text_field($_GET['jenis']) : 'nasional';
        $results = $wpdb->get_results($wpdb->prepare("SELECT asal, tujuan, biaya, biaya_volumetrik, `min` FROM $table_name WHERE jenis = %s", $jenis), ARRAY_A);

        $filename = "tarif-" . $jenis . "-" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['asal', 'tujuan', 'biaya', 'biaya_volumetrik', 'min']);

        if ($results) {
            foreach ($results as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit;
    }

    public function ajax_resi_export()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "resi";
        $jenis = isset($_GET['jenis']) ? sanitize_text_field($_GET['jenis']) : 'nasional';
        $results = $wpdb->get_results($wpdb->prepare("SELECT no_resi, nama_pengirim, hp_pengirim, kota_pengirim, negara_pengirim, nama_penerima, hp_penerima, kota_penerima, negara_penerima, nama_barang, jenis_barang, jumlah_barang, berat_barang, berat_volumetrik, jenis_packing FROM $table_name WHERE jenis = %s", $jenis), ARRAY_A);

        $filename = "resi-" . $jenis . "-" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['no_resi', 'nama_pengirim', 'hp_pengirim', 'kota_pengirim', 'negara_pengirim', 'nama_penerima', 'hp_penerima', 'kota_penerima', 'negara_penerima', 'nama_barang', 'jenis_barang', 'jumlah_barang', 'berat_barang', 'berat_volumetrik', 'jenis_packing']);

        if ($results) {
            foreach ($results as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit;
    }

    public function ajax_tarif_import()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $file_id = isset($_POST['import_file_id']) ? intval($_POST['import_file_id']) : 0;
        $file = get_attached_file($file_id);

        if (!$file || !file_exists($file)) {
            wp_send_json_error('File tidak ditemukan');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "tarif";
        $jenis = isset($_POST['jenis']) ? sanitize_text_field($_POST['jenis']) : 'nasional';

        if (($handle = fopen($file, "r")) !== FALSE) {
            // Pastikan baris pertama (header) selalu dilewati
            fgetcsv($handle, 1000, ",");

            $count = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 3) continue;
                $this->process_tarif_row($table_name, $jenis, $data);
                $count++;
            }
            fclose($handle);
            wp_send_json_success(['message' => "$count data tarif berhasil diimpor"]);
        }

        wp_send_json_error('Gagal membaca file');
    }

    private function process_tarif_row($table_name, $jenis, $data)
    {
        global $wpdb;
        $asal   = sanitize_text_field($data[0]);
        $tujuan = sanitize_text_field($data[1]);

        if (empty($asal) || empty($tujuan)) return;

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE asal = %s AND tujuan = %s AND jenis = %s",
            $asal,
            $tujuan,
            $jenis
        ));

        $insert_data = [
            'asal'              => $asal,
            'tujuan'            => $tujuan,
            'biaya'             => sanitize_text_field($data[2]),
            'biaya_volumetrik'  => isset($data[3]) ? sanitize_text_field($data[3]) : '0',
            'min'               => isset($data[4]) ? sanitize_text_field($data[4]) : '1',
            'jenis'             => $jenis,
        ];

        if ($existing) {
            $wpdb->update($table_name, $insert_data, ['id' => $existing]);
        } else {
            $wpdb->insert($table_name, $insert_data);
        }
    }

    public function ajax_resi_import()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $file_id = isset($_POST['import_file_id']) ? intval($_POST['import_file_id']) : 0;
        $file = get_attached_file($file_id);

        if (!$file || !file_exists($file)) {
            wp_send_json_error('File tidak ditemukan');
        }

        global $wpdb;
        $table_resi = $wpdb->prefix . "resi";
        $jenis = isset($_POST['jenis']) ? sanitize_text_field($_POST['jenis']) : 'nasional';

        if (($handle = fopen($file, "r")) !== FALSE) {
            // Skip header if needed, but the UI says without header. 
            // Let's assume there's a header and skip it to be safe, or check first column.
            $first_row = fgetcsv($handle, 1000, ",");
            if ($first_row && $first_row[0] === 'no_resi') {
                // It's a header, skip it
            } else {
                // Not a header, process it
                if ($first_row) {
                    $this->insert_resi_data($table_resi, $jenis, $first_row);
                }
            }

            $count = ($first_row && $first_row[0] !== 'no_resi') ? 1 : 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 2) continue;
                $this->insert_resi_data($table_resi, $jenis, $data);
                $count++;
            }
            fclose($handle);
            wp_send_json_success(['message' => "$count data resi berhasil diimpor"]);
        }

        wp_send_json_error('Gagal membaca file');
    }

    private function insert_resi_data($table_name, $jenis, $data)
    {
        global $wpdb;
        $no_resi = sanitize_text_field($data[0]);
        if (empty($no_resi)) return;

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE no_resi = %s", $no_resi));

        $insert_data = [
            'no_resi'           => $no_resi,
            'nama_pengirim'     => isset($data[1]) ? sanitize_text_field($data[1]) : '',
            'hp_pengirim'       => isset($data[2]) ? sanitize_text_field($data[2]) : '',
            'kota_pengirim'     => isset($data[3]) ? sanitize_text_field($data[3]) : '',
            'negara_pengirim'   => isset($data[4]) ? sanitize_text_field($data[4]) : '',
            'nama_penerima'     => isset($data[5]) ? sanitize_text_field($data[5]) : '',
            'hp_penerima'       => isset($data[6]) ? sanitize_text_field($data[6]) : '',
            'kota_penerima'     => isset($data[7]) ? sanitize_text_field($data[7]) : '',
            'negara_penerima'   => isset($data[8]) ? sanitize_text_field($data[8]) : '',
            'nama_barang'       => isset($data[9]) ? sanitize_text_field($data[9]) : '',
            'jenis_barang'      => isset($data[10]) ? sanitize_text_field($data[10]) : '',
            'jumlah_barang'     => isset($data[11]) ? sanitize_text_field($data[11]) : '',
            'berat_barang'      => isset($data[12]) ? sanitize_text_field($data[12]) : '',
            'berat_volumetrik'  => isset($data[13]) ? sanitize_text_field($data[13]) : '',
            'jenis_packing'     => isset($data[14]) ? sanitize_text_field($data[14]) : '',
            'jenis'             => $jenis,
        ];

        if ($existing) {
            $wpdb->update($table_name, $insert_data, ['id' => $existing]);
        } else {
            $wpdb->insert($table_name, $insert_data);
        }
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
        // add_submenu_page(
        //     'velocity-expedisi',
        //     'Daftar Tarif',
        //     'Daftar Tarif',
        //     'manage_options',
        //     'velocity-expedisi-tarif',
        //     [$this, 'render_tarif_page']
        // );

        add_submenu_page(
            'velocity-expedisi',
            'Resi',
            'Resi',
            'manage_options',
            'velocity-expedisi-resi',
            [$this, 'render_resi_page']
        );

        add_submenu_page(
            'velocity-expedisi',
            'Setting Expedisi',
            'Setting Expedisi',
            'manage_options',
            'velocity-expedisi-tarif-settings',
            [$this, 'render_tarif_settings_page']
        );
    }

    public function enqueue_scripts($hook = '')
    {
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if (!in_array($page, ['velocity-expedisi', 'velocity-expedisi-tarif', 'velocity-expedisi-tarif-settings', 'velocity-expedisi-resi'], true)) {
            return;
        }

        wp_enqueue_media();

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
            update_option('velocity_expedisi_pdf_size', sanitize_text_field($_POST['velocity_expedisi_pdf_size']));
            update_option('velocity_expedisi_volumetrik_enable', isset($_POST['velocity_expedisi_volumetrik_enable']) ? '1' : '0');
            update_option('velocity_expedisi_volumetrik_divisor', sanitize_text_field($_POST['velocity_expedisi_volumetrik_divisor']));
            echo '<div class="notice notice-success is-dismissible"><p>Pengaturan disimpan.</p></div>';
        }

        $type = get_option('velocity_expedisi_type', 'nasional');
        $pdf_size = get_option('velocity_expedisi_pdf_size', 'A4');
        $volumetrik_enable = get_option('velocity_expedisi_volumetrik_enable', '0');
        $volumetrik_divisor = get_option('velocity_expedisi_volumetrik_divisor', '4000');
        ?>
        <div class="wrap">
            <h1>Setting Expedisi</h1>
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
                    <tr>
                        <th scope="row">Ukuran Kertas PDF Resi</th>
                        <td>
                            <select name="velocity_expedisi_pdf_size">
                                <option value="A4" <?php selected($pdf_size, 'A4'); ?>>A4 (Standard)</option>
                                <option value="F4" <?php selected($pdf_size, 'F4'); ?>>F4 (Legal)</option>
                                <option value="thermal" <?php selected($pdf_size, 'thermal'); ?>>Thermal (80mm)</option>
                                <option value="thermal-58" <?php selected($pdf_size, 'thermal-58'); ?>>Thermal (58mm)</option>
                            </select>
                            <p class="description">Pilih ukuran kertas yang sesuai dengan printer Anda.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Perhitungan Volumetrik</th>
                        <td>
                            <label>
                                <input type="checkbox" name="velocity_expedisi_volumetrik_enable" value="1" <?php checked($volumetrik_enable, '1'); ?>>
                                Aktifkan perhitungan volumetrik di frontend
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Pembagi Volumetrik</th>
                        <td>
                            <input type="number" name="velocity_expedisi_volumetrik_divisor" value="<?php echo esc_attr($volumetrik_divisor); ?>" class="small-text">
                            <p class="description">Default: 4000 atau 6000 (Panjang x Lebar x Tinggi / Pembagi).</p>
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
