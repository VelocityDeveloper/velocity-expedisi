<?php
$edit   = isset($_POST['edit']) ? $_POST['edit'] : '';
$tarif_volume = isset($_POST['tarif_volume']) ? $_POST['tarif_volume'] : get_option('tarif_volume');
if($edit == 'yes' && $_POST['tarif_volume'] == 'on'){
    update_option('tarif_volume',$tarif_volume);
} elseif($edit == 'yes'){
    $tarif_volume = 'off';
    update_option('tarif_volume',$tarif_volume);
}
$checked = $tarif_volume == 'on'?'checked':'';
?>

<div class="ongkir-opt container mt-3 p-0 card border-0 shadow">
    <div class="card-header bg-dark text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16"> <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/> </svg>
        Pengaturan Tarif 
    </div>

    <div class="card-body">
        <form action="" method="post">
            <input name="edit" type="hidden" value="yes">
            <table class="form-table" role="presentation">
				<tbody>
                    <tr>
						<th scope="row">Tarif Volume</th>
						<td>
							<label for="tarif_volume">
                                <input name="tarif_volume" type="checkbox" id="tarif_volume" <?php echo $checked;?>>Aktifkan tarif volume
                            </label>
						</td>
				    </tr>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        </form>
    </div>

</div>