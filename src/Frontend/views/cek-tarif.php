<?php if (!defined('ABSPATH')) exit; ?>

<div class="velocity-tarif-container mt-4">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3 px-4">
            <h5 class="mb-0 d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calculator me-2" viewBox="0 0 16 16">
                    <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/>
                    <path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-2zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                </svg>
                Cek Tarif Pengiriman (<?php echo ucfirst($type); ?>)
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="" method="get">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="asal" class="form-label fw-bold">Asal</label>
                        <input list="list_asal" name="asal" id="asal" class="form-control" placeholder="Ketik Kota Asal..." value="<?php echo esc_attr($asal); ?>" required autocomplete="off">
                        <datalist id="list_asal" class="listcity"></datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="tujuan" class="form-label fw-bold">Tujuan</label>
                        <input list="list_tujuan" name="tujuan" id="tujuan" class="form-control" placeholder="Ketik Kota Tujuan..." value="<?php echo esc_attr($tujuan); ?>" required autocomplete="off">
                        <datalist id="list_tujuan" class="listcity"></datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="berat" class="form-label fw-bold">Berat (kg)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="berat" id="berat" class="form-control" placeholder="1" value="<?php echo esc_attr($berat ?: 1); ?>" min="0.1" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                        Cek Ongkos Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($asal && $tujuan): ?>
        <div class="mt-4">
            <?php if ($tarif_result): ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-success text-white py-3">
                        <h6 class="mb-0">Hasil Estimasi Tarif</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Layanan</th>
                                        <th>Rute</th>
                                        <th>Berat</th>
                                        <th class="text-end pe-4">Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tarif_result as $row): ?>
                                        <?php 
                                        $final_weight = max($berat, (float)$row->min);
                                        $total_cost = $final_weight * (float)$row->biaya;
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-primary"><?php echo ucfirst($row->jenis); ?> Service</div>
                                                <small class="text-muted">Min. Order: <?php echo $row->min; ?> kg</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border"><?php echo esc_html($row->asal); ?></span>
                                                <span class="mx-1 text-muted">→</span>
                                                <span class="badge bg-light text-dark border"><?php echo esc_html($row->tujuan); ?></span>
                                            </td>
                                            <td>
                                                <div><?php echo number_format($berat, 1); ?> kg</div>
                                                <?php if ($berat < (float)$row->min): ?>
                                                    <small class="text-danger">(Dihitung min. <?php echo $row->min; ?> kg)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="fs-5 fw-bold text-success">Rp <?php echo number_format($total_cost, 0, ',', '.'); ?></div>
                                                <small class="text-muted">Rp <?php echo number_format((float)$row->biaya, 0, ',', '.'); ?> / kg</small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle me-3" viewBox="0 0 16 16">
                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/>
                    </svg>
                    <div>
                        Maaf, tarif untuk rute <strong><?php echo esc_html($asal); ?></strong> ke <strong><?php echo esc_html($tujuan); ?></strong> belum tersedia. Silakan hubungi admin untuk informasi lebih lanjut.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    jQuery(function($){
        var expedisi_type = '<?php echo $type; ?>';
        var storage_key = "data_expedisi_" + expedisi_type;
        var json_file = expedisi_type == 'internasional' ? 'countries.json' : 'city.json';

        function loadcity(){
            return jQuery.ajax({
                url : '<?php echo VELOCITY_EXPEDISI_PLUGIN_URL; ?>src/Core/' + json_file,
                success:function(dataarray) {
                    localStorage.setItem(storage_key, JSON.stringify(dataarray));
                },
            });
        }
        function populateCityList(){
            var datacity = localStorage.getItem(storage_key);
            try {
                datacity = datacity ? JSON.parse(datacity) : null;
            } catch (e) {
                datacity = null;
            }

            if (!Array.isArray(datacity)) {
                loadcity().then(function(data) {
                    renderOptions(data);
                });
            } else { 
                renderOptions(datacity); 
            }
        }

        function renderOptions(datacity) {
            if (!Array.isArray(datacity)) return;
            var options = '';
            datacity.forEach(item => {
                var ct = expedisi_type == 'internasional' ? item.country : item.city_name;
                if(expedisi_type == 'nasional' && item.type=='Kota'){
                    ct += ' '+item.type;
                }
                options += '<option value="'+ct+'">';
            });
            $('.listcity').html(options);
        }
        populateCityList();
    });
</script>

<style>
    .velocity-tarif-container .card {
        border-radius: 1rem;
    }
    .velocity-tarif-container .form-control:focus, 
    .velocity-tarif-container .btn:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }
    .velocity-tarif-container .input-group-text {
        background-color: #f8f9fa;
        border-left: 0;
    }
    .velocity-tarif-container .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .velocity-tarif-container .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
</style>
