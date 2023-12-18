<?php
global $wpdb;
$table_name = $wpdb->prefix . "tarif"; 
$asal   = isset($_POST['asal']) ? $_POST['asal'] : '';
$tujuan = isset($_POST['tujuan']) ? $_POST['tujuan'] : '';
$biaya  = isset($_POST['biaya']) ? $_POST['biaya'] : '';
$biaya_volumetrik  = isset($_POST['biaya_volumetrik']) ? $_POST['biaya_volumetrik'] : '';
$min    = isset($_POST['min']) ? $_POST['min'] : '';

if($asal && $tujuan && $biaya) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    echo '<div class="container py-3">';
    if(isset($_POST['act']) && $_POST['act']=='add'){
        $result_check = $wpdb->insert($table_name, 
            array(
                'asal'      => $asal,
                'tujuan'    => $tujuan,
                'biaya'     => $biaya,
                'biaya_volumetrik' => $biaya_volumetrik,
                'min'       => $min,
            )
        );
        echo '<div class="alert alert-success">Data berhasil di tambah</div>';
    } else if(isset($_POST['id'])) {
        $result_check = $wpdb->update($table_name, 
            array(                
                'asal'      => $asal,
                'tujuan'    => $tujuan,
                'biaya'     => $biaya,
                'biaya_volumetrik' => $biaya_volumetrik,
                'min'       => $min,
            ),
            array( 'id'  => $_POST['id'],)
		);
        echo '<div class="alert alert-info">Data berhasil di perbarui</div>';
    }   
    echo '</div>';
}


///ambil data
$details = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>

<div class="ongkir-opt container mt-3 p-0 card border-0 shadow">
    <div class="card-header bg-dark text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16"> <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/> </svg>
        Manajemen Tarif 
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card m-0 p-0 form-tarif">
                    <div class="card-header font-weight-bold">Tambah data</div>
                    <form action="" method="post" class="card-body">
                        <div class="form-floating mb-3">
                            <select class="form-select selectcity" id="asal" name="asal" placeholder="kota asal">
                                <option value="">Pilih Kota Asal</option>
                            </select>
                            <label for="asal">Kota Asal</label>
                        </div>
                        <div class="form-floating mb-3">                        
                            <select class="form-select selectcity" id="tujuan" name="tujuan" placeholder="kota tujuan">
                                <option value="">Pilih Kota tujuan</option>
                            </select>
                            <label for="tujuan">Kota Tujuan</label>    
                        </div>  
                        <div class="form-floating mb-3">  
                            <input required name="biaya" class="form-control" type="number">
                            <label for="biaya">Biaya / kg</label> 
                        </div>
                        <div class="form-floating mb-3">  
                            <input required name="biaya_volumetrik" class="form-control" type="number">
                            <label for="biaya_volumetrik">Biaya Volumetrik / kubik</label> 
                        </div>
                        <div class="form-floating mb-3">  
                            <input required name="min" class="form-control" type="number">
                            <label for="min">Minimal Order (kg)</label> 
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn-batal btn btn-sm btn-secondary">
                                Batal
                            </button>
                            <button class="btn btn-sm btn-success" type="submit">Simpan</button>
                        </div>
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="act" value="add">
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="border mt-1 p-2 p-md-3 rounded">
                    <table class="table table-striped">
                        <thead class="thead-light">
                            <tr><th>Asal</th><th>Tujuan</th><th>Biaya (/kg)</th><th>Biaya Volumetrik</th><th>Min</th><th></th></tr> 
                        </thead>
                        <tbody>
                        <?php foreach($details as $detail):?>
                            <tr class="tr-<?php echo $detail->id;?>" data-tarif='<?php echo json_encode($detail);?>'>
                                <td><?php echo $detail->asal;?></td>
                                <td><?php echo $detail->tujuan;?></td>
                                <td><?php echo $detail->biaya;?></td>
                                <td><?php echo $detail->biaya_volumetrik;?></td>
                                <td><?php echo $detail->min;?></td>
                                <td>
                                    <button data-id="<?php echo $detail->id;?>" class="btn-delete link-danger bg-transparent border-0" >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/> </svg>
                                    </button>
                                    <button class="btn-edit link-info bg-transparent border-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"> <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/> </svg>
                                    </button>	
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>

    </div>


</div>

<script>
    jQuery(function($){
        function loadcity(){
            jQuery.ajax({
                url : '<?php echo VELOCITY_EXPEDISI_PLUGIN_URL; ?>/lib/city.json',
                success:function(dataarray) {
                    localStorage.setItem("data_city", JSON.stringify(dataarray));
                },
            });
        }
        function optioncity(){
            var datacity = localStorage.getItem("data_city");
            if (datacity === null) {
                loadcity();
                datacity = JSON.parse(localStorage.getItem("data_city")); 
            } else { 
                datacity = JSON.parse(datacity); 
            }

            var opt = '<option value="">Pilih Kota</option>';
            datacity.forEach(item => {
                var ct = item.city_name;
                if(item.type=='Kota'){
                    ct += ' '+item.type;
                }
                opt += '<option value="'+ct+'">'+ct+'</option>';
            });
            $('.selectcity').html(opt);

        }
        optioncity();
        $(document).on('click','.btn-delete', function(){
            if (confirm("Hapus data ?") == true) {
                var id = $(this).data('id');
                jQuery.ajax({
                    type: 'POST',
                    url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    data: ({action : 'tarifdelete',id:id}),
                    success:function(result) {
                        if(result){
                            $('tr.tr-'+id).hide();
                        }
                    },
                });
            }
        });

        // modif FORM
        function formerEdit(data,mode){
            if(mode == 'tambah'){
                $('.form-tarif').removeClass('border-info shadow');
                $('.form-tarif').find('.card-header').html('Tambah Data').removeClass('bg-info');
                $('.form-tarif').find('.selectcity').val('');
                $('.form-tarif').find('input[name="biaya"]').val('');
                $('.form-tarif').find('input[name="biaya_volumetrik"]').val('');
                $('.form-tarif').find('input[name="id"]').val('');
                $('.form-tarif').find('input[name="min"]').val('');
                $('.form-tarif').find('input[name="act"]').val('add');
            } else {
                $('.form-tarif').addClass('border-info shadow');
                $('.form-tarif').find('.card-header').html('Edit Data').addClass('bg-info');
                $('.form-tarif').find('.selectcity[name="asal"]').val(data.asal);
                $('.form-tarif').find('.selectcity[name="tujuan"]').val(data.tujuan);
                $('.form-tarif').find('input[name="biaya"]').val(data.biaya);
                $('.form-tarif').find('input[name="biaya_volumetrik"]').val(data.biaya_volumetrik);
                $('.form-tarif').find('input[name="min"]').val(data.min);
                $('.form-tarif').find('input[name="id"]').val(data.id);
                $('.form-tarif').find('input[name="act"]').val('edit');
            }
        }
        $(document).on('click','.btn-edit', function(){
            var data = $(this).closest('tr').data('tarif');
            formerEdit(data,'edit');
        });
        $(document).on('click','.btn-batal', function(){
            formerEdit('','tambah');
        });
    });
</script>