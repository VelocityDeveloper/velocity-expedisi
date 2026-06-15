<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = isset($_GET['action']) && $_GET['action'] === 'edit';
$resi_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$resi_data = null;
$tracking_list = [];

if ($is_edit && $resi_id > 0) {
    $resi_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}resi WHERE id = %d", $resi_id));
    $tracking_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}resi_tracking WHERE resi_id = %d ORDER BY waktu DESC, id DESC", $resi_id));
}

// Load cities and countries
$cities_json = file_get_contents(VELOCITY_EXPEDISI_DIR_PATH . 'src/Core/city.json');
$countries_json = file_get_contents(VELOCITY_EXPEDISI_DIR_PATH . 'src/Core/countries.json');
$cities = json_decode($cities_json);
$countries = json_decode($countries_json);

$city_options = [];
if ($cities) {
    foreach ($cities as $city) {
        $name = $city->city_name . ($city->type === 'Kota' ? ' Kota' : '');
        $city_options[] = $name;
    }
    sort($city_options);
}

$country_options = [];
if ($countries) {
    foreach ($countries as $country) {
        $country_options[] = $country->country;
    }
    sort($country_options);
}

// Set default values for new resi
if (!$resi_data) {
    $resi_data = (object) [
        'no_resi' => 'RESI-' . date('Ymd') . '-' . strtoupper(wp_generate_password(4, false)),
        'jenis' => $type,
        'nama_pengirim' => '',
        'hp_pengirim' => '',
        'kota_pengirim' => '',
        'negara_pengirim' => '',
        'nama_penerima' => '',
        'hp_penerima' => '',
        'kota_penerima' => '',
        'negara_penerima' => '',
        'nama_barang' => '',
        'jenis_barang' => '',
        'jumlah_barang' => '',
        'berat_barang' => '',
        'berat_volumetrik' => '',
        'jenis_packing' => '',
    ];
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo $is_edit ? 'Edit Resi' : 'Tambah Resi Baru'; ?></h1>
    <a href="<?php echo admin_url('admin.php?page=velocity-expedisi-resi'); ?>" class="page-title-action">Kembali ke Daftar</a>
    <hr class="wp-header-end">

    <form method="post" action="">
        <?php wp_nonce_field('save_resi_action', 'resi_nonce'); ?>
        <input type="hidden" name="resi_id" value="<?php echo $resi_id; ?>">
        <input type="hidden" name="action_type" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">

        <div class="row mt-4">
            <!-- Data Resi -->
            <div class="col-12">
                <div class="card-resi shadow-sm mb-4 border-0">
                    <div class="resi-header bg-primary text-white py-3">
                        <h5 class="mb-0 d-flex align-items-center">
                            <span class="dashicons dashicons-text-page me-2"></span>
                            Informasi Resi & Pengiriman
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small uppercase">No Resi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><span class="dashicons dashicons-clipboard"></span></span>
                                    <input type="text" name="no_resi" class="form-control bg-light fw-bold" value="<?php echo esc_attr($resi_data->no_resi); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small uppercase">Jenis Pengiriman</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><span class="dashicons dashicons-admin-site"></span></span>
                                    <select name="jenis" class="form-select" onchange="toggleLocationInputs(this.value)">
                                        <option value="nasional" <?php selected($resi_data->jenis, 'nasional'); ?>>Nasional</option>
                                        <option value="internasional" <?php selected($resi_data->jenis, 'internasional'); ?>>Internasional</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row g-4">
                                    <!-- Pengirim -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 bg-light border-start border-primary border-4">
                                            <h6 class="text-primary mb-3 d-flex align-items-center fw-bold">
                                                <span class="dashicons dashicons-upload me-2"></span>
                                                Data Pengirim
                                            </h6>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Pengirim</label>
                                                <input type="text" name="nama_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->nama_pengirim); ?>" required placeholder="Nama lengkap pengirim">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">No HP Pengirim</label>
                                                <input type="text" name="hp_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->hp_pengirim); ?>" required placeholder="Contoh: 0812xxxx">
                                            </div>
                                            <div class="mb-0 location-input nasional-input" <?php echo $resi_data->jenis === 'internasional' ? 'style="display:none;"' : ''; ?>>
                                                <label class="form-label small fw-bold">Kota Pengirim</label>
                                                <select name="kota_pengirim" class="form-select select2-location">
                                                    <option value="">Pilih Kota Asal</option>
                                                    <?php foreach ($city_options as $option) : ?>
                                                        <option value="<?php echo esc_attr($option); ?>" <?php selected($resi_data->kota_pengirim, $option); ?>><?php echo esc_html($option); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-0 location-input internasional-input" <?php echo $resi_data->jenis === 'nasional' ? 'style="display:none;"' : ''; ?>>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Negara Pengirim</label>
                                                    <select name="negara_pengirim" class="form-select select2-location">
                                                        <option value="">Pilih Negara Asal</option>
                                                        <?php foreach ($country_options as $option) : ?>
                                                            <option value="<?php echo esc_attr($option); ?>" <?php selected($resi_data->negara_pengirim, $option); ?>><?php echo esc_html($option); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Kota Pengirim</label>
                                                    <input type="text" name="kota_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->kota_pengirim); ?>" placeholder="Ketik nama kota">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Penerima -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 bg-light border-start border-success border-4 h-100">
                                            <h6 class="text-success mb-3 d-flex align-items-center fw-bold">
                                                <span class="dashicons dashicons-download me-2"></span>
                                                Data Penerima
                                            </h6>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Penerima</label>
                                                <input type="text" name="nama_penerima" class="form-control" value="<?php echo esc_attr($resi_data->nama_penerima); ?>" required placeholder="Nama lengkap penerima">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">No HP Penerima</label>
                                                <input type="text" name="hp_penerima" class="form-control" value="<?php echo esc_attr($resi_data->hp_penerima); ?>" required placeholder="Contoh: 0812xxxx">
                                            </div>
                                            <div class="mb-0 location-input nasional-input" <?php echo $resi_data->jenis === 'internasional' ? 'style="display:none;"' : ''; ?>>
                                                <label class="form-label small fw-bold">Kota Penerima</label>
                                                <select name="kota_penerima" class="form-select select2-location">
                                                    <option value="">Pilih Kota Tujuan</option>
                                                    <?php foreach ($city_options as $option) : ?>
                                                        <option value="<?php echo esc_attr($option); ?>" <?php selected($resi_data->kota_penerima, $option); ?>><?php echo esc_html($option); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-0 location-input internasional-input" <?php echo $resi_data->jenis === 'nasional' ? 'style="display:none;"' : ''; ?>>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Kota Penerima</label>
                                                    <input type="text" name="kota_penerima" class="form-control" value="<?php echo esc_attr($resi_data->kota_penerima); ?>" placeholder="Ketik nama kota">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Negara Penerima</label>
                                                    <select name="negara_penerima" class="form-select select2-location">
                                                        <option value="">Pilih Negara Tujuan</option>
                                                        <?php foreach ($country_options as $option) : ?>
                                                            <option value="<?php echo esc_attr($option); ?>" <?php selected($resi_data->negara_penerima, $option); ?>><?php echo esc_html($option); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Barang -->
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 border border-dashed bg-white">
                                    <h6 class="text-dark mb-4 d-flex align-items-center fw-bold border-bottom pb-2">
                                        <span class="dashicons dashicons-archive me-2"></span>
                                        Detail Barang
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Nama Barang</label>
                                            <input type="text" name="nama_barang" class="form-control" value="<?php echo esc_attr($resi_data->nama_barang); ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Jenis Barang</label>
                                            <input type="text" name="jenis_barang" class="form-control" value="<?php echo esc_attr($resi_data->jenis_barang); ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Jumlah Barang</label>
                                            <input type="text" name="jumlah_barang" class="form-control" value="<?php echo esc_attr($resi_data->jumlah_barang); ?>" required placeholder="Contoh: 2 koli">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Berat Barang (kg)</label>
                                            <div class="input-group">
                                                <input type="text" name="berat_barang" class="form-control" value="<?php echo esc_attr($resi_data->berat_barang); ?>" required>
                                                <span class="input-group-text bg-light">kg</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Berat Volumetrik (kg)</label>
                                            <div class="input-group">
                                                <input type="text" name="berat_volumetrik" class="form-control" value="<?php echo esc_attr($resi_data->berat_volumetrik); ?>">
                                                <span class="input-group-text bg-light">kg</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Jenis Packing</label>
                                            <input type="text" name="jenis_packing" class="form-control" value="<?php echo esc_attr($resi_data->jenis_packing); ?>" required placeholder="Contoh: Kayu / Plastik">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-end py-3 px-4 d-flex justify-content-end">
                        <button type="submit" name="save_resi" class="btn btn-primary btn-lg px-5 shadow-sm d-flex align-items-center">
                            <span class="dashicons dashicons-saved me-2"></span> Simpan Data Resi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tracking History (Hanya saat Edit) -->
            <?php if ($is_edit) : ?>
                <div class="col-12">
                    <div class="card-resi shadow-sm mb-4 border-0">
                        <div class="resi-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 d-flex align-items-center">
                                <span class="dashicons dashicons-location-alt me-2"></span>
                                Tracking Status
                            </h5>
                            <button type="button" class="btn btn-sm btn-success px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTracking">
                                <span class="dashicons dashicons-plus"></span> Update Status
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Waktu</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Kurir</th>
                                            <th class="pe-4 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($tracking_list)) : ?>
                                            <tr>
                                                <td colspan="5" class="p-5 text-center text-muted">
                                                    <div class="mb-2"><span class="dashicons dashicons-info" style="font-size: 40px; width: 40px; height: 40px;"></span></div>
                                                    Belum ada riwayat tracking.
                                                </td>
                                            </tr>
                                        <?php else : ?>
                                            <?php foreach ($tracking_list as $track) : ?>
                                                <tr>
                                                    <td class="ps-4"><?php echo date('d/m/Y H:i', strtotime($track->waktu)); ?></td>
                                                    <td><span class="badge rounded-pill bg-primary px-3"><?php echo esc_html($track->status); ?></span></td>
                                                    <td><?php echo esc_html($track->keterangan); ?></td>
                                                    <td>
                                                        <?php if ($track->kurir) : ?>
                                                            <span class="badge rounded-pill bg-secondary px-3"><?php echo esc_html($track->kurir); ?></span>
                                                        <?php else : ?>
                                                            <span class="text-muted small">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="pe-4 text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Hapus status ini?')) window.location.href='<?php echo wp_nonce_url(admin_url('admin.php?page=velocity-expedisi-resi&action=delete_track&track_id=' . $track->id . '&id=' . $resi_id), 'delete_track_' . $track->id); ?>'">
                                                            <span class="dashicons dashicons-trash"></span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Modal Tracking -->
<?php if ($is_edit) : ?>
    <div class="modal fade" id="modalTracking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="">
                    <?php wp_nonce_field('add_track_action', 'track_nonce'); ?>
                    <input type="hidden" name="resi_id" value="<?php echo $resi_id; ?>">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title d-flex align-items-center">
                            <span class="dashicons dashicons-calendar-alt me-2 text-primary"></span>
                            Tambah Update Status
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small uppercase text-muted">Waktu</label>
                            <input type="datetime-local" name="waktu" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small uppercase text-muted">Status</label>
                            <select name="status" class="form-select" id="statusSelect" onchange="toggleKurirInput(this.value)">
                                <option value="Pending">Pending</option>
                                <option value="Diterima Kantor">Diterima Kantor</option>
                                <option value="Dalam Perjalanan">Dalam Perjalanan</option>
                                <option value="Sampai di Transit">Sampai di Transit</option>
                                <option value="Dikirim Kurir">Dikirim Kurir</option>
                                <option value="Diterima">Diterima</option>
                            </select>
                        </div>
                        <div class="mb-3" id="kurirInput" style="display:none;">
                            <label class="form-label text-danger fw-bold small uppercase">Nama Kurir</label>
                            <input type="text" name="kurir" class="form-control border-danger" placeholder="Masukkan nama kurir">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small uppercase text-muted">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" required placeholder="Contoh: Paket telah sampai di gudang transit Jakarta"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_track" class="btn btn-primary px-4 shadow-sm">
                            <span class="dashicons dashicons-saved me-1"></span> Simpan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    jQuery(document).ready(function($) {
        $('.select2-location').select2({
            width: '100%'
        });
    });

    function toggleLocationInputs(val) {
        const nasionalInputs = document.querySelectorAll('.nasional-input');
        const internasionalInputs = document.querySelectorAll('.internasional-input');

        if (val === 'nasional') {
            nasionalInputs.forEach(el => el.style.display = 'block');
            internasionalInputs.forEach(el => el.style.display = 'none');
        } else {
            nasionalInputs.forEach(el => el.style.display = 'none');
            internasionalInputs.forEach(el => el.style.display = 'block');
        }
    }

    function toggleKurirInput(val) {
        const kurirInput = document.getElementById('kurirInput');
        if (val === 'Dikirim Kurir') {
            kurirInput.style.display = 'block';
            kurirInput.querySelector('input').setAttribute('required', 'required');
        } else {
            kurirInput.style.display = 'none';
            kurirInput.querySelector('input').removeAttribute('required');
        }
    }
</script>
