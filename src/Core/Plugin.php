<?php
namespace Expedisi\Core;

class Plugin
{
    public function run()
    {
        $this->load_core();
        $this->load_admin();
        $this->load_frontend();
    }

    private function load_core()
    {
        // 
    }
    
    private function load_admin()
    {
        if (!is_admin()) {
            return;
        }
        
        $admin_menu = new \Expedisi\Admin\AdminMenu();
        $admin_menu->register();
    }
    
    private function load_frontend()
    {
        $shortcode = new \Expedisi\Frontend\Shortcode();
        $shortcode->register();
    }
}