<?php

class SystemModel extends Mysql
{
    private int $idConfiguration;
    private string $name;
    private string $description;
    private string $logo;
    private string $ColorPrimary;
    private string $ColorSecondary;
    private string $nameInstitution;
    private string $ruc;
    private string $address;
    private string $phone;
    private string $mail;
    private int $durationLock;
    private int $typeLoader;
    private string $loader;
    private string $txtLoader;
    private string $txtUserApi;
    private string $txtPasswordApi;
    private string $txtKeyApi;
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpEncryption;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $fromEmail;
    private string $fromName;

    /**
     * Constructor de la clase
     */

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Funcion que inserta el registro en la tabla de la base de datos
     * @param string $nombreSistema
     * @param string $descripcion
     * @param string $logo
     * @param string $colorPrimary
     * @param string $colorSecondary
     * @param string $nameInstitution
     * 6
     * @return void
     */
    public function insert_info_system(
        string $nombreSistema,
        string $descripcion,
        string $logo,
        string $ColorPrimary,
        string $ColorSecondary,
        string $nameInstitution,
        string $ruc,
        string $address,
        string $phone,
        string $mail,
        int $durationLock,
        int $typeLoader,
        string $loader,
        string $txtLoader,
        string $txtUserAPi,
        string $txtPasswordApi,
        string $txtKeyApi,
        string $smtpHost,
        int $smtpPort,
        string $smtpEncryption,
        string $smtpUsername,
        string $smtpPassword,
        string $fromEmail,
        string $fromName,
    ) {
        $this->name = $nombreSistema;
        $this->description = $descripcion;
        $this->logo = $logo;
        $this->ColorPrimary = $ColorPrimary;
        $this->ColorSecondary = $ColorSecondary;
        $this->nameInstitution = $nameInstitution;
        $this->ruc = $ruc;
        $this->address = $address;
        $this->phone = $phone;
        $this->mail = $mail;
        $this->durationLock = $durationLock;
        $this->typeLoader = $typeLoader;
        $this->loader = $loader;
        $this->txtLoader = $txtLoader;
        $this->txtUserApi = $txtUserAPi;
        $this->txtPasswordApi = $txtPasswordApi;
        $this->txtKeyApi = $txtKeyApi;
        //recogemos las variables del correo
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
        $this->smtpEncryption = $smtpEncryption;
        $this->smtpUsername = $smtpUsername;
        $this->smtpPassword = $smtpPassword;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $sql = "INSERT INTO `tb_configuration` (`c_name`, `c_logo`, `c_description`, `c_color_primary`, `c_color_secondary`, `c_company_name`, `c_ruc`, `c_address`, `c_phone`, `c_mail`,`c_duration_lock`,`c_typeLoader`,`c_contentLoader`,`c_textLoader`,`c_user_api_reniec_sunat`,`c_password_api_reniec_sunat`,`c_key_api_reniec_sunat`,
                                               `c_email_server_smtp`,
                                                `c_email_port`,
                                                `c_email_encryption`,
                                                `c_email_user_smtp`,
                                                `c_email_password_smtp`,
                                                `c_email_sender`,
                                                `c_email_sender_name` ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
        $arrValues = array(
            $this->name,
            $this->logo,
            $this->description,
            $this->ColorPrimary,
            $this->ColorSecondary,
            $this->nameInstitution,
            $this->ruc,
            $this->address,
            $this->phone,
            $this->mail,
            $this->durationLock,
            $this->typeLoader,
            $this->loader,
            $this->txtUserApi,
            $this->txtPasswordApi,
            $this->txtKeyApi,
            //pasamos las varaibles del correo
            $this->smtpHost,
            $this->smtpPort,
            $this->smtpEncryption,
            $this->smtpUsername,
            $this->smtpPassword,
            $this->fromEmail,
            $this->fromName,
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }
    /**
     * Funcion que devuelve los registros de la tabla de configuracion
     * @return void
     */
    public function selects_info_system()
    {
        $sql = "SELECT * FROM tb_configuration";
        $request = $this->select_all($sql);
        return $request;
    }
    /**
     * Funcion que devuelve un registro de la tabla de configuracion
     * @return void
     */
    public function select_info_system()
    {
        $sql = "SELECT * FROM tb_configuration";
        $request = $this->select($sql);
        return $request;
    }
    /**
     * Funcion que trunca la tabla de configuracion
     * @return bool
     */
    public function truncate_info_system()
    {
        $sql = "TRUNCATE TABLE tb_configuration";
        $request = $this->delete($sql, []);
        return $request;
    }
    /**
     * Funcion que actualiza los registros de la tabla de configuracion
     * @param int $idConfiguration
     * @param string $nombreSistema
     * @param string $descripcion
     * @param string $logo
     * @param string $ColorPrimary
     * @param string $ColorSecondary
     * @param string $nameInstitution
     * @param string $ruc
     * @param string $address
     * @param string $phone
     * @param string $mail
     * @return bool
     */
    public function update_info_system(
        int $idConfiguration,
        string
        $nombreSistema,
        string $descripcion,
        string $logo,
        string $ColorPrimary,
        string $ColorSecondary,
        string $nameInstitution,
        string $ruc,
        string $address,
        string $phone,
        string $mail,
        int $durationLock,
        int $typeLoader,
        string $loader,
        string $txtLoader,
        string $txtUserAPi,
        string $txtPasswordApi,
        string $txtKeyApi,
        string $smtpHost,
        int $smtpPort,
        string $smtpEncryption,
        string $smtpUsername,
        string $smtpPassword,
        string $fromEmail,
        string $fromName,
    ) {
        $this->name = $nombreSistema;
        $this->description = $descripcion;
        $this->logo = $logo;
        $this->idConfiguration = $idConfiguration;
        $this->ColorPrimary = $ColorPrimary;
        $this->ColorSecondary = $ColorSecondary;
        $this->nameInstitution = $nameInstitution;
        $this->ruc = $ruc;
        $this->address = $address;
        $this->phone = $phone;
        $this->mail = $mail;
        $this->durationLock = $durationLock;
        $this->typeLoader = $typeLoader;
        $this->loader = $loader;
        $this->txtLoader = $txtLoader;
        $this->txtUserApi = $txtUserAPi;
        $this->txtPasswordApi = $txtPasswordApi;
        $this->txtKeyApi = $txtKeyApi;
        //recogemos las variables del correo
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
        $this->smtpEncryption = $smtpEncryption;
        $this->smtpUsername = $smtpUsername;
        $this->smtpPassword = $smtpPassword;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $sql = "UPDATE `tb_configuration` 
                        SET 
                        `c_name`=?, 
                        `c_logo`=?, 
                        `c_description`=?, 
                        `c_color_primary`=?, 
                        `c_color_secondary`=?, 
                        `c_company_name`=?, 
                        `c_ruc`=?, 
                        `c_address`=?, 
                        `c_phone`=?, 
                        `c_mail`=?,
                        `c_duration_lock`=?,
                        `c_typeLoader`=?,
                        `c_contentLoader`=?,
                        `c_textLoader`=?,
                        `c_user_api_reniec_sunat`=?,
                        `c_password_api_reniec_sunat`=?,
                        `c_key_api_reniec_sunat`=?,
                        `c_email_server_smtp`=?,
                        `c_email_port`=?,
                        `c_email_encryption`=?,
                        `c_email_user_smtp`=?,
                        `c_email_password_smtp`=?,
                        `c_email_sender`=?,
                        `c_email_sender_name`=?
                        WHERE  
                        `idConfiguration`=?;";
        $arrValues = array(
            $this->name,
            $this->logo,
            $this->description,
            $this->ColorPrimary,
            $this->ColorSecondary,
            $this->nameInstitution,
            $this->ruc,
            $this->address,
            $this->phone,
            $this->mail,
            $this->durationLock,
            $this->typeLoader,
            $this->loader,
            $this->txtLoader,
            $this->txtUserApi,
            $this->txtPasswordApi,
            $this->txtKeyApi,
            //pasamos las varaibles del correo
            $this->smtpHost,
            $this->smtpPort,
            $this->smtpEncryption,
            $this->smtpUsername,
            $this->smtpPassword,
            $this->fromEmail,
            $this->fromName,
            $this->idConfiguration,
        );
        $request = $this->update($sql, $arrValues);
        return $request;
    }
}