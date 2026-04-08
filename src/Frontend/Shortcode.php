<?php
namespace Expedisi\Frontend;
class Shortcode
{
    public function register()
    {
        add_shortcode('cek_tarif', [$this, 'cek_tarif']);
    }

    public function cek_tarif($atts)
    {
        $atts = shortcode_atts([
            'asal' => '',
            'tujuan' => '',
        ], $atts);

        return 'Cek Tarif';
    }

}
