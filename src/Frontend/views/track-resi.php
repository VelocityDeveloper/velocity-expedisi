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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Hasil Pelacakan: <span x-text="resi.no_resi"></span></h5>
                    <a :href="'<?php echo home_url('/'); ?>?download_resi_pdf=1&no_resi=' + resi.no_resi" class="btn btn-light btn-sm d-flex align-items-center" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf me-1" viewBox="0 0 16 16">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5v2z"/>
                            <path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.487-.647 18.69 18.69 0 0 0 .397-1.39c.125-.576.191-1.287.188-1.844a3.307 3.307 0 0 1 .09-.904c.094-.347.243-.589.446-.742.204-.154.444-.243.674-.243.548 0 .899.322 1.056.812.115.359.077.796-.105 1.272-.188.493-.513.96-.936 1.307-.423.348-.948.562-1.564.639a15.114 15.114 0 0 1-2.71 1.015c-.348.117-.64.256-.874.416-.234.16-.398.358-.474.61zM10.214 10.5c.029-.01.054-.024.072-.046.1-.115.11-.323.066-.511a1.245 1.245 0 0 0-.12-.317c-.028-.044-.062-.081-.103-.115-.1-.083-.233-.17-.384-.254a2.632 2.632 0 0 0-.154-.08c-.176-.085-.39-.167-.623-.256-.232-.09-.482-.185-.739-.285-.258-.1-.512-.21-.746-.347-.234-.137-.433-.3-.563-.51-.131-.21-.164-.477-.1-.747.064-.27.214-.52.417-.745.202-.226.438-.42.693-.58.255-.16.51-.274.743-.343.233-.07.433-.085.592-.045.16.04.28.14.363.305.083.165.114.397.094.694-.02.297-.087.658-.204 1.087-.117.428-.291.927-.513 1.487-.222.56-.47 1.18-.739 1.844-.268.664-.548 1.39-.824 2.144-.275.755-.536 1.543-.768 2.333-.232.79-.427 1.58-.568 2.333-.14.753-.223 1.423-.23 1.95a1.14 1.14 0 0 1-.035.15c-.015.034-.034.053-.05.06a.072.072 0 0 1-.034.007.085.085 0 0 1-.034-.007.135.135 0 0 1-.05-.06 1.14 1.14 0 0 1-.035-.15 6.464 6.464 0 0 0-.23-1.95c-.007-.527-.09-1.197-.23-1.95-.141-.753-.336-1.543-.568-2.333-.232-.79-.493-1.578-.768-2.333-.276-.754-.556-1.48-.824-2.144-.269-.664-.517-1.284-.739-1.844-.222-.56-.396-1.06-.513-1.487-.117-.429-.184-.79-.204-1.087-.02-.297.011-.529.094-.694.083-.165.203-.265.363-.305.16-.04.36-.025.592.045.233.07.488.184.743.343.255.16.491.354.693.58.203.225.353.475.417.745.064.27.03.537-.1.747-.13.21-.329.373-.563.51-.234.137-.488.247-.746.347-.257.1-.507.195-.739.285-.233.09-.447.17-.623.256-.154.083-.284.17-.384.254-.041.034-.075.071-.103.115-.041.066-.081.173-.12.317-.044.188-.034.396.066.511.018.022.043.036.072.046.03.01.054.024.072.046z"/>
                        </svg>
                        Download PDF
                    </a>
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
