<?php

class Home extends Controllers
{
        /**
         * Construye el controlador y garantiza la carga del modelo correspondiente.
         */
        public function __construct()
        {
                parent::__construct("");
        }

        /**
         * Renderiza la página de inicio pública utilizando contenido estático.
         *
         * @return void
         */
        public function home()
        {
                $data['page_id'] = 0;
                $data['page_title'] = "Principal";
                $data['page_description'] = "home";
                $data['page_js_css'] = "home";
                $this->views->getView($this, "home", $data);
        }
}
