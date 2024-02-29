<?php
/**
 * Template Name: Resi Print
 *
*/

$resi       = isset($_GET['resi']) ? $_GET['resi'] : '';
$download   = isset($_GET['download']) ? $_GET['download'] : 0;

if ( empty($resi) )
return false;

$args = array("post_type" => "data_resi", "s" => $resi);
$query = get_posts( $args );

if(empty($query))
return false;

//RESI
$dresi = new Saelog_Resi;
$dataresi = $dresi->data_resi($query[0]->ID); 
foreach ($dataresi as $key => $value) {            
    ${$key} = $value;
}

///Barcode
require 'vendor/autoload.php';
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
$barcode = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($resi, $generator::TYPE_CODE_128)) . '">';

//start html
$html   = '';
$html   .= '<html>';
$html   .= '<body>';
$html   .= '<div class="page">';

$html   .= '<div style="border: 1px solid #000000;padding: 20px;">';
    $html   .= '<table border="0" style="width: 100%;">';
    
        ///header
        $html   .= '<tr>';
            $html   .= '<td>';
            $html   .= '<img style="width:100px;" src="'.esc_url( wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) ).'" >';
            $html   .= '</td>';
            $html   .= '<td style="text-align:right;font-size:11px;">Tanggal : '.$tanggal.'</td>';
        $html   .= '</tr>';

        ///header barcode
        $html   .= '<tr><td colspan="2"><hr></td></tr>';
        $html   .= '<tr>';
            $html   .= '<td>';
                $html   .= '<div style="font-size:11px;">'.$resi.'</div>';
                $html   .= '<div class="barcode">'.$barcode.'</div>';
                $html   .= '<div style="font-size:11px;">'.$kota_pengirim.','.$kota_penerima.'</div>';
            $html   .= '</td>';
            $html   .= '<td style="text-align:right;">';
                $html   .= '<span>'.$jenis_packing.'</span>';
            $html   .= '</td>';
        $html   .= '</tr>';

        ///Body
        $html   .= '<tr><td colspan="2"><hr></td></tr>';
        $html   .= '<tr style="font-size: 12px;line-height:18px;vertical-align: top;">';
            $html   .= '<td>';
                //Pengirim
                $html   .= '<div><strong>Pengirim :</strong></div>';
                $html   .= '<div>Nama Pengirim : '.$nama_pengirim.'</div>';
                $html   .= '<div>No. Hp : '.$nohp_pengirim.'</div>';
                $html   .= '<br>';
                //Penerima
                $html   .= '<div><strong>Pengirim :</strong></div>';
                $html   .= '<div>Nama Pengirim : '.$nama_penerima.'</div>';
                $html   .= '<div>No. Hp : '.$nohp_penerima.'</div>';
            $html   .= '</td>';
            $html   .= '<td>';
                //Barang
                $html   .= '<div><strong>Barang :</strong></div>';
                $html   .= '<div>Jumlah : '.$jumlah_barang.'</div>';
                $html   .= '<div>Berat/Volume : '.$berat_barang.'</div>';
                $html   .= '<div>Jenis : '.$jenis_barang.'</div>';
            $html   .= '</td>';
        $html   .= '</tr>';

        ///footer
        $html   .= '<tr><td colspan="2"><hr></td></tr>';
        $html   .= '<tr>';
            $html   .= '<td style="font-size:10px;" colspan="2">';
            $html   .= 'Dengan menyerahkan kiriman, Anda setuju syarat & ketentuan yang tertera';
            $html   .= '</td>';
        $html   .= '</tr>';

    $html   .= '</table>';

$html   .= '</div>';


///add style
$html .= '
<style>
    .page {
        width: 100mm !important;
        min-height: 100mm !important;
        border-bottom: 1 dashed #dddddd;
        padding: 4mm 4mm 1mm !important;
    }
    .barcode img {
        width: 150px;
    }
    @page { margin: 0px; }
</style>';

//end html
$html .= '</div>';
$html .= '</body>';
$html .= '</html>';


// reference the Dompdf namespace
// require_once VELOCITY_EXPEDISI_DIR_PATH('inc/lib/dompdf/vendor/autoload.php');
// require_once(VELOCITY_EXPEDISI_DIR_PATH.'lib/dompdf/vendor/autoload.php');
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('enable_remote', true);

// instantiate and use the dompdf class
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A6', 'potrait');
// $dompdf->setPaper(array(0, 0, 300, 300), 'potrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Resi " . $resi, array("Attachment" => $download));