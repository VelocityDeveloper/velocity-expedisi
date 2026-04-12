<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="wp-heading-inline">Daftar Resi (<?php echo ucfirst($type); ?>)</h1>
        <a href="<?php echo admin_url('admin.php?page=velocity-expedisi-resi&action=add&jenis=' . $type); ?>" class="page-title-action">Tambah Resi Baru</a>
    </div>
    <hr class="wp-header-end">

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
