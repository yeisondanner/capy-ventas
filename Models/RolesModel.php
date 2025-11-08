<?php

class RolesModel extends Mysql
{
    private int $idRole;
    private int $idInterface;
    private string $name;
    private string $description; // Permitir que la descripción pueda ser NULL
    private int $idUser;
    private string $status;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene la lista de todos los roles registrados en la base de datos.
     * @return array Lista de roles
     */

    public function select_roles(): array
    {
        $sql = "SELECT * FROM tb_role;";
        $request = $this->select_all($sql, []);
        return $request;
    }
    /**
     * Inserta un nuevo rol en la base de datos.
     * @param string $name Nombre del rol
     * @param string|null $description Descripción del rol (opcional)
     * @return int ID del rol insertado
     */
    public function insert_role($name, $description = null): int
    {
        $sql = "INSERT INTO `tb_role` (`r_name`, `r_description`) VALUES (?, ?)";
        $arrValues = array(
            $this->name = $name,
            $this->description = $description
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Funcion que eliminar un registro de la base de datos
     * @param mixed $idRole
     * @return bool
     */
    public function delete_role($idRole)
    {
        $this->idRole = $idRole;
        $sql = "DELETE FROM `tb_role` WHERE idRole = ?";
        $arrValues = array($this->idRole);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }
    /**
     * Funcion que devuelve el registro deun rol por el id
     * @param int $idRole
     * @return array
     */
    public function select_rol_by_id(int $idRole)
    {
        $this->idRole = $idRole;
        $sql = "SELECT*FROM `tb_role` WHERE idRole=?";
        $arrValues = array($this->idRole);
        $request = $this->select($sql, $arrValues);
        return $request;
    }
    /**
     * Summary of update_role
     * @param int $idRole
     * @param string $name
     * @param string $description
     * @return bool
     */
    public function update_role(int $idRole, string $name, string $description, string $status)
    {
        $this->idRole = $idRole;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $sql = "UPDATE `tb_role` SET `r_name`=?,`r_description`=?,`r_status`=? WHERE idRole=?";
        $arrValues = array($this->name, $this->description, $this->status, $this->idRole);
        $request = $this->update($sql, $arrValues);
        return $request;
    }
    /**
     * * Funcion que obtiene los modulos e interfaces por usuario
     * @param int $idUser
     * @return array
     */
    public function select_module_iterface_by_user(int $idUser)
    {
        $this->idUser = $idUser;
        $sqlGetModule = "SELECT tbm.idModule,tbm.m_name,tbm.m_icon   FROM tb_user AS tbu 
                            INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id
                            INNER JOIN tb_userroledetail AS tburd ON tburd.role_id=tbu.role_id
                            INNER JOIN tb_interface AS tbi ON tbi.idInterface=tburd.interface_id
                            INNER JOIN tb_module AS tbm ON tbm.idModule=tbi.module_id
                            WHERE tburd.urd_status='Activo' AND tbu.idUser=? AND tbi.i_isListNav=1
                            GROUP BY tbm.m_name;";
        $arrValues = array($this->idUser);
        $requestModule = $this->select_all($sqlGetModule, $arrValues);
        foreach ($requestModule as $key => $value) {
            $sqlgetInterface = "SELECT tbi.* FROM tb_user AS tbu 
                                    INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id
                                    INNER JOIN tb_userroledetail AS tburd ON tburd.role_id=tbu.role_id
                                    INNER JOIN tb_interface AS tbi ON tbi.idInterface=tburd.interface_id
                                    WHERE tburd.urd_status='Activo'  AND tbu.idUser=? 
                                    AND tbi.module_id=? AND tbi.i_isPublic=0;";
            $arrValues = array($this->idUser, $value['idModule']);
            $requestInterface = $this->select_all($sqlgetInterface, $arrValues);
            $requestModule[$key]['interface'] = $requestInterface;
        }
        return $requestModule;
    }
    /**
     * seleccionamos los modulos e interfaces que estan disponibles para el modulo que estemos viendo su reporte detallado
     * @param int $idRol
     * @return array
     */
    public function select_permissions_by_role(int $idRol)
    {
        $this->idRole = $idRol;
        $sql = "SELECT tbm.idModule,tbm.m_name,tbm.m_icon,tbm.m_description,tbm.m_status FROM tb_userroledetail AS tburd
                    INNER JOIN tb_interface AS tbi ON tbi.idInterface=tburd.interface_id
                    INNER JOIN tb_module AS tbm ON tbm.idModule=tbi.module_id
                    WHERE tburd.role_id=?
                    GROUP BY tbm.idModule;";
        $arrValues = array($this->idRole);
        $request = $this->select_all($sql, $arrValues);
        foreach ($request as $key => $value) {
            $sqlInterface = "SELECT  * FROM `tb_interface` AS tbi
                                INNER JOIN tb_userroledetail AS tburd ON tburd.interface_id=tbi.idInterface
                                WHERE tbi.module_id=? AND tburd.role_id=?;";
            $arrValues = array($value['idModule'], $this->idRole);
            $requestInterface = $this->select_all($sqlInterface, $arrValues);
            $request[$key]['interface'] = $requestInterface;
        }
        return $request;
    }
    /**
     * Seleccciona todos los modulos e interfaces
     * @param int $idRole
     * @return array
     */
    public function selects_all_modules_and_interface(int $idRole)
    {
        $this->idRole = $idRole;
        $sqlModule = "SELECT tbm.idModule,tbm.m_name,tbm.m_icon FROM tb_module AS tbm WHERE tbm.m_status='Activo';";
        $request = $this->select_all($sqlModule);
        foreach ($request as $key => $value) {
            $sqlInterface = "SELECT * FROM tb_interface AS tbi WHERE tbi.i_status='Activo' AND tbi.module_id=?;";
            $arrValues = array($value['idModule']);
            $requestInterface = $this->select_all($sqlInterface, $arrValues);
            foreach ($requestInterface as $key2 => $value2) {
                $sqlTbUserRoleDetail = "SELECT*FROM tb_userroledetail WHERE role_id=? AND interface_id=?;";
                $arrValuesDetail = array($this->idRole, $value2['idInterface']);
                $resultDetail = $this->select($sqlTbUserRoleDetail, $arrValuesDetail);
                if ($resultDetail) {
                    $requestInterface[$key2]['detail'] = $resultDetail;
                }
            }
            $request[$key]['interface'] = $requestInterface;
        }
        return $request;
    }
    /**
     * Selccionamos la informacion del rol del detalle
     * @param int $idInterface
     * @param int $idRole
     */
    public function select_userroldetail(int $idInterface, int $idRole)
    {
        $this->idRole = $idRole;
        $this->idInterface = $idInterface;
        $arrValues = array(
            $this->idInterface,
            $this->idRole
        );
        $sql = "SELECT*FROM tb_userroledetail AS tburd WHERE tburd.interface_id=? AND tburd.role_id=?;";
        $result = $this->select($sql, $arrValues);
        return $result;
    }
    /**
     * Funcion que permite actualizar la informacion de actualizar el permiso
     * @param int $idInterface
     * @param int $idRole
     * @param string $status
     * @return bool
     */
    public function update_status_interface_role_detail(int $idInterface, int $idRole, string $status)
    {
        $this->idRole = $idRole;
        $this->idInterface = $idInterface;
        $this->status = $status;
        $sql = "UPDATE tb_userroledetail SET urd_status=? WHERE interface_id=? AND role_id=?";
        $arrValues = array($this->status, $this->idInterface, $this->idRole);
        $result = $this->update($sql, $arrValues);
        return $result;
    }
    /**
     * Summary of insert_new_permission_role_detail
     * @param int $idInterface
     * @param int $idRole
     * @param string $status
     * @return bool|int|string
     */
    public function insert_new_permission_role_detail(int $idInterface, int $idRole, string $status)
    {
        $this->idInterface = $idInterface;
        $this->idRole = $idRole;
        $this->status = $status;
        $sql = "INSERT INTO tb_userroledetail(interface_id,role_id,urd_status) VALUES (?,?,?)";
        $arrValues = array($this->idInterface, $this->idRole, $this->status);
        $request = $this->insert($sql, $arrValues);
        return $request;
    }
    /**
     * Funcion que selecciona los modulos e interfaces que no estan en el menu de navegacion
     * @param int $idUser
     * @return array
     */
    public function select_module_iterface_by_user_is_not_list_nav(int $idUser)
    {
        $this->idUser = $idUser;
        $slq = "SELECT tbm.idModule,tbm.m_name,tbm.m_icon   FROM tb_user AS tbu 
                            INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id
                            INNER JOIN tb_userroledetail AS tburd ON tburd.role_id=tbu.role_id
                            INNER JOIN tb_interface AS tbi ON tbi.idInterface=tburd.interface_id
                            INNER JOIN tb_module AS tbm ON tbm.idModule=tbi.module_id
                            WHERE tburd.urd_status='Activo' AND tbu.idUser=? AND tbi.i_isListNav=0
                            GROUP BY tbm.m_name;";
        $arrValues = array($this->idUser);
        $request = $this->select_all($slq, $arrValues);
        return $request;
    }
    /**
     * Esta funcion selecciona los modulos e interfaces todas con todo
     * @param int $idUser
     * @return array
     */
    public function select_module_iterface_by_user_all(int $idUser)
    {
        $this->idUser = $idUser;
        $sqlGetModule = "SELECT tbm.idModule,tbm.m_name,tbm.m_icon   FROM tb_user AS tbu 
                            INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id
                            INNER JOIN tb_userroledetail AS tburd ON tburd.role_id=tbu.role_id
                            INNER JOIN tb_interface AS tbi ON tbi.idInterface=tburd.interface_id
                            INNER JOIN tb_module AS tbm ON tbm.idModule=tbi.module_id
                            WHERE tburd.urd_status='Activo' AND tbu.idUser=? 
                            GROUP BY tbm.m_name;";
        $arrValues = array($this->idUser);
        $requestModule = $this->select_all($sqlGetModule, $arrValues);
        foreach ($requestModule as $key => $value) {
            $sqlgetInterface = "SELECT tbi.* FROM tb_user AS tbu 
                                    INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id
                                    INNER JOIN tb_userroledetail AS tburd ON tburd.role_id=tbu.role_id
                                    INNER JOIN tb_interface AS tbi ON tbi.idInterface=tburd.interface_id
                                    WHERE tburd.urd_status='Activo'  AND tbu.idUser=? 
                                    AND tbi.module_id=?;";
            $arrValues = array($this->idUser, $value['idModule']);
            $requestInterface = $this->select_all($sqlgetInterface, $arrValues);
            $requestModule[$key]['interface'] = $requestInterface;
        }
        return $requestModule;
    }
    /**
     * Metodo que valida si el registro del rol depende de algun registro en especifico que no permita
     * eliminarse
     * @param int $id
     * @return void
     */
    public function validation_dependenci_tables(int $id): void
    {
        $this->idRole = $id;
        $arrSql = [
            "SELECT*FROM tb_user AS tu WHERE tu.role_id=?;"
        ];
        foreach ($arrSql as $value) {
            $result = $this->select_all($value, [$this->idRole]);
            if ($result) {
                registerLog("Ocurrió un error inesperado", "Registro asociado: Actualmente el registro cuenta asociado a una tabla cuando se intento hacer la consulta : " . $value, 1, $_SESSION['login_info']['idUser']);

                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El rol cuenta con registros vinculados, por lo que no se podra eliminar hasta liberar este registro",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
    }
}