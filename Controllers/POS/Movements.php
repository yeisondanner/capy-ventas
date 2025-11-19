<?php

class Movements extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión que contiene la información del usuario POS.DDD
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    /**
     * Clave normalizada de la categoría protegida por defecto.
     *
     * @var string|null
     */
    private ?string $protectedCategoryKey = null;

    public function __construct()
    {
        isSession(1);
        parent::__construct("POS");

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    public function movements()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'Inventario de productos',
            'page_description' => 'Gestiona los productos disponibles en tu negocio.',
            'page_container'   => 'Movements',
            'page_view'        => 'movements',
            'page_js_css'      => 'movements',
        ];
        $this->views->getView($this, "movements", $data, "POS");
    }

    public function getMovements()
    {
       
        $arrData = $this->model->select_movements();
        $cont = 1; //Contador para la tabla
        foreach ($arrData as $key => $value) {
            $arrData[$key]["cont"] = $cont;
            //agregamos un badge para el estado
            // if ($value["status"] == 'Activo') {
            //     $arrData[$key]["status_badge"] = '<span class="badge badge-success"> <i class="fa fa-check"></i> Activo</span>';
            // } else {
            //     $arrData[$key]["status_badge"] = '<span class="badge badge-danger"> <i class="fa fa-close"></i> Inactivo</span>';
            // }
            // $arrData[$key]["actions"] = '
            // <div class="btn-group">
            //     <button class="btn btn-success update-item" title="Editar registro" 
            //     data-id="' . $value["idCategory"] . '" 
            //     data-name="' . $value["name"] . '" 
            //     data-status="' . $value['status'] . '"  
            //     data-description="' . $value["description"] . '" 
            //     type="button"><i class="fa fa-pencil"></i></button>
            //     <button class="btn btn-info report-item" title="Ver reporte" 
            //     data-id="' . $value["idCategory"] . '"
            //     data-name="' . $value["name"] . '" 
            //     data-status="' . $value['status'] . '"  
            //     data-description="' . $value["description"] . '" 
            //     data-registrationDate="' . dateFormat($value['dateRegistration']) . '" 
            //     data-updateDate="' . dateFormat($value['dateUpdate']) . '" 
            //     type="button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></button>
            //     <button class="btn btn-danger delete-item" title ="Eliminar registro" data-id="' . $value["idCategory"] . '" data-name="' . $value["name"] . '" ><i class="fa fa-remove"></i></button>
            //     <a href="' . base_url() . '/pdf/category/' . encryption($value["idCategory"]) . '" target="_Blank" class="btn btn-warning"><i class="fa fa-print  text-white"></i></a>
            //     </div>
            //      ';

            $cont++;
        }
        toJson($arrData);
    }

}
