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
         * Renderiza la página de inicio pública incluyendo los planes activos.
         *
         * @return void
         */
        public function home()
        {
                $data['page_id'] = 0;
                $data['page_title'] = "Principal";
                $data['page_description'] = "home";
                $data['page_js_css'] = "home";
                $data['plans'] = $this->model->getActivePlans();
                $data['minimum_plan_price'] = $this->resolveMinimumPlanPrice($data['plans']);
                $this->views->getView($this, "home", $data);
        }

        /**
         * Determina el precio mensual mínimo entre los planes activos obtenidos.
         *
         * @param array<int, array<string, mixed>> $plans Conjunto de planes activos obtenidos desde la base de datos.
         *
         * @return float|null Precio mínimo encontrado o null si no existen montos registrados.
         */
        private function resolveMinimumPlanPrice(array $plans): ?float
        {
                $prices = [];

                foreach ($plans as $plan) {
                        if (isset($plan['base_price'])) {
                                $prices[] = (float) $plan['base_price'];
                        }
                }

                if (empty($prices)) {
                        return null;
                }

                return min($prices);
        }
}
