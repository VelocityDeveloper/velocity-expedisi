<?php
class Saelog_Tarif {

    public $wpdb;
    public $table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . "tarif";
    }

    public function create_db() {
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE IF NOT EXISTS $this->table 
        (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            asal varchar(255) NOT NULL,
            tujuan varchar(255) NOT NULL,
            biaya varchar(115) NOT NULL,
            min varchar(115) NOT NULL,
            PRIMARY KEY  (id)
        );  
        ";
        dbDelta($sql);

    }

    /**
     * autoload method.
     */
    public function autoload() {
        add_action( 'admin_menu', array( $this, 'register_admin_page' ) );

        //ajax
        add_action( 'wp_ajax_tarifdelete', array( $this, 'ajax_delete' ) );
        add_action( 'wp_ajax_gotarif', array( $this, 'ajax_gotarif' ) );

        //shortcode
        add_shortcode( 'cek-tarif', array( $this, 'sh_cektarif' ) );
    }

    /**
     * Register custom page menu.
     */
    public function register_admin_page() {
        add_menu_page(
            'Daftar Tarif',         // Judul menu
            'Daftar Tarif',         // Judul pada sidebar menu
            'manage_options',       // Hak akses yang diperlukan
            'daftar-tarif-page',    // Slug halaman
            array( $this, 'render_admin_page' ),   // Callback untuk rendering konten halaman
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16"> <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/> </svg>'),   // Ikon untuk menu
            2   // Posisi menu dalam sidebar
        );
    }

    /**
     * Render custom page content.
     */
    public function render_admin_page() {        
		require_once(plugin_dir_path(__FILE__).'admin-tarif.php');
    }

    public function getarif($asal,$tujuan){

        if(empty($asal) || empty($tujuan) )
        return false;

        $query = $this->wpdb->prepare(
            "SELECT * FROM $this->table WHERE asal = %s AND tujuan = %s",
            $asal,
            $tujuan
        );            
        $result = $this->wpdb->get_row($query);
        return $result;
    }

    public function ajax_delete(){
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if($id){
            $this->wpdb->delete(
                $this->table,
                array(
                    'id' => $id,
                )
            );
            echo $id;
        }
        wp_die();
    }

    public function ajax_gotarif(){
        $form = isset($_POST['form'])?$_POST['form']:'';

        if(empty($form))
        return false;

        $data = array();
        parse_str($form,$data);
        $asal   = $data['asal'];
        $tujuan = $data['tujuan'];
        $berat  = $data['berat'];
        $berat  = $berat?$berat:0;
        
        if(empty($asal) || empty($tujuan) )
        return false;
        
        $tarif = $this->getarif($asal,$tujuan);
        if(empty($tarif))
        return false;

        ///VOLUME
        $p  = $data['panjang'];
        $l  = $data['lebar'];
        $t  = $data['tinggi'];
        if($p && $l && $t){
            $vol = ($p*$l*$t)/4000;
        } else {
            $vol = 0;
        }

        //MIN
        $min = $tarif->min?$tarif->min:0;

        //MAX
        $max = max($vol, $berat, $min);

        $biaya = $tarif->biaya?$tarif->biaya:0;
        $biaya = $max*$biaya;
        $biaya =  number_format($biaya, 0, ',', '.');

        $html = '<div class="card p-3 mt-3 text-center">';
            $html .= '<div>Ongkos Kirim dari <strong>'.$asal.'</strong> ke <strong>'.$tujuan.'</strong> Berat <strong>'.$berat.'kg</strong></div>';
            $html .= '<div class="fs-3">Rp '.$biaya.'</div>';
        $html .= '</div>';

        echo $html;
        wp_die();
    }

    public function sh_cektarif(){
        ob_start();
        $kota_asal = get_option('_kota_asal',[]);
        $kota_tuju = get_option('_kota_tuju',[]);
        $id = rand(999,9999);
        ?>
        <div class="cek-tarif">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="" method='POST' class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <select name="asal" class="form-select selectcity" required>
                                    <option value="">Pilih Kota</option>
                                    <?php foreach( $kota_asal as $kota): ?>
                                        <option value="<?php echo $kota; ?>"><?php echo $kota; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="asal">Kota Asal</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <select name="tujuan" class="form-select selectcity" required>
                                    <option value="">Pilih Kota</option>
                                    <?php foreach( $kota_tuju as $kota): ?>
                                        <option value="<?php echo $kota; ?>"><?php echo $kota; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="tujuan">Kota Tujuan</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="berat" placeholder="Berat" required>
                                <label for="berat">Berat (kg)</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="panjang" placeholder="Panjang">
                                <label for="panjang">Panjang</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="lebar" placeholder="Lebar">
                                <label for="lebar">Lebar</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="tinggi" placeholder="Tinggi">
                                <label for="tinggi">Tinggi</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-dark py-md-3 w-100">
                                Cek Tarif
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-result-<?php echo $id;?>"></div>
            <script>                
                jQuery(function($){ 
                    $('.cek-tarif form').submit(function(event) {
                        $('.cek-tarif .card-result-<?php echo $id;?>').html('<div class="spinner-border" role="status"> <span class="visually-hidden">Loading...</span> </div>');
                        jQuery.ajax({
                            type: 'POST',
                            url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                            data: ({
                                action : 'gotarif',
                                form: $(this).serialize(),
                            }),
                            success:function(result) {
                                if(result == '0') {
                                    $('.cek-tarif .card-result-<?php echo $id;?>').html('<div class="alert-dismissible alert alert-warning mt-3">Data tidak ditemukan <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                                } else {
                                    $('.cek-tarif .card-result-<?php echo $id;?>').html(result);
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

// Inisialisasi class Saelog_Tarif autoload()
$tarif = new Saelog_Tarif();
$tarif->autoload();