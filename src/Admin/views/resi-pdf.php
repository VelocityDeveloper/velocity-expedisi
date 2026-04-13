<?php 
$site_name = get_bloginfo('name');
$favicon_url = wp_get_attachment_image_url(get_option('site_icon'), 'full');
$pdf_size = get_option('velocity_expedisi_pdf_size', 'A4');
$is_thermal = strpos($pdf_size, 'thermal') !== false;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resi - <?php echo $resi->no_resi; ?></title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: <?php echo $is_thermal ? '10px' : '12px'; ?>; 
            color: #000; 
            line-height: 1.2; 
            margin: 0;
            padding: <?php echo $is_thermal ? '8px' : '20px'; ?>;
        }
        .header { text-align: center; border-bottom: 1px dashed #333; padding-bottom: 5px; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: <?php echo $is_thermal ? '14px' : '20px'; ?>; text-transform: uppercase; }
        .header p { margin: 1px 0 0; font-size: <?php echo $is_thermal ? '9px' : '13px'; ?>; font-weight: bold; }
        .resi-no { margin-top: 3px; font-weight: bold; font-size: <?php echo $is_thermal ? '9px' : '12px'; ?>; }
        
        .section { margin-bottom: 10px; }
        .section-title { 
            background: #eee; 
            padding: 2px 6px; 
            font-weight: bold; 
            border-left: 3px solid #333; 
            margin-bottom: 5px;
            font-size: <?php echo $is_thermal ? '8px' : '11px'; ?>;
        }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { vertical-align: top; padding: 2px; }
        <?php if (!$is_thermal): ?>
        .info-grid td { width: 50%; }
        <?php endif; ?>
        .info-label { font-weight: bold; color: #444; margin-bottom: 1px; font-size: <?php echo $is_thermal ? '9px' : '11px'; ?>; }
        .info-value { margin-bottom: 3px; word-wrap: break-word; }
        
        .package-table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        .package-table th { background: #f5f5f5; text-align: left; padding: 4px; border: 1px solid #ccc; font-size: <?php echo $is_thermal ? '9px' : '11px'; ?>; }
        .package-table td { padding: 4px; border: 1px solid #ccc; vertical-align: top; overflow: hidden; }
        
        <?php if ($is_thermal): ?>
        .package-table th:nth-child(1) { width: 70%; }
        .package-table th:nth-child(2) { width: 30%; }
        <?php endif; ?>
        
        .tracking-timeline { margin-top: 10px; }
        .tracking-item { border-left: 1px solid #ccc; padding-left: 8px; position: relative; margin-bottom: 8px; }
        .tracking-item:before { content: ""; position: absolute; left: -4px; top: 0; width: 6px; height: 6px; background: #333; border-radius: 50%; }
        .tracking-date { font-weight: bold; font-size: <?php echo $is_thermal ? '9px' : '11px'; ?>; }
        .tracking-status { font-weight: bold; margin: 1px 0; }
        .tracking-desc { color: #555; font-size: <?php echo $is_thermal ? '8px' : '10px'; ?>; }
        
        .footer { text-align: center; margin-top: 15px; font-size: <?php echo $is_thermal ? '8px' : '10px'; ?>; border-top: 1px dashed #ccc; padding-top: 5px; }
        
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo $favicon_url; ?>" alt="<?php echo $site_name; ?>" style="width: 50px;">
        <h1><?php echo esc_html($site_name); ?></h1>
        <p>BUKTI PENGIRIMAN (RESI)</p>
        <div class="resi-no">No. Resi: <?php echo esc_html($resi->no_resi); ?></div>
    </div>

    <div class="section">
        <table class="info-grid">
            <?php if ($is_thermal): ?>
            <tr>
                <td>
                    <div class="section-title">PENGIRIM</div>
                    <div class="info-value">
                        <strong><?php echo esc_html($resi->nama_pengirim); ?></strong><br>
                        HP: <?php echo esc_html($resi->hp_pengirim); ?><br>
                        <?php echo esc_html($resi->kota_pengirim); ?>, <?php echo esc_html($resi->negara_pengirim); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="section-title">PENERIMA</div>
                    <div class="info-value">
                        <strong><?php echo esc_html($resi->nama_penerima); ?></strong><br>
                        HP: <?php echo esc_html($resi->hp_penerima); ?><br>
                        <?php echo esc_html($resi->kota_penerima); ?>, <?php echo esc_html($resi->negara_penerima); ?>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <tr>
                <td>
                    <div class="section-title">PENGIRIM</div>
                    <div class="info-label">Nama:</div>
                    <div class="info-value"><?php echo esc_html($resi->nama_pengirim); ?></div>
                    <div class="info-label">HP:</div>
                    <div class="info-value"><?php echo esc_html($resi->hp_pengirim); ?></div>
                    <div class="info-label">Alamat:</div>
                    <div class="info-value"><?php echo esc_html($resi->kota_pengirim); ?>, <?php echo esc_html($resi->negara_pengirim); ?></div>
                </td>
                <td>
                    <div class="section-title">PENERIMA</div>
                    <div class="info-label">Nama:</div>
                    <div class="info-value"><?php echo esc_html($resi->nama_penerima); ?></div>
                    <div class="info-label">HP:</div>
                    <div class="info-value"><?php echo esc_html($resi->hp_penerima); ?></div>
                    <div class="info-label">Alamat:</div>
                    <div class="info-value"><?php echo esc_html($resi->kota_penerima); ?>, <?php echo esc_html($resi->negara_penerima); ?></div>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="section">
        <div class="section-title">INFORMASI PAKET</div>
        <table class="package-table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <?php if (!$is_thermal): ?>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <?php endif; ?>
                    <th>Berat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?php echo esc_html($resi->nama_barang); ?></strong>
                        <?php if ($is_thermal): ?>
                        <div style="font-size: 8px; color: #555; margin-top: 2px;">
                            Jns: <?php echo esc_html($resi->jenis_barang); ?> | Jml: <?php echo esc_html($resi->jumlah_barang); ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php if (!$is_thermal): ?>
                    <td><?php echo esc_html($resi->jenis_barang); ?></td>
                    <td><?php echo esc_html($resi->jumlah_barang); ?></td>
                    <?php endif; ?>
                    <td><?php echo esc_html($resi->berat_barang); ?> kg</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">STATUS TERAKHIR</div>
        <div class="tracking-timeline">
            <?php if ($tracking) : $latest = $tracking[0]; ?>
                <div class="tracking-item">
                    <div class="tracking-date"><?php echo date('d/m/Y H:i', strtotime($latest->waktu)); ?></div>
                    <div class="tracking-status"><?php echo esc_html($latest->status); ?></div>
                    <div class="tracking-desc"><?php echo esc_html($latest->keterangan); ?></div>
                </div>
            <?php else : ?>
                <p>Belum ada riwayat.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        Dicetak pada: <?php echo date('d/m/Y H:i'); ?> - <?php echo esc_html($site_name); ?>
    </div>
</body>
</html>
