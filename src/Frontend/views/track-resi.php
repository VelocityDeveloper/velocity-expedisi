<?php if (!defined('ABSPATH')) exit; ?>

<div x-data="trackResi()" class="velocity-tracking-container" x-cloak>
    <div class="tracking-form mb-4">
        <form @submit.prevent="submitTrack">
            <div class="input-group">
                <input type="text" x-model="no_resi" class="form-control" placeholder="Masukkan Nomor Resi" required>
                <button class="btn btn-primary" type="submit" :disabled="isLoading">
                    <span x-show="!isLoading">Lacak Resi</span>
                    <span x-show="isLoading" class="spinner-border spinner-border-sm"></span>
                </button>
            </div>
        </form>
    </div>

    <div x-show="hasSearched">
        <template x-if="resi">
            <div class="tracking-result card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Hasil Pelacakan: <span x-text="resi.no_resi"></span></h5>
                </div>
                <div class="card-body">
                    <div class="row info-pengiriman">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted border-bottom pb-2">Informasi Pengirim</h6>
                            <p class="mb-1"><strong>Nama:</strong> <span x-text="resi.nama_pengirim"></span></p>
                            <p class="mb-1"><strong>Kota:</strong> <span x-text="resi.kota_pengirim || '-'"></span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted border-bottom pb-2">Informasi Penerima</h6>
                            <p class="mb-1"><strong>Nama:</strong> <span x-text="resi.nama_penerima"></span></p>
                            <p class="mb-1"><strong>Kota:</strong> <span x-text="resi.kota_penerima || '-'"></span></p>
                        </div>
                    </div>

                    <div class="row info-barang mt-2">
                        <div class="col-12">
                            <h6 class="text-muted border-bottom pb-2">Informasi Paket</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Nama Barang:</strong> <span x-text="resi.nama_barang"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Jenis Barang:</strong> <span x-text="resi.jenis_barang"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Berat:</strong> <span x-text="resi.berat_barang"></span> kg</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tracking-timeline mt-4">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Riwayat Status</h6>
                        <template x-if="tracking.length > 0">
                            <div class="timeline-items">
                                <template x-for="step in tracking" :key="step.id">
                                    <div class="timeline-item d-flex mb-3">
                                        <div class="timeline-date me-3 text-end" style="min-width: 120px;">
                                            <div class="fw-bold" x-text="formatDate(step.waktu)"></div>
                                            <small class="text-muted" x-text="formatTime(step.waktu)"></small>
                                        </div>
                                        <div class="timeline-content ps-3 border-start position-relative">
                                            <div class="timeline-dot position-absolute" style="left: -6px; top: 6px; width: 11px; height: 11px; background: #0d6efd; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px #0d6efd;"></div>
                                            <div class="fw-bold text-primary" x-text="step.status"></div>
                                            <div class="text-muted small" x-text="step.keterangan"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="tracking.length === 0">
                            <div class="alert alert-info">Belum ada riwayat pelacakan.</div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        
        <template x-if="!resi && !isLoading">
            <div class="alert alert-danger">
                Maaf, nomor resi <strong x-text="no_resi"></strong> tidak ditemukan. Silakan periksa kembali nomor resi Anda.
            </div>
        </template>
    </div>
</div>

<script>
function trackResi() {
    return {
        no_resi: '<?php echo esc_js($no_resi); ?>',
        resi: null,
        tracking: [],
        isLoading: false,
        hasSearched: false,

        init() {
            if (this.no_resi) {
                this.submitTrack();
            }
        },

        async submitTrack() {
            if (!this.no_resi) return;
            
            this.isLoading = true;
            this.hasSearched = false;

            const formData = new FormData();
            formData.append('action', 'cek_resi');
            formData.append('no_resi', this.no_resi);

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    this.resi = result.data.resi;
                    this.tracking = result.data.tracking;
                } else {
                    this.resi = null;
                    this.tracking = [];
                }
            } catch (error) {
                console.error('Error tracking resi:', error);
                this.resi = null;
                this.tracking = [];
            } finally {
                this.isLoading = false;
                this.hasSearched = true;
            }
        },

        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
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
