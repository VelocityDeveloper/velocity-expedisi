<?php if (!defined('ABSPATH')) exit; 
$volumetrik_enable = get_option('velocity_expedisi_volumetrik_enable', '0');
$volumetrik_divisor = get_option('velocity_expedisi_volumetrik_divisor', '4000');
?>

<div x-data="cekTarif()" class="velocity-tarif-container mt-4" x-cloak>
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
            <form @submit.prevent="submitCek">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="asal" class="form-label fw-bold">Asal</label>
                        <input list="list_asal" x-model="formData.asal" id="asal" class="form-control" placeholder="Ketik Kota Asal..." required autocomplete="off">
                        <datalist id="list_asal">
                            <template x-for="city in cities" :key="city">
                                <option :value="city"></option>
                            </template>
                        </datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="tujuan" class="form-label fw-bold">Tujuan</label>
                        <input list="list_tujuan" x-model="formData.tujuan" id="tujuan" class="form-control" placeholder="Ketik Kota Tujuan..." required autocomplete="off">
                        <datalist id="list_tujuan">
                            <template x-for="city in cities" :key="city">
                                <option :value="city"></option>
                            </template>
                        </datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="berat" class="form-label fw-bold">Berat (kg)</label>
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
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Panjang (cm)</label>
                                <input type="number" x-model="formData.panjang" class="form-control form-control-sm" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Lebar (cm)</label>
                                <input type="number" x-model="formData.lebar" class="form-control form-control-sm" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tinggi (cm)</label>
                                <input type="number" x-model="formData.tinggi" class="form-control form-control-sm" placeholder="0">
                            </div>
                        </div>
                        <div class="mt-2 small text-muted">
                            Berat Volumetrik: <span class="fw-bold text-primary" x-text="volumetrikWeight.toFixed(2)"></span> kg
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm" :disabled="isLoading">
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
                                <template x-for="row in results" :key="row.id">
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-primary" x-text="row.jenis.charAt(0).toUpperCase() + row.jenis.slice(1) + ' Service'"></div>
                                            <small class="text-muted">Min. Order: <span x-text="row.min"></span> kg</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border" x-text="row.asal"></span>
                                            <span class="mx-1 text-muted">→</span>
                                            <span class="badge bg-light text-dark border" x-text="row.tujuan"></span>
                                        </td>
                                        <td>
                                            <div x-text="finalWeight + ' kg'"></div>
                                            <template x-if="parseFloat(finalWeight) < parseFloat(row.min)">
                                                <small class="text-danger">(Dihitung min. <span x-text="row.min"></span> kg)</small>
                                            </template>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="fs-5 fw-bold text-success" x-text="formatRupiah(Math.max(finalWeight, row.min) * row.biaya)"></div>
                                            <small class="text-muted" x-text="formatRupiah(row.biaya) + ' / kg'"></small>
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
        type: '<?php echo $type; ?>',
        volumetrikDivisor: <?php echo $volumetrik_divisor; ?>,
        cities: [],
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
            this.loadCities();
            if (this.formData.asal && this.formData.tujuan) {
                this.submitCek();
            }
        },

        async loadCities() {
            const storageKey = "data_expedisi_" + this.type;
            const jsonFile = this.type === 'internasional' ? 'countries.json' : 'city.json';
            let datacity = localStorage.getItem(storageKey);
            
            try {
                datacity = datacity ? JSON.parse(datacity) : null;
            } catch (e) {
                datacity = null;
            }

            if (!Array.isArray(datacity)) {
                try {
                    const response = await fetch('<?php echo VELOCITY_EXPEDISI_PLUGIN_URL; ?>src/Core/' + jsonFile);
                    datacity = await response.json();
                    localStorage.setItem(storageKey, JSON.stringify(datacity));
                } catch (error) {
                    console.error('Failed to load cities:', error);
                    return;
                }
            }

            this.cities = datacity.map(item => {
                let ct = this.type === 'internasional' ? item.country : item.city_name;
                if (this.type === 'nasional' && item.type === 'Kota') {
                    ct += ' ' + item.type;
                }
                return ct;
            });
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
</style>
