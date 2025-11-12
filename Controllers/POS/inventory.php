<?php

class inventory extends Controllers
{
    public function __construct()
    {
        isSession(1);
        parent::__construct("POS");
    }

    public function inventory()
    {
        $data['page_id'] = 0;
        $data['page_title'] = "Dashboard de la app";
        $data['page_description'] = "Dashboard";
        $data['page_container'] = "Inventory";
        $data['page_view'] = 'inventory';
        $data['page_js_css'] = "inventory";
        $this->views->getView($this, "inventory", $data, "POS");
    }
}
