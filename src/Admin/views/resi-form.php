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
                <div class="resi shadow-sm mb-4">
                    <div class="resi-header bg-primary text-white">
                        <h5 class="mb-0">Informasi Resi & Pengiriman</h5>
                    </div>
                    <div class="resi-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No Resi</label>
                                <input type="text" name="no_resi" class="form-control bg-light" value="<?php echo esc_attr($resi_data->no_resi); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Pengiriman</label>
                                <select name="jenis" class="form-select" onchange="toggleLocationInputs(this.value)">
                                    <option value="nasional" <?php selected($resi_data->jenis, 'nasional'); ?>>Nasional</option>
                                    <option value="internasional" <?php selected($resi_data->jenis, 'internasional'); ?>>Internasional</option>
                                </select>
                            </div>

                            <hr class="my-4">

                            <!-- Pengirim -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Data Pengirim</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Pengirim</label>
                                    <input type="text" name="nama_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->nama_pengirim); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No HP Pengirim</label>
                                    <input type="text" name="hp_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->hp_pengirim); ?>" required>
                                </div>
                                <div class="mb-3 location-input nasional-input" <?php echo $resi_data->jenis === 'internasional' ? 'style="display:none;"' : ''; ?>>
                                    <label class="form-label">Kota Pengirim</label>
                                    <input type="text" name="kota_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->kota_pengirim); ?>">
                                </div>
                                <div class="mb-3 location-input internasional-input" <?php echo $resi_data->jenis === 'nasional' ? 'style="display:none;"' : ''; ?>>
                                    <label class="form-label">Negara Pengirim</label>
                                    <input type="text" name="negara_pengirim" class="form-control" value="<?php echo esc_attr($resi_data->negara_pengirim); ?>">
                                </div>
                            </div>

                            <!-- Penerima -->
                            <div class="col-md-6 border-start ps-4">
                                <h6 class="text-success mb-3">Data Penerima</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Penerima</label>
                                    <input type="text" name="nama_penerima" class="form-control" value="<?php echo esc_attr($resi_data->nama_penerima); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No HP Penerima</label>
                                    <input type="text" name="hp_penerima" class="form-control" value="<?php echo esc_attr($resi_data->hp_penerima); ?>" required>
                                </div>
                                <div class="mb-3 location-input nasional-input" <?php echo $resi_data->jenis === 'internasional' ? 'style="display:none;"' : ''; ?>>
                                    <label class="form-label">Kota Penerima</label>
                                    <input type="text" name="kota_penerima" class="form-control" value="<?php echo esc_attr($resi_data->kota_penerima); ?>">
                                </div>
                                <div class="mb-3 location-input internasional-input" <?php echo $resi_data->jenis === 'nasional' ? 'style="display:none;"' : ''; ?>>
                                    <label class="form-label">Negara Penerima</label>
                                    <input type="text" name="negara_penerima" class="form-control" value="<?php echo esc_attr($resi_data->negara_penerima); ?>">
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Barang -->
                            <div class="col-12">
                                <h6 class="text-dark mb-3">Detail Barang</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" name="nama_barang" class="form-control" value="<?php echo esc_attr($resi_data->nama_barang); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jenis Barang</label>
                                        <input type="text" name="jenis_barang" class="form-control" value="<?php echo esc_attr($resi_data->jenis_barang); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jumlah Barang</label>
                                        <input type="text" name="jumlah_barang" class="form-control" value="<?php echo esc_attr($resi_data->jumlah_barang); ?>" required placeholder="Contoh: 2 koli">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Berat Barang (kg)</label>
                                        <input type="text" name="berat_barang" class="form-control" value="<?php echo esc_attr($resi_data->berat_barang); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Berat Volumetrik (kg)</label>
                                        <input type="text" name="berat_volumetrik" class="form-control" value="<?php echo esc_attr($resi_data->berat_volumetrik); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jenis Packing</label>
                                        <input type="text" name="jenis_packing" class="form-control" value="<?php echo esc_attr($resi_data->jenis_packing); ?>" required placeholder="Contoh: Kayu / Plastik">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="resi-footer bg-light text-end py-3">
                        <button type="submit" name="save_resi" class="btn btn-primary btn-lg px-5">Simpan Data Resi</button>
                    </div>
                </div>
            </div>

            <!-- Tracking History (Hanya saat Edit) -->
            <?php if ($is_edit) : ?>
                <div class="col-12">
                    <div class="resi shadow-sm mb-4">
                        <div class="resi-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tracking Status</h5>
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTracking">
                                <span class="dashicons dashicons-plus"></span> Update Status
                            </button>
                        </div>
                        <div class="resi-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Kurir</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($tracking_list)) : ?>
                                            <tr>
                                                <td colspan="5" class="p-4 text-center text-muted">Belum ada riwayat tracking.</td>
                                            </tr>
                                        <?php else : ?>
                                            <?php foreach ($tracking_list as $track) : ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($track->waktu)); ?></td>
                                                    <td><span class="badge bg-primary"><?php echo esc_html($track->status); ?></span></td>
                                                    <td><?php echo esc_html($track->keterangan); ?></td>
                                                    <td>
                                                        <?php if ($track->kurir) : ?>
                                                            <span class="badge bg-secondary"><?php echo esc_html($track->kurir); ?></span>
                                                        <?php else : ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Hapus status ini?')) window.location.href='<?php echo wp_nonce_url(admin_url('admin.php?page=velocity-expedisi-resi&action=delete_track&track_id=' . $track->id . '&id=' . $resi_id), 'delete_track_' . $track->id); ?>'">
                                                            Hapus
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
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Update Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="datetime-local" name="waktu" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
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
                            <label class="form-label text-danger fw-bold">Nama Kurir</label>
                            <input type="text" name="kurir" class="form-control border-danger">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_track" class="btn btn-primary">Simpan Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
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
