<?php if (!defined('ABSPATH')) exit; 
$volumetrik_enable = get_option('velocity_expedisi_volumetrik_enable', '0');
$volumetrik_divisor = get_option('velocity_expedisi_volumetrik_divisor', '4000');
?>

<div x-data="cekTarif()" class="velocity-tarif-container mt-4" x-cloak>
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3 px-3 px-sm-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <h5 class="mb-0 d-flex align-items-center col-md-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calculator me-2 flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/>
                        <path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-2zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                    </svg>
                    <span class="text-wrap">Cek Tarif Pengiriman</span>
                </h5>
                <?php if ($type === 'nasional-internasional') : ?>
                <div class="btn-group btn-group-sm w-100 w-sm-auto" role="group">
                    <input type="radio" class="btn-check" name="type_selector" id="type_nasional" value="nasional" x-model="type" @change="initLists()">
                    <label class="btn btn-outline-light px-3" for="type_nasional">Nasional</label>

                    <input type="radio" class="btn-check" name="type_selector" id="type_internasional" value="internasional" x-model="type" @change="initLists()">
                    <label class="btn btn-outline-light px-3" for="type_internasional">Internasional</label>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-3 p-sm-4">
            <form @submit.prevent="submitCek">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="asal" class="form-label fw-bold mb-1" x-text="type === 'internasional' ? 'Negara Asal' : 'Kota Asal'">Asal</label>
                        <select x-model="formData.asal" id="asal" class="form-select select2-asal" required @change="onAsalChange()">
                            <option value="">-- Pilih --</option>
                            <template x-for="city in origins" :key="city">
                                <option :value="city" x-text="city"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tujuan" class="form-label fw-bold mb-1" x-text="type === 'internasional' ? 'Negara Tujuan' : 'Kota Tujuan'">Tujuan</label>
                        <select x-model="formData.tujuan" id="tujuan" class="form-select select2-tujuan" required @change="onTujuanChange()">
                            <option value="">-- Pilih --</option>
                            <template x-for="city in destinations" :key="city">
                                <option :value="city" x-text="city"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="berat" class="form-label fw-bold mb-1">Berat (kg)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" x-model="formData.berat" id="berat" class="form-control" placeholder="1" min="0.1" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>

                <?php if ($volumetrik_enable === '1') : ?>
                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="formData.useVolumetrik" id="useVolumetrik">
                            <label class="form-check-label fw-bold" for="useVolumetrik">
                                Hitung Volumetrik
                            </label>
                        </div>
                    </div>

                    <div x-show="formData.useVolumetrik" class="mt-3 p-3 bg-light rounded-3 border" x-transition>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small fw-bold mb-1">P (cm)</label>
                                <input type="number" x-model="formData.panjang" class="form-control form-control-sm" placeholder="0">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold mb-1">L (cm)</label>
                                <input type="number" x-model="formData.lebar" class="form-control form-control-sm" placeholder="0">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold mb-1">T (cm)</label>
                                <input type="number" x-model="formData.tinggi" class="form-control form-control-sm" placeholder="0">
                            </div>
                        </div>
                        <div class="mt-2 small text-muted">
                            Berat Volumetrik: <span class="fw-bold text-primary" x-text="volumetrikWeight.toFixed(2)"></span> kg
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100 w-md-auto px-5 rounded-pill shadow-sm" :disabled="isLoading">
                        <span x-show="!isLoading">Cek Ongkos Kirim</span>
                        <span x-show="isLoading" class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4" x-show="hasSearched">
        <template x-if="results.length > 0">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-3 px-3">
                    <h6 class="mb-0">Hasil Estimasi Tarif</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 ps-sm-4">Layanan</th>
                                    <th>Rute</th>
                                    <th>Berat</th>
                                    <th class="text-end pe-3 pe-sm-4">Total Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="row in results" :key="row.id">
                                    <tr>
                                        <td class="ps-3 ps-sm-4">
                                            <div class="fw-bold text-primary" x-text="row.jenis.charAt(0).toUpperCase() + row.jenis.slice(1) + ' Service'"></div>
                                            <small class="text-muted">Min. Order: <span x-text="row.min"></span> kg</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center flex-wrap gap-1">
                                                <span class="badge bg-light text-dark border small" x-text="row.asal"></span>
                                                <span class="text-muted">→</span>
                                                <span class="badge bg-light text-dark border small" x-text="row.tujuan"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold" x-text="finalWeight + ' kg'"></div>
                                            <template x-if="parseFloat(finalWeight) < parseFloat(row.min)">
                                                <small class="text-danger d-block" style="font-size: 0.7rem;">(Min. <span x-text="row.min"></span> kg)</small>
                                            </template>
                                        </td>
                                        <td class="text-end pe-3 pe-sm-4">
                                            <div class="fs-6 fw-bold text-success" x-text="formatRupiah(Math.max(finalWeight, row.min) * (volumetrikWeight > formData.berat ? (row.biaya_volumetrik || row.biaya) : row.biaya))"></div>
                                            <small class="text-muted" style="font-size: 0.75rem;" x-text="formatRupiah(volumetrikWeight > formData.berat ? (row.biaya_volumetrik || row.biaya) : row.biaya) + ' / kg'"></small>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
        
        <template x-if="results.length === 0">
            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle me-3" viewBox="0 0 16 16">
                    <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                    <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/>
                </svg>
                <div>
                    Maaf, tarif untuk rute <strong x-text="formData.asal"></strong> ke <strong x-text="formData.tujuan"></strong> belum tersedia. Silakan hubungi admin untuk informasi lebih lanjut.
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function cekTarif() {
    return {
        type: '<?php echo $type === 'nasional-internasional' ? 'nasional' : $type; ?>',
        volumetrikDivisor: <?php echo $volumetrik_divisor; ?>,
        origins: [],
        destinations: [],
        formData: {
            asal: '<?php echo esc_js($asal); ?>',
            tujuan: '<?php echo esc_js($tujuan); ?>',
            berat: '<?php echo esc_js($berat ?: 1); ?>',
            useVolumetrik: false,
            panjang: 0,
            lebar: 0,
            tinggi: 0
        },
        results: [],
        isLoading: false,
        hasSearched: false,

        get volumetrikWeight() {
            if (!this.formData.useVolumetrik) return 0;
            const vol = (parseFloat(this.formData.panjang || 0) * parseFloat(this.formData.lebar || 0) * parseFloat(this.formData.tinggi || 0)) / this.volumetrikDivisor;
            return vol || 0;
        },

        get finalWeight() {
            const berat = parseFloat(this.formData.berat || 0);
            const volumetrik = this.volumetrikWeight;
            return Math.max(berat, volumetrik);
        },

        init() {
            this.initLists();
            if (this.formData.asal && this.formData.tujuan) {
                this.submitCek();
            }

            // Initialize Select2
            this.$nextTick(() => {
                this.initSelect2();
            });
        },

        initSelect2() {
            const self = this;
            jQuery('.select2-asal').select2({
                placeholder: this.type === 'internasional' ? 'Pilih Negara Asal' : 'Pilih Kota Asal',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                self.formData.asal = jQuery(this).val();
                self.onAsalChange();
            });

            jQuery('.select2-tujuan').select2({
                placeholder: this.type === 'internasional' ? 'Pilih Negara Tujuan' : 'Pilih Kota Tujuan',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                self.formData.tujuan = jQuery(this).val();
                self.onTujuanChange();
            });
        },

        async initLists() {
            this.isLoading = true;
            await Promise.all([
                this.loadOrigins(),
                this.loadDestinations()
            ]);
            this.isLoading = false;
            
            // Refresh Select2 options
            this.$nextTick(() => {
                jQuery('.select2-asal, .select2-tujuan').select2('destroy');
                this.initSelect2();
            });
        },

        async loadOrigins(destination = '') {
            const formData = new FormData();
            formData.append('action', 'get_origins');
            formData.append('type', this.type);
            if (destination) formData.append('destination', destination);

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    this.origins = result.data;
                    this.$nextTick(() => {
                        jQuery('.select2-asal').select2('destroy');
                        this.initSelect2();
                    });
                }
            } catch (error) {
                console.error('Failed to load origins:', error);
            }
        },

        async loadDestinations(origin = '') {
            const formData = new FormData();
            formData.append('action', 'get_destinations');
            formData.append('type', this.type);
            if (origin) formData.append('origin', origin);

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    this.destinations = result.data;
                    this.$nextTick(() => {
                        jQuery('.select2-tujuan').select2('destroy');
                        this.initSelect2();
                    });
                }
            } catch (error) {
                console.error('Failed to load destinations:', error);
            }
        },

        async onAsalChange() {
            if (this.formData.asal) {
                await this.loadDestinations(this.formData.asal);
            } else {
                await this.loadDestinations();
            }
        },

        async onTujuanChange() {
            if (this.formData.tujuan) {
                await this.loadOrigins(this.formData.tujuan);
            } else {
                await this.loadOrigins();
            }
        },

        async submitCek() {
            if (!this.formData.asal || !this.formData.tujuan) return;
            
            this.isLoading = true;
            this.hasSearched = false;

            const formData = new FormData();
            formData.append('action', 'cek_tarif');
            formData.append('asal', this.formData.asal);
            formData.append('tujuan', this.formData.tujuan);
            formData.append('type', this.type);

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    this.results = result.data;
                } else {
                    this.results = [];
                }
            } catch (error) {
                console.error('Error fetching tarif:', error);
                this.results = [];
            } finally {
                this.isLoading = false;
                this.hasSearched = true;
            }
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
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
    /* Select2 Bootstrap 5 compatibility */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-dropdown {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 767.98px) {
        .velocity-tarif-container .table-responsive {
            overflow-x: visible !important;
        }

        .velocity-tarif-container .table {
            min-width: 100% !important;
            margin-bottom: 0;
        }

        .velocity-tarif-container .table thead {
            display: none;
        }

        .velocity-tarif-container .table,
        .velocity-tarif-container .table tbody,
        .velocity-tarif-container .table tr,
        .velocity-tarif-container .table td {
            display: block;
            width: 100%;
        }

        .velocity-tarif-container .table tr {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            background: #fff;
        }

        .velocity-tarif-container .table td {
            padding: 6px 0 !important;
            text-align: left !important;
            border: 0 !important;
        }

        .velocity-tarif-container .table td:last-child {
            padding-bottom: 0 !important;
        }
    }
</style>