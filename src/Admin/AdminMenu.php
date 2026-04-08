<?php

namespace Expedisi\Admin;

class AdminMenu
{
    public function register()
    {
        add_action('admin_menu', [$this, 'add_main_menu'], 5);
        add_action('admin_menu', [$this, 'add_sub_menu'], 5);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
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
        $asal   = isset($_POST['asal']) ? $_POST['asal'] : '';
        $tujuan = isset($_POST['tujuan']) ? $_POST['tujuan'] : '';
        $biaya  = isset($_POST['biaya']) ? $_POST['biaya'] : '';
        $biaya_volumetrik  = isset($_POST['biaya_volumetrik']) ? $_POST['biaya_volumetrik'] : '';
        $min    = isset($_POST['min']) ? $_POST['min'] : '';

        if ($asal && $tujuan && $biaya) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            echo '<div class="container py-3">';
            if (isset($_POST['act']) && $_POST['act'] == 'add') {
                $result_check = $wpdb->insert($table_name,
                    array(
                        'asal'      => $asal,
                        'tujuan'    => $tujuan,
                        'jenis'     => $type,
                        'biaya'     => $biaya,
                        'biaya_volumetrik' => $biaya_volumetrik,
                        'min'       => $min,
                    )
                );
                echo '<div class="alert alert-success">Data berhasil di tambah</div>';
            } else if (isset($_POST['id'])) {
                $result_check = $wpdb->update($table_name,
                    array(
                        'asal'      => $asal,
                        'tujuan'    => $tujuan,
                        'biaya'     => $biaya,
                        'biaya_volumetrik' => $biaya_volumetrik,
                        'min'       => $min,
                    ),
                    array('id'  => $_POST['id'],)
                );
                echo '<div class="alert alert-info">Data berhasil di perbarui</div>';
            }
            echo '</div>';
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
        echo '<div class="wrap"><h1>Resi</h1><p>Halaman Resi akan ditambahkan di sini.</p></div>';
    }
}
