<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="wp-heading-inline">Daftar Resi (<?php echo ucfirst($type); ?>)</h1>
        <div>
            <a href="<?php echo admin_url('admin-ajax.php?action=resiexport&jenis=' . $type); ?>" class="page-title-action">
                Ekspor Resi
            </a>
            <button type="button" class="page-title-action" data-bs-toggle="modal" data-bs-target="#importResiModal">
                Impor Resi (Media)
            </button>
            <a href="<?php echo admin_url('admin.php?page=velocity-expedisi-resi&action=add&jenis=' . $type); ?>" class="page-title-action">Tambah Resi Baru</a>
        </div>
    </div>
    <hr class="wp-header-end">

    <!-- Modal Impor Resi -->
    <div class="modal fade" id="importResiModal" tabindex="-1" aria-labelledby="importResiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importResiModalLabel">Impor Data Resi (<?php echo ucfirst($type); ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formImportResi">
                    <div class="modal-body">
                        <div class="mb-3 text-dark">
                            <label class="form-label">Pilih File CSV dari Media</label>
                            <div class="d-flex align-items-center">
                                <input type="hidden" id="import_resi_file_id" name="import_file_id">
                                <input type="text" class="form-control me-2" id="import_resi_file_url" readonly placeholder="Pilih file dari media...">
                                <button type="button" class="btn btn-secondary btn-sm" id="btnSelectMediaResi">Pilih</button>
                            </div>
                            <div class="form-text mt-2">
                                <p class="mb-1">Format file harus CSV dengan urutan kolom (tanpa header):</p>
                                <code class="d-block bg-light p-2 rounded">no_resi, nama_pengirim, hp_pengirim, kota_pengirim, negara_pengirim, nama_penerima, hp_penerima, kota_penerima, negara_penerima, nama_barang, jenis_barang, jumlah_barang, berat_barang, berat_volumetrik, jenis_packing</code>
                                <small class="text-muted">Pastikan data sesuai dengan jenis yang dipilih (<?php echo $type; ?>).</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit-import">
                            Impor Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="resi mt-3 shadow-sm">
        <div class="resi-header bg-dark text-white d-flex justify-content-between align-items-center">
            <div class="mb-0">
                <span class="dashicons dashicons-media-spreadsheet me-2"></span>
                Manajemen Resi
            </div>
            <div class="ms-auto">
                <form method="get" action="" class="d-flex align-items-center">
                    <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
                    <label for="jenis_selector" class="text-white me-2 mb-0">Pilih Jenis:</label>
                    <select name="jenis" id="jenis_selector" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="nasional" <?php selected($type, 'nasional'); ?>>Nasional</option>
                        <option value="internasional" <?php selected($type, 'internasional'); ?>>Internasional</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No Resi</th>
                            <th>Jenis</th>
                            <th>Pengirim</th>
                            <th>Penerima</th>
                            <th>Barang</th>
                            <th>Status Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($resi_list)) : ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada data resi.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($resi_list as $resi) : 
                                $status_terakhir = $wpdb->get_row($wpdb->prepare(
                                    "SELECT status, waktu FROM {$wpdb->prefix}resi_tracking WHERE resi_id = %d ORDER BY waktu DESC, id DESC LIMIT 1",
                                    $resi->id
                                ));
                            ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?php echo esc_html($resi->no_resi); ?></td>
                                    <td>
                                        <span class="badge <?php echo $resi->jenis === 'nasional' ? 'bg-info' : 'bg-warning'; ?>">
                                            <?php echo ucfirst(esc_html($resi->jenis)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div><?php echo esc_html($resi->nama_pengirim); ?></div>
                                        <small class="text-muted"><?php echo esc_html($resi->hp_pengirim); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo esc_html($resi->nama_penerima); ?></div>
                                        <small class="text-muted"><?php echo esc_html($resi->hp_penerima); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo esc_html($resi->nama_barang); ?></div>
                                        <small class="text-muted"><?php echo esc_html($resi->jumlah_barang); ?> - <?php echo esc_html($resi->berat_barang); ?>kg</small>
                                    </td>
                                    <td>
                                        <?php if ($status_terakhir) : ?>
                                            <span class="badge bg-success"><?php echo esc_html($status_terakhir->status); ?></span>
                                            <div class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($status_terakhir->waktu)); ?></div>
                                        <?php else : ?>
                                            <span class="badge bg-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo admin_url('admin.php?page=velocity-expedisi-resi&action=edit&id=' . $resi->id . '&jenis=' . $type); ?>" class="btn btn-outline-primary" title="Edit/Track">
                                                <span class="dashicons dashicons-edit"></span>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-delete-resi" data-id="<?php echo $resi->id; ?>" title="Hapus">
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
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

<script>
    jQuery(function($){
        var mediaFrame;
        $('#btnSelectMediaResi').on('click', function(e) {
            e.preventDefault();
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }
            mediaFrame = wp.media({
                title: 'Pilih File CSV',
                button: { text: 'Pilih' },
                multiple: false,
                library: { type: 'text/csv' }
            });
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                $('#import_resi_file_id').val(attachment.id);
                $('#import_resi_file_url').val(attachment.url);
            });
            mediaFrame.open();
        });

        $('#formImportResi').on('submit', function(e){
            e.preventDefault();
            var fileId = $('#import_resi_file_id').val();
            if (!fileId) {
                alert('Pilih file terlebih dahulu.');
                return;
            }

            var $btn = $(this).find('.btn-submit-import');
            var formData = new FormData(this);
            formData.append('action', 'resiimport');
            formData.append('jenis', '<?php echo $type; ?>');
            formData.append('import_file_id', fileId);

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Mengimpor...');

            $.ajax({
                type: 'POST',
                url : '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if(result.success){
                        alert(result.data.message || 'Data resi berhasil diimpor.');
                        location.reload();
                    } else {
                        alert(result.data || 'Gagal mengimpor data resi.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan sistem.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Impor Sekarang');
                }
            });
        });

        $(document).on('click','.btn-delete-resi', function(){
            if (confirm("Hapus data resi ini? Semua riwayat tracking juga akan dihapus.") == true) {
                var id = $(this).data('id');
                var $row = $(this).closest('tr');
                jQuery.ajax({
                    type: 'POST',
                    url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    data: ({action : 'residelete', id:id}),
                    success:function(result) {
                        if(result.success){
                            $row.fadeOut(function(){
                                $(this).remove();
                            });
                        } else {
                            alert('Gagal menghapus data.');
                        }
                    }, 
                });
            }
        });
    });
</script>
