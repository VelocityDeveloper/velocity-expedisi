<?php
$type = isset($type) ? $type : get_option('velocity_expedisi_type', 'nasional');
$label_asal = $type == 'internasional' ? 'Negara Asal' : 'Kota Asal';
$label_tujuan = $type == 'internasional' ? 'Negara Tujuan' : 'Kota Tujuan';
?>

<div x-data="tarifManager()" class="ongkir-opt container mt-3 p-0 card border-0 shadow" x-cloak>
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16"> <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/> </svg>
            Manajemen Tarif (<?php echo ucfirst($type); ?>)
        </div>
        <div class="ms-auto d-flex align-items-center">
            <a href="<?php echo admin_url('admin-ajax.php?action=tarifexport&jenis=' . $type); ?>" class="btn btn-sm btn-outline-light me-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Ekspor Data
            </a>
            <button type="button" class="btn btn-sm btn-outline-light me-3" data-bs-toggle="modal" data-bs-target="#importModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel me-1" viewBox="0 0 16 16">
                    <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.117 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h4.5v2z"/>
                </svg>
                Impor Data
            </button>
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

    <!-- Modal Impor -->
    <div class="modal fade text-dark" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Impor Data Tarif (<?php echo ucfirst($type); ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="importData">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih File CSV</label>
                            <div class="d-flex align-items-center">
                                <input type="hidden" id="import_file_id" x-model="importFileId">
                                <input type="text" class="form-control me-2" id="import_file_url" readonly placeholder="Pilih file dari media..." x-model="importFileUrl">
                                <button type="button" class="btn btn-secondary btn-sm" @click="selectMedia">Pilih</button>
                            </div>
                            <div class="form-text mt-2">
                                <p class="mb-1">Format file harus CSV dengan urutan kolom:</p>
                                <code class="d-block bg-light p-2 rounded">asal, tujuan, biaya, biaya_volumetrik, min</code>
                                <small class="text-muted">Pastikan baris pertama adalah header sesuai urutan di atas.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" :disabled="isImporting || !importFileId">
                            <span x-show="!isImporting">Impor Sekarang</span>
                            <span x-show="isImporting" class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card m-0 p-0 form-tarif" :class="isEditing ? 'border-info shadow' : ''">
                    <div class="card-header font-weight-bold" :class="isEditing ? 'bg-info text-white' : ''" x-text="isEditing ? 'Edit Data' : 'Tambah data'"></div>
                    <form @submit.prevent="saveData" class="card-body">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="asal" x-model="formData.asal" required>
                                <option value="">Pilih <?php echo $label_asal; ?></option>
                                <template x-for="city in cities" :key="city">
                                    <option :value="city" x-text="city" :selected="formData.asal == city"></option>
                                </template>
                            </select>
                            <label for="asal"><?php echo $label_asal; ?></label>
                        </div>
                        <div class="form-floating mb-3">                        
                            <select class="form-select" id="tujuan" x-model="formData.tujuan" required>
                                <option value="">Pilih <?php echo $label_tujuan; ?></option>
                                <template x-for="city in cities" :key="city">
                                    <option :value="city" x-text="city" :selected="formData.tujuan == city"></option>
                                </template>
                            </select>
                            <label for="tujuan"><?php echo $label_tujuan; ?></label>    
                        </div>  
                        <div class="form-floating mb-3">  
                            <input required x-model="formData.biaya" class="form-control" type="number">
                            <label for="biaya">Biaya / kg</label> 
                        </div>
                        <div class="form-floating mb-3">  
                            <input required x-model="formData.biaya_volumetrik" class="form-control" type="number">
                            <label for="biaya_volumetrik">Biaya Volumetrik / kubik</label> 
                        </div>
                        <div class="form-floating mb-3">  
                            <input required x-model="formData.min" class="form-control" type="number">
                            <label for="min">Minimal Order (kg)</label> 
                        </div>
                        <div class="text-end">
                            <button type="button" @click="resetForm" class="btn btn-sm btn-secondary" x-show="isEditing || formData.asal || formData.tujuan || formData.biaya">
                                Batal
                            </button>
                            <button class="btn btn-sm btn-success" type="submit" :disabled="isLoading">
                                <span x-show="!isLoading">Simpan</span>
                                <span x-show="isLoading" class="spinner-border spinner-border-sm"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="border mt-1 p-2 p-md-3 rounded">
                    <div x-show="message" class="alert" :class="message.type == 'success' ? 'alert-success' : 'alert-danger'" x-text="message.text" @click="message = null"></div>
                    
                    <table class="table table-striped">
                        <thead class="thead-light">
                            <tr><th>Asal</th><th>Tujuan</th><th>Biaya (/kg)</th><th>Biaya Volumetrik</th><th>Min</th><th></th></tr> 
                        </thead>
                        <tbody>
                            <template x-for="item in details" :key="item.id">
                                <tr :class="'tr-' + item.id">
                                    <td x-text="item.asal"></td>
                                    <td x-text="item.tujuan"></td>
                                    <td x-text="item.biaya"></td>
                                    <td x-text="item.biaya_volumetrik"></td>
                                    <td x-text="item.min"></td>
                                    <td>
                                        <button @click="deleteData(item.id)" class="link-danger bg-transparent border-0" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/> </svg>
                                        </button>
                                        <button @click="editData(item)" class="link-info bg-transparent border-0" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"> <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1-.11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/> </svg>
                                        </button>	
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="details.length === 0">
                                <td colspan="6" class="text-center">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tarifManager() {
    return {
        type: '<?php echo $type; ?>',
        details: <?php echo json_encode($details); ?>,
        cities: [],
        formData: {
            id: '',
            asal: '',
            tujuan: '',
            biaya: '',
            biaya_volumetrik: '',
            min: ''
        },
        isEditing: false,
        isLoading: false,
        isImporting: false,
        importFileId: '',
        importFileUrl: '',
        message: null,

        init() {
            this.loadCities();
        },

        selectMedia() {
            const frame = wp.media({
                title: 'Pilih File CSV',
                button: { text: 'Pilih' },
                multiple: false,
                library: { type: 'text/csv' }
            });

            frame.on('select', () => {
                const attachment = frame.state().get('selection').first().toJSON();
                this.importFileId = attachment.id;
                this.importFileUrl = attachment.url;
            });

            frame.open();
        },

        async importData() {
            if (!this.importFileId) return;

            this.isImporting = true;
            this.message = null;

            const formData = new FormData();
            formData.append('action', 'tarifimport');
            formData.append('jenis', this.type);
            formData.append('import_file_id', this.importFileId);

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    this.message = { type: 'success', text: result.data.message || 'Data berhasil diimpor' };
                    // Refresh data
                    location.reload();
                } else {
                    this.message = { type: 'danger', text: result.data || 'Gagal mengimpor data' };
                }
            } catch (error) {
                this.message = { type: 'danger', text: 'Terjadi kesalahan sistem' };
            } finally {
                this.isImporting = false;
                bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                this.importFileId = '';
                this.importFileUrl = '';
            }
        },

        async loadCities() {
            const storageKey = "data_expedisi_" + this.type;
            const jsonFile = this.type === 'internasional' ? 'countries.json' : 'city.json';
            
            let cachedData = localStorage.getItem(storageKey);
            let datacity = null;
            
            try {
                datacity = cachedData ? JSON.parse(cachedData) : null;
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

        async saveData() {
            this.isLoading = true;
            this.message = null;

            const formData = new FormData();
            formData.append('action', 'tarifsave');
            formData.append('jenis', this.type);
            for (let key in this.formData) {
                formData.append(key, this.formData[key]);
            }

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    if (this.isEditing) {
                        const index = this.details.findIndex(item => item.id == this.formData.id);
                        if (index !== -1) {
                            this.details[index] = result.data;
                        }
                        this.message = { type: 'success', text: 'Data berhasil diperbarui' };
                    } else {
                        this.details.unshift(result.data);
                        this.message = { type: 'success', text: 'Data berhasil ditambahkan' };
                    }
                    this.resetForm();
                } else {
                    this.message = { type: 'danger', text: result.data || 'Gagal menyimpan data' };
                }
            } catch (error) {
                this.message = { type: 'danger', text: 'Terjadi kesalahan sistem' };
            } finally {
                this.isLoading = false;
            }
        },

        async deleteData(id) {
            if (!confirm("Hapus data ?")) return;

            const formData = new FormData();
            formData.append('action', 'tarifdelete');
            formData.append('id', id);

            try {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    this.details = this.details.filter(item => item.id != id);
                    this.message = { type: 'success', text: 'Data berhasil dihapus' };
                }
            } catch (error) {
                alert('Gagal menghapus data');
            }
        },

        editData(item) {
            this.isEditing = true;
            this.formData = { ...item };
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: '',
                asal: '',
                tujuan: '',
                biaya: '',
                biaya_volumetrik: '',
                min: ''
            };
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
