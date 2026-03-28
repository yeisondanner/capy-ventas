<?php
class Pdf extends Controllers
{
    protected string $sessionName;

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
    public function __construct()
    {
        parent::__construct("POS");
        $this->sessionName = config_sesion(1)['name'];
        $this->nameVarBusiness = $this->sessionName . 'business_active';
        $this->nameVarLoginInfo = $this->sessionName . 'login_info';
    }

    /**
     * Obtiene el identificador del negocio activo desde la sesión.
     *
     * @return int
     */
    private function getBusinessId(): int
    {
        if (!isset($_SESSION[$this->nameVarBusiness]['idBusiness'])) {
            $this->responseError('No se encontró el negocio activo en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarBusiness]['idBusiness'];
    }

    /**
     * Obtiene el identificador del usuario POS autenticado.
     *
     * @return int
     */
    private function getUserId(): int
    {
        if (!isset($_SESSION[$this->nameVarLoginInfo]['idUser'])) {
            $this->responseError('No se encontró información del usuario en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarLoginInfo]['idUser'];
    }
}
