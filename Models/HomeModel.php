<?php
class HomeModel extends Mysql
{
        /**
         * Inicializa el modelo base y establece la conexión con la base de datos.
         */
        public function __construct()
        {
                parent::__construct();
        }

        /**
         * Recupera los planes activos disponibles para su visualización pública.
         *
         * @return array<int, array<string, mixed>> Listado de planes ordenados por precio base y nombre.
         */
        public function getActivePlans(): array
        {
                $sql = "SELECT idPlan, name, description, base_price, billing_period, is_active FROM plans WHERE is_active = 1 ORDER BY base_price ASC, name ASC";

                return $this->select_all($sql);
        }
}
