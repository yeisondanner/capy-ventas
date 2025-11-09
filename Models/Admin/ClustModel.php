<?php
class ClustModel extends Mysql
{
    private int $iduser;
    private int $idfolder;
    private int $idfile;
    private string $name;
    private int $idfather;
    private int $folder_id;
    private string $extension;
    private int $size;
    private string $path;
    private int $favorites;

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Metodo que obtiene un archivo por su identificador y usuario asociado.
     *
     * @param int $idFile Identificador del archivo.
     * @param int $idUser Identificador del usuario propietario.
     * @return array Arreglo con la información del archivo o vacío si no existe.
     */
    public function select_file_by_id_and_user(int $idFile, int $idUser)
    {
        $this->idfile = $idFile;
        $this->iduser = $idUser;
        $sql = "SELECT * FROM tb_file AS tbf WHERE tbf.idFile = ? AND tbf.user_id = ?;";
        $params = [$this->idfile, $this->iduser];
        $request = $this->select($sql, $params);
        return $request ?? [];
    }
    /**
     * Este metodo obtiene la carpeta raíz del usuario
     * @param int $iduser
     * @param string $name
     * @return array
     */
    public function select_folder_root(int $iduser, string $name = 'root')
    {
        $query = "SELECT*FROM tb_folder WHERE user_id = ? AND f_name = ?;";
        $this->iduser = $iduser;
        $this->name = $name;
        $params = [$this->iduser, $this->name];
        $request = $this->select($query, $params);
        return $request;
    }
    /**
     * Metodo que se encarga de buscar el folder de un usuario
     * @param  int  $idUser
     * @param int  $idFolder
     * @return array
     */
    public function select_folder_of_user_for_ids(int $idFolder, int $idUser = 0)
    {
        $this->iduser = $idUser;
        $this->idfolder = $idFolder;
        $arrdata = array(
            $this->iduser,
            $this->idfolder
        );
        if ($this->iduser === 0) {
            $arrdata = array(
                $this->idfolder
            );
            $sql = "SELECT*FROM tb_folder AS tbf WHERE  tbf.idFolder=?;";
        } else {
            $arrdata = array(
                $this->iduser,
                $this->idfolder
            );
            $sql = "SELECT*FROM tb_folder AS tbf WHERE tbf.user_id=? AND tbf.idFolder=?;";
        }
        $request = $this->select($sql, $arrdata);
        return $request ?? [];
    }
    /**
     * Metodo que obtiene las carpetas hijas de una carpeta
     * @param int $idFather
     * @return array
     */
    public function select_folders(int $idFather)
    {
        $this->idfather = $idFather;
        $sql = "SELECT*FROM tb_folder AS tbf WHERE tbf.f_idFather=? AND tbf.idFolder!=? ORDER BY tbf.idFolder DESC;";
        $params = [$this->idfather, $this->idfather];
        return $this->select_all($sql, $params);
    }
    /**
     * Metodo que obtiene los archivos de una carpeta
     * @param int $idFolder
     * @return array
     */
    public function select_files_by_folder(int $idFolder)
    {
        $this->idfolder = $idFolder;
        $sql = "SELECT*FROM tb_file AS tbf WHERE tbf.folder_id = ?  ORDER BY tbf.idFile DESC;";
        $params = [$this->idfolder];
        return $this->select_all($sql, $params);
    }
    /**
     * Metodo que inserta una nueva carpeta
     * @param int $iduser
     * @param string $name
     * @param int $idfather
     * @return bool|int|string
     */
    public function insert_folder(int $iduser, string $name, int $idfather)
    {
        $this->iduser = $iduser;
        $this->name = $name;
        $this->idfather = $idfather;
        $sql = "INSERT INTO `tb_folder` (`user_id`, `f_name`, `f_idFather`) VALUES (?, ?,?);";
        $params = [$this->iduser, $this->name, $this->idfather];
        return $this->insert($sql, $params);
    }
    /**
     * Metodo que selecciona el espacio usado y lo devuelve como un componente
     * y lo devuelve mediante la vista
     * @param int $iduser
     * @return string
     */
    public function select_space_used(int $iduser)
    {
        $this->iduser = strClean($iduser);
        $sql = "SELECT SUM(tbf.f_size) as f_size FROM tb_file AS tbf WHERE tbf.user_id=?;";
        $params = [$this->iduser];
        $requestStorage = $this->select($sql, $params);

        $storageAccount = $_SESSION['login_info']['space_limit'];
        //obtenemos los datos usados pero en GB
        $storageUsed = $requestStorage ? ($requestStorage['f_size'] ? valConvert($requestStorage['f_size'])['GB'] : 0) : 0;
        if ($storageAccount == 0) {
            // Caso de almacenamiento ilimitado
            if ($storageUsed < 20) {
                $width = 0;
            } elseif ($storageUsed > 20) {
                $width = 50;
            }

            $componentHtml = '<div class="storage mt-auto">                 
                <div class="progress">
                    <div class="progress-bar bg-success" style="width:' . $width . '%"></div>
                </div>
                <small>' . number_format($storageUsed, 2, ',', '.') . ' GB de <strong>[Ilimitado]</strong> utilizado(s)</small>
            </div>';
        } else {
            // Calcular porcentaje
            $width = ($storageUsed / $storageAccount) * 100;

            // Si quieres sin decimales si es número redondo, y con 2 si no:
            $widthFormatted = (floor($width) == $width) ? intval($width) : number_format($width, 2, ',', '.');

            // Igual con GB usados
            $storageUsedFormatted = (floor($storageUsed) == $storageUsed) ? intval($storageUsed) : number_format($storageUsed, 2, ',', '.');

            // Colores dinámicos de la barra
            if ($width < 50) {
                $barClass = "bg-success";   // verde
            } elseif ($width < 80) {
                $barClass = "bg-warning";   // amarillo
            } else {
                $barClass = "bg-danger";    // rojo
            }

            $componentHtml = <<<HTML
                            <div class="mt-auto">
                                <p style="font-size: 11px;" class="m-0">Almacenamiento ($widthFormatted % lleno)</p>
                                <div class="progress">
                                    <div class="progress-bar $barClass " 
                                        style="width: $width%" 
                                        aria-valuenow="$width" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small style="font-size: 9px;">$storageUsedFormatted GB de $storageAccount GB utilizado(s)</small>
                                <button class="btn btn-outline-primary rounded-pill w-100 mt-2">Obtener más almacenamiento</button>
                            </div>
            HTML;
        }

        return $componentHtml;
    }
    /**
     * Metodo que obtiene el espacio restante de la cuenta
     * lo devuelve en un array con los distintos formatos
     * para su uso posterior
     * @param int $iduser
     * @return array
     */
    public function select_total_space_remaining(int $iduser)
    {
        $this->iduser = strClean($iduser);
        $sql = "SELECT SUM(tbf.f_size) as space_used FROM tb_file AS tbf WHERE tbf.user_id=?;";
        $params = [$this->iduser];
        $requestStorage = $this->select($sql, $params);
        //obtenemos el espacio usado
        $spaceUsed = $requestStorage['space_used'] ?? 0;
        //si el espacio es  quiere decir que es ilimitado
        if ($_SESSION['login_info']['space_limit'] == 0) {
            //si el espacio es ilimitado devolvemos un valor alto
            return valConvert(1000000000, "TB") ?? []; //1TB en bytes
        }
        $storageAccount = $_SESSION['login_info']['space_limit'] ?? 0;
        //convertimos el espacion de la cuenta a bytes
        $storageAccountBytes = valConvert($storageAccount, "GB")['Bytes'] ?? 0;
        //obtenemos el espacio restante
        $spaceRemaining = $storageAccountBytes - $spaceUsed;
        //convertimos el espacio restante y lo devolvemos como array para su uso en los distintos formatos
        return valConvert($spaceRemaining) ?? [];
    }
    /**
     * Metodo que consulta en la base de datos mediante
     * user_id, f_name y f_idfather y devuelve la informacion
     * @param  int  $id
     * @param string  $name
     * @param  int  $father
     * @return mixed
     */
    public function select_folder_by_id_and_name_and_father(
        string $name,
        int $father
    ) {
        $this->name = $name;
        $this->idfather = $father;
        $arrValues = [
            $this->name,
            $this->idfather
        ];
        $sql = "SELECT
                    *
                FROM
                    tb_folder AS tbf
                WHERE
                     tbf.f_name = ?
                    AND tbf.f_idFather = ?;";
        $rqst = $this->select_all($sql, $arrValues);
        return $rqst;
    }
    /**
     * metodo que se encarga de solicitar la informacion de una tabla 
     */
    public function select_folder_id(int $iduser, int $idfolder)
    {
        $this->iduser = $iduser;
        $this->idfolder = $idfolder;
        $arrvalues = array($this->idfolder, $this->iduser);
        $sql = "SELECT*FROM tb_folder AS tbf WHERE tbf.idFolder=? AND tbf.user_id=?;";
        $rqst = $this->select($sql, $arrvalues);
        return $rqst;
    }
    /**
     * Metodo que se encarga de eliminar la carpeta
     * @param int $id
     * @return mixed
     */
    public function delete_folder(int $id)
    {
        $this->idfolder = $id;
        $sql = "DELETE FROM `tb_folder` WHERE  `idFolder`=?;";
        $request = $this->delete($sql, [$this->idfolder]);
        return $request;
    }
    /**
     * Metodo que se encarga de actualizar el nombre de la carpeta
     * @param int $idfolder
     * @param int $iduser
     * @param string $name
     * @return mixed
     */
    public function update_folder(int $idfolder, int $iduser, string $name)
    {
        $this->idfolder = $idfolder;
        $this->iduser = $iduser;
        $this->name = $name;
        $sql = "UPDATE `tb_folder` SET `f_name`=? WHERE  `idFolder`=? AND user_id=?;";
        $arrdata = [$this->name, $this->idfolder, $this->iduser];
        $request = $this->update($sql, $arrdata);
        return $request;
    }
    /**
     * Consultamos un archivo por el id del folder y el nombre del archivo
     * @param int $idfolder
     * @param string $name
     * @return array
     */
    public function select_file_by_idfolder_and_name(int $idfolder, string $name)
    {
        $this->idfolder = $idfolder;
        $this->name = $name;
        $sql = "SELECT * FROM tb_file AS tbf WHERE  tbf.folder_id=? AND tbf.f_name=?;";
        $params = [$this->idfolder, $this->name];
        $request = $this->select($sql, $params);
        return $request ?? [];
    }
    /**
     * Metodo que se encarga de insertar un nuevo archivo
     * @param int $user_id
     * @param int $folder_id
     * @param string $name
     * @param string $extension
     * @param int $size
     * @param string $path
     * @param int $favorites
     */
    public function insert_file(
        int $user_id,
        int $folder_id,
        string $name,
        string $extension,
        int $size,
        string $path,
        int $favorites
    ) {
        $this->iduser = $user_id;
        $this->folder_id = $folder_id;
        $this->name = $name;
        $this->extension = $extension;
        $this->size = $size;
        $this->path = $path;
        $this->favorites = $favorites;
        $sql = <<<SQL
                INSERT INTO `tb_file` (`user_id`, `folder_id`, `f_name`, `f_extension`, `f_size`, `f_path`, `f_favorites`) VALUES (?, ?, ?, ?, ?, ?, ?);
        SQL;
        $arrValues = [
            $this->iduser,
            $this->folder_id,
            $this->name,
            $this->extension,
            $this->size,
            $this->path,
            $this->favorites
        ];
        $request = $this->insert($sql, $arrValues);
        return $request;
    }
    /**
     * Metodo que se encarga de actualizar el nombre del archivo
     * @param int $idfile
     * @param int $iduser
     * @param string $name
     * @return mixed
     */
    public function update_file(int $idfile, int $iduser, string $name)
    {
        $this->iduser = $iduser;
        $this->idfolder = $idfile;
        $this->name = $name;
        $sql = <<<SQL
                    UPDATE `tb_file` SET `f_name`=? WHERE  `idFile`=? AND user_id=?;
        SQL;
        $arrValues = [$this->name, $this->idfolder, $this->iduser];
        $request = $this->update($sql, $arrValues);
        return $request;
    }
    /**
     * Metodo que se encarga de seleccionar un archivo por su id y el id del usuario
     * @param int $iduser
     * @param int $idfile
     */
    public function select_file_id(int $iduser, int $idfile)
    {
        $this->iduser = $iduser;
        $this->idfile = $idfile;
        $arrValues = [$this->idfile, $this->iduser];
        $sql = "SELECT * FROM tb_file AS tbf 
                WHERE tbf.idFile=? AND tbf.user_id=?;";
        $request = $this->select($sql, $arrValues);
        return $request;
    }
    /**
     * Metodo que se encarga de eliminar un archivo
     * @param int $id
     * @return bool
     */
    public function delete_file(int $id, int $iduser)
    {
        $this->idfile = $id;
        $this->iduser = $iduser;
        $sql = "DELETE FROM `tb_file` WHERE  `idFile`=? AND user_id=?";
        $request = $this->delete($sql, [$this->idfile, $this->iduser]);
        return $request;
    }
}