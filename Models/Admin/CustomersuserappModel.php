<?php

/**
 * Modelo para la gestión de personas (clientes de la aplicación)
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `people`
 * que almacena información de clientes para acceso a la aplicación.
 */
class CustomersuserappModel extends Mysql
{
    private $idPeople;
    private $names;
    private $lastname;
    private $email;
    private $dateOfBirth;
    private $country;
    private $telephonePrefix;
    private $phoneNumber;
    private $status;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros de personas ordenados por fecha de registro descendente
     * 
     * @return array Lista de todas las personas registradas
     */
    public function select_people(): array
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                people
            ORDER BY
                registration_date DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }

    /**
     * Obtiene una persona por su ID
     * 
     * @param int $idPeople ID de la persona a buscar
     * @return array|false Datos de la persona o false si no existe
     */
    public function select_people_by_id(int $idPeople)
    {
        $this->idPeople = $idPeople;
        $sql = "SELECT * FROM people WHERE idPeople = ?";
        $arrValues = array($this->idPeople);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene una persona por su email para validar duplicados
     * 
     * @param string $email Email encriptado a buscar
     * @return array|false Datos de la persona o false si no existe
     * Nota: El parámetro $email debe estar encriptado antes de llamar a este método
     */
    public function select_people_by_email(string $email)
    {
        $this->email = $email;
        $sql = "SELECT * FROM people WHERE email = ?";
        $arrValues = array($this->email);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene una persona por su número de teléfono para validar duplicados
     * 
     * @param string $phoneNumber Número de teléfono a buscar
     * @return array|false Datos de la persona o false si no existe
     */
    public function select_people_by_phone(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        $sql = "SELECT * FROM people WHERE phone_number = ?";
        $arrValues = array($this->phoneNumber);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo registro de persona en la base de datos
     * 
     * @param string $names Nombres de la persona
     * @param string $lastname Apellidos de la persona
     * @param string $email Correo electrónico
     * @param string $dateOfBirth Fecha de nacimiento (formato: Y-m-d)
     * @param string $country País de residencia
     * @param string $telephonePrefix Prefijo telefónico
     * @param string $phoneNumber Número de teléfono
     * @param string $status Estado (Activo/Inactivo)
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_people($names, $lastname, $email, $dateOfBirth, $country, $telephonePrefix, $phoneNumber)
    {
        $sql = "INSERT INTO `people` (`names`, `lastname`, `email`, `date_of_birth`, `country`, `telephone_prefix`, `phone_number`) VALUES (?,?,?,?,?,?,?);";
        $arrValues = array(
            $this->names = $names,
            $this->lastname = $lastname,
            $this->email = $email,
            $this->dateOfBirth = $dateOfBirth,
            $this->country = $country,
            $this->telephonePrefix = $telephonePrefix,
            $this->phoneNumber = $phoneNumber,
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un registro de persona en la base de datos
     * 
     * @param int $idPeople ID de la persona a actualizar
     * @param string $names Nombres de la persona
     * @param string $lastname Apellidos de la persona
     * @param string $email Correo electrónico
     * @param string $dateOfBirth Fecha de nacimiento (formato: Y-m-d)
     * @param string $country País de residencia
     * @param string $telephonePrefix Prefijo telefónico
     * @param string $phoneNumber Número de teléfono
     * @param string $status Estado (Activo/Inactivo)
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_people($idPeople, $names, $lastname, $email, $dateOfBirth, $country, $telephonePrefix, $phoneNumber, $status)
    {
        $this->idPeople = $idPeople;
        $this->names = $names;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->dateOfBirth = $dateOfBirth;
        $this->country = $country;
        $this->telephonePrefix = $telephonePrefix;
        $this->phoneNumber = $phoneNumber;
        $this->status = $status;

        $sql = "UPDATE `people` SET `names`=?, `lastname`=?, `email`=?, `date_of_birth`=?, `country`=?, `telephone_prefix`=?, `phone_number`=?, `status`=? WHERE idPeople=?";
        $arrValues = array(
            $this->names,
            $this->lastname,
            $this->email,
            $this->dateOfBirth,
            $this->country,
            $this->telephonePrefix,
            $this->phoneNumber,
            $this->status,
            $this->idPeople
        );

        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un registro de persona de la base de datos
     * 
     * @param int $idPeople ID de la persona a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_people($idPeople)
    {
        $this->idPeople = $idPeople;
        $sql = "DELETE FROM `people` WHERE idPeople = ?";
        $arrValues = array($this->idPeople);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene el usuario de la aplicación por ID de persona
     * 
     * @param int $peopleId ID de la persona
     * @return array|false Datos del usuario o false si no existe
     */
    public function select_user_app_by_people_id(int $peopleId)
    {
        $sql = "SELECT * FROM user_app WHERE people_id = ?";
        $arrValues = array($peopleId);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene el usuario de la aplicación por nombre de usuario para validar duplicados
     * 
     * @param string $user Nombre de usuario a buscar
     * @return array|false Datos del usuario o false si no existe
     */
    public function select_user_app_by_user(string $user)
    {
        $sql = "SELECT * FROM user_app WHERE user = ?";
        $arrValues = array($user);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo usuario de aplicación en la base de datos
     * 
     * @param string $user Nombre de usuario
     * @param string $password Contraseña encriptada
     * @param string $status Estado (Activo/Inactivo)
     * @param int $peopleId ID de la persona asociada
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_user_app($user, $password, $peopleId)
    {
        $sql = "INSERT INTO `user_app` (`user`, `password`, `people_id`) VALUES (?,?,?);";
        $arrValues = array(
            $user,
            $password,
            $peopleId
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un usuario de aplicación en la base de datos
     * 
     * @param int $idUserApp ID del usuario a actualizar
     * @param string $user Nombre de usuario
     * @param string $password Contraseña encriptada (puede ser null si no se actualiza)
     * @param string $status Estado (Activo/Inactivo)
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_user_app($idUserApp, $user, $password, $status)
    {
        if ($password !== null && $password !== "") {
            $sql = "UPDATE `user_app` SET `user`=?, `password`=?, `status`=? WHERE idUserApp=?";
            $arrValues = array($user, $password, $status, $idUserApp);
        } else {
            $sql = "UPDATE `user_app` SET `user`=?, `status`=? WHERE idUserApp=?";
            $arrValues = array($user, $status, $idUserApp);
        }
        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un usuario de aplicación de la base de datos
     * 
     * @param int $idUserApp ID del usuario a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_user_app($idUserApp)
    {
        $sql = "DELETE FROM `user_app` WHERE idUserApp = ?";
        $arrValues = array($idUserApp);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene todas las personas con sus usuarios asociados
     * 
     * @return array Lista de personas con información de usuario
     */
    public function select_people_with_users(): array
    {
        $query = <<<SQL
            SELECT
                p.*,
                ua.idUserApp,
                ua.user,
                ua.status as user_status
            FROM
                people AS p
            LEFT JOIN
                user_app AS ua ON ua.people_id = p.idPeople
            ORDER BY
                p.registration_date DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }
}
