<?php if (!defined('ABSPATH')) exit; ?>

<div class="velocity-tracking-container">
    <div class="tracking-form mb-4">
        <form action="" method="get">
            <div class="input-group">
                <input type="text" name="no_resi" class="form-control" placeholder="Masukkan Nomor Resi" value="<?php echo esc_attr($no_resi); ?>" required>
                <button class="btn btn-primary" type="submit">Lacak Resi</button>
            </div>
        </form>
    </div>

    <?php if ($no_resi): ?>
        <?php if ($resi): ?>
            <div class="tracking-result card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Hasil Pelacakan: <?php echo esc_html($resi->no_resi); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row info-pengiriman">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted border-bottom pb-2">Informasi Pengirim</h6>
                            <p class="mb-1"><strong>Nama:</strong> <?php echo esc_html($resi->nama_pengirim); ?></p>
                            <p class="mb-1"><strong>Kota:</strong> <?php echo esc_html($resi->kota_pengirim ?: '-'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted border-bottom pb-2">Informasi Penerima</h6>
                            <p class="mb-1"><strong>Nama:</strong> <?php echo esc_html($resi->nama_penerima); ?></p>
                            <p class="mb-1"><strong>Kota:</strong> <?php echo esc_html($resi->kota_penerima ?: '-'); ?></p>
                        </div>
                    </div>

                    <div class="row info-barang mt-2">
                        <div class="col-12">
                            <h6 class="text-muted border-bottom pb-2">Informasi Paket</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Nama Barang:</strong> <?php echo esc_html($resi->nama_barang); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Jenis Barang:</strong> <?php echo esc_html($resi->jenis_barang); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Berat:</strong> <?php echo esc_html($resi->berat_barang); ?> kg</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tracking-timeline mt-4">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Riwayat Status</h6>
                        <?php if ($tracking): ?>
                            <div class="timeline-items">
                                <?php foreach ($tracking as $step): ?>
                                    <div class="timeline-item d-flex mb-3">
                                        <div class="timeline-date me-3 text-end" style="min-width: 120px;">
                                            <div class="fw-bold"><?php echo date('d M Y', strtotime($step->waktu)); ?></div>
                                            <small class="text-muted"><?php echo date('H:i', strtotime($step->waktu)); ?></small>
                                        </div>
                                        <div class="timeline-content ps-3 border-start position-relative">
                                            <div class="timeline-dot position-absolute" style="left: -5px; top: 5px; width: 10px; height: 10px; background: #007bff; border-radius: 50%;"></div>
                                            <div class="fw-bold text-primary"><?php echo esc_html($step->status); ?></div>
                                            <div class="text-muted small"><?php echo esc_html($step->keterangan); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Belum ada riwayat pelacakan.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Maaf, nomor resi <strong><?php echo esc_html($no_resi); ?></strong> tidak ditemukan. Silakan periksa kembali nomor resi Anda.
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .velocity-tracking-container .timeline-item {
        position: relative;
    }
    .velocity-tracking-container .timeline-content {
        padding-bottom: 20px;
    }
    .velocity-tracking-container .timeline-items .timeline-item:last-child .timeline-content {
        border-left-color: transparent !important;
        padding-bottom: 0;
    }
    .velocity-tracking-container .card {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .velocity-tracking-container .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,.125);
        border-radius: .5rem .5rem 0 0 !important;
    }
    .velocity-tracking-container .card-body {
        padding: 1.5rem;
    }
    .velocity-tracking-container .timeline-dot {
        left: -6px;
        top: 6px;
        width: 11px;
        height: 11px;
        background: #0d6efd;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #0d6efd;
    }
    .velocity-tracking-container .info-pengiriman h6,
    .velocity-tracking-container .info-barang h6,
    .velocity-tracking-container .tracking-timeline h6 {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }
    .velocity-tracking-container .timeline-date {
        font-size: 0.9rem;
    }
</style>
