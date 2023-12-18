<?php
class Saelog_Resi {

    /**
     * autoload method.
     */
    public function autoload() {
        add_filter('default_title', array($this, 'resi_default_title'));
        add_action('init', array($this, 'register_post_type'));
        add_action( 'cmb2_admin_init', array($this, 'register_metabox'));

        //ajax
        add_action( 'wp_ajax_nopriv_goresi', array( $this, 'ajax_goresi' ) );
        add_action( 'wp_ajax_goresi', array( $this, 'ajax_goresi' ) );

        //shortcode
        add_shortcode( 'cek-resi', array( $this, 'sh_cekresi' ) );
    }

    public function resi_default_title() {
        global $post_type;
        if ('data_resi' == $post_type) {
            $title = mt_rand(100000000, 999999999);
            return $title;
        }
    }

    public function register_post_type() {
        $labels = array(
            'name' => 'Data Resi',
            'singular_name' => 'Data Resi',
            'menu_name' => 'Data Resi',
            'add_new' => 'Tambah Data Resi',
            'add_new_item' => 'Tambah Data Resi Baru',
            'edit' => 'Edit',
            'edit_item' => 'Edit Data Resi',
            'new_item' => 'Data Resi Baru',
            'view' => 'Lihat',
            'view_item' => 'Lihat Data Resi',
            'search_items' => 'Cari Data Resi',
            'not_found' => 'Data Resi tidak ditemukan',
            'not_found_in_trash' => 'Data Resi tidak ditemukan di Trash',
            'parent' => 'Parent Data Resi'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'data-resi'),
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array(
                'title',
                'thumbnail',
            ),
            'menu_position' => 3,
            'menu_icon' => 'dashicons-clipboard',
            'show_in_rest' => true // Gunakan jika menggunakan blok editor Gutenberg (WordPress 5.0+)
        );

        register_post_type('data_resi', $args);
    }

    public function register_metabox() {
        $cmb = new_cmb2_box( array(
            'id'            => 'data_resi_metabox',
            'title'         => __( 'Detail resi', 'cmb2' ),
            'object_types'  => array( 'data_resi'),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ) );

        $cmb->add_field( array(
            'name' => __( 'Pengirim', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'pengirim',
            'type' => 'title',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Nama Pengirim', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'nama_pengirim',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'No.HP Pengirim', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'nohp_pengirim',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Kota Pengirim', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'kota_pengirim',
            'type' => 'text',
        ) );

        $cmb->add_field( array(
            'name' => __( 'Penerima', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'penerima',
            'type' => 'title',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Nama Penerima', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'nama_penerima',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'No.HP Penerima', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'nohp_penerima',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Kota Penerima', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'kota_penerima',
            'type' => 'text',
        ) );

        $cmb->add_field( array(
            'name' => __( 'Data Barang', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'data_barang',
            'type' => 'title',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Jumlah Barang', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'jumlah_barang',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Berat Barang/Volume', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'berat_barang',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Jenis Barang', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'jenis_barang',
            'type' => 'text',
        ) );
        $cmb->add_field( array(
            'name' => __( 'Jenis Packing', 'cmb2' ),
            'desc' => __( '', 'cmb2' ),
            'id'   => 'jenis_packing',
            'type' => 'select',
            'options' => array(
                'Curah'     => __( 'Curah', 'cmb2' ),
                'Kardus'    => __( 'Kardus', 'cmb2' ),
                'Karung'    => __( 'Karung', 'cmb2' ),
                'Plastik'   => __( 'Plastik', 'cmb2' ),
                'Peti Kayu' => __( 'Peti Kayu', 'cmb2' ),
                'Palet'     => __( 'Palet', 'cmb2' ),
            ),
        ) );

        $group_field_id = $cmb->add_field( array(
            'id'          => 'status',
            'type'        => 'group',
            'description' => __( 'Status barang', 'cmb2' ),
            'options'     => array(
                'group_title'       => __( 'Entry {#}', 'cmb2' ),
                'add_button'        => __( 'Add Another Entry', 'cmb2' ),
                'remove_button'     => __( 'Remove Entry', 'cmb2' ),
                'sortable'          => true,                
            ),
        ) );
        $cmb->add_group_field( $group_field_id, array(
            'name' => 'Waktu',
            'id'   => 'waktu',
            'type' => 'text_datetime_timestamp',
            'time_format' => 'H:i',
        ) );
        $cmb->add_group_field( $group_field_id, array(
            'name' => 'Keterangan',
            'id'   => 'keterangan',
            'type' => 'textarea_small',            
        ) );
    }

    public function data_resi($id){
        $data = [
            'resi'      => get_the_title($id),
            'tanggal'   => get_the_date('d-M-Y',$id),
        ];

        $meta = [
            'status',
            'jenis_packing',
            'jenis_barang',
            'jumlah_barang',
            'berat_barang',
            'nama_pengirim',
            'nohp_pengirim',
            'kota_pengirim',
            'nama_penerima',
            'nohp_penerima',
            'kota_penerima'
        ];
        foreach ($meta as $mt) {
            $data[$mt] = get_post_meta($id,$mt,true);
        }

        return $data;
    }

    public function card_resi($id){
        $data = $this->data_resi($id);
        foreach ($data as $key => $value) {            
            ${$key} = $value;
        }
        ?>
        <div class="card">
            <div class="card-header bg-primary text-white fw-bold">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upc-scan me-1" viewBox="0 0 16 16">
                    <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z"/>
                </svg>
                <?php echo $resi; ?>
            </div>
            <div class="card-body">

                <div class="row border-bottom">
                    <div class="col-6">
                        <div class="fw-bold">Pengirim</div>
                        <ul class="ps-3">
                            <li>Nama: <?php echo $nama_pengirim; ?></li>
                            <li>Hp: <?php echo $nohp_pengirim; ?></li>
                        </ul>
                        <div class="fw-bold">Penerima</div>
                        <ul class="ps-3 pb-0">
                            <li>Nama: <?php echo $nama_penerima; ?></li>
                            <li>Hp: <?php echo $nohp_penerima; ?></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold">Barang</div>
                        <ul class="ps-3 pb-0">
                            <li>Jumlah: <?php echo $jumlah_barang; ?></li>
                            <li>Berat: <?php echo $berat_barang; ?></li>
                            <li>Jenis: <?php echo $jenis_barang; ?></li>
                            <li>Jenis Packing: <?php echo $jenis_packing; ?></li>
                        </ul>
                    </div>
                </div>

                <?php if($status): ?>
                    <div class="d-flex flex-column-reverse mt-3">
                    <?php foreach( $status as $st): ?>
                        <div class="d-flex mb-2">
                            <div class="pe-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-record-circle text-success" viewBox="0 0 16 16"> <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/> <path d="M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/> </svg>
                            </div>
                            <div>
                                <?php if($st['waktu']){ ?>
                                    <div>
                                        <small class="opacity-75">
                                            <?php echo date('Y/m/d H:i', $st['waktu']); ?>
                                        </small>
                                    </div>
                                <?php } ?>
                                <div>
                                    <?php echo $st['keterangan']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
            <div class="card-footer text-end">
                <a href="<?php echo get_site_url(); ?>/print-resi/?resi=<?php echo $resi; ?>" target="_blank" class="btn btn-sm btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"> <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/> <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/> </svg>
                    Cetak
                </a>
            </div>
        </div>
        <?php
    }

    public function ajax_goresi(){
        $resi = isset($_POST['resi']) ? $_POST['resi'] : '';

        if(empty($resi))
        return false;

        $args = array("post_type" => "data_resi", "s" => $resi);
        $query = get_posts( $args );

        if(empty($query))
        return false;

        foreach ($query as $qry) {
            $this->card_resi($qry->ID);
        }

        wp_die();
    }
    
    public function sh_cekresi() {
        ob_start();
        $id = rand(999,9999);
        ?>
        <div class="cek-resi" style="max-width: 40rem;margin: 0 auto;">
            <form action="" method="POST" class="card">
                <div class="card-body">
                    <div class="form-floating mb-2">
                        <input type="text" name="resi" id="resi" class="form-control" placeholder="No resi" required>
                        <label for="resi">Nomor Resi</label>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-dark">
                            Lacak
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card-result-<?php echo $id;?> mt-3"></div>
            <script>                
                jQuery(function($){ 
                    $('.cek-resi form').submit(function(event) {
                        $('.card-result-<?php echo $id;?>').html('<div class="spinner-border" role="status"> <span class="visually-hidden">Loading...</span> </div>');
                        jQuery.ajax({
                            type: 'POST',
                            url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                            data: ({
                                action : 'goresi',
                                resi: $('.cek-resi input[name="resi"]').val(),
                            }),
                            success:function(result) {
                                if(result == '0') {
                                    $('.cek-resi .card-result-<?php echo $id;?>').html('<div class="alert-dismissible alert alert-warning mt-3">Data tidak ditemukan <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                                } else {
                                    $('.cek-resi .card-result-<?php echo $id;?>').html(result);
                                }
                            },
                        });
                        event.preventDefault();
                    });
                });
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Inisialisasi class Saelog_Resi autoload()
$tarif = new Saelog_Resi();
$tarif->autoload();