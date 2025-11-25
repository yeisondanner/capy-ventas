<?php
class LogsModel extends Mysql
{
    /**
     * Encapsula los datos del log.
     */
    private string $title;
    private string  $description;
    private int $typeLog;
    private int $idUser;
    private string $table;
    /**
     * Constructor de la clase LogsModel.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Inserta un nuevo registro de log en la base de datos.
     *
     * @param string $title
     * @param string $description
     * @param int $typeLog
     * @param int $idUser
     * @param string $table
     * @return int
     */
    public function insert_log(string $title, string $description, int $typeLog, int $idUser, string $table)
    {
        $this->title = $title;
        $this->description = $description;
        $this->typeLog = $typeLog;
        $this->idUser = $idUser;
        $this->table = $table;
        $arrValues = array(
            $this->title,
            $this->description,
            $this->typeLog,
            $this->idUser,
            $this->table
        );
        $sql = "INSERT INTO `tb_log` (`l_title`, `l_description`, `typelog_id`, `user_id`, `l_table`) VALUES (?,?,?,?,?);";
        $request = $this->insert($sql, $arrValues);
        return $request;
    }
    public function select_logs($minData, $maxData, int $filter_type)
    {
        $this->typeLog = $filter_type;
        if ($this->typeLog == 0 && $minData == 0 && $maxData == 0) {
            $sql = "SELECT tbl.*,tbtl.tl_name,tbu.u_fullname,tbu.u_user,tbu.u_email FROM tb_log AS tbl
            INNER JOIN tb_typelog AS tbtl ON tbtl.idTypeLog=tbl.typelog_id
            LEFT JOIN tb_user AS tbu ON tbu.idUser=tbl.user_id ORDER BY tbl.idLog DESC;";
            $arrValues = [];
        } else if ($filter_type == 0 && $minData != 0 && $maxData != 0) {
            $sql = "SELECT 
                        tbl.*,
                        tbtl.tl_name,
                        tbu.u_fullname,
                        tbu.u_user,
                        tbu.u_email 
                    FROM tb_log AS tbl
                    INNER JOIN tb_typelog AS tbtl ON tbtl.idTypeLog = tbl.typelog_id
                    INNER JOIN tb_user AS tbu ON tbu.idUser = tbl.user_id
                    WHERE tbl.l_registrationDate BETWEEN ? AND ?
                    ORDER BY tbl.idLog DESC;";
            $arrValues = array($minData, $maxData);
        } else {
            $sql = "SELECT 
                        tbl.*,
                        tbtl.tl_name,
                        tbu.u_fullname,
                        tbu.u_user,
                        tbu.u_email 
                    FROM tb_log AS tbl
                    INNER JOIN tb_typelog AS tbtl ON tbtl.idTypeLog = tbl.typelog_id
                    INNER JOIN tb_user AS tbu ON tbu.idUser = tbl.user_id
                    WHERE tbl.typelog_id = ?
                    AND tbl.l_registrationDate BETWEEN ? AND ?
                    ORDER BY tbl.idLog DESC;";
            $arrValues = array($this->typeLog, $minData, $maxData);
        }
        $request = $this->select_all($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene la lista de años disponibles en los registros de logs.
     *
     * @return array
     */
    public function getAvailableYears()
    {
        $sql = "SELECT DISTINCT YEAR(l_registrationDate) AS year FROM tb_log ORDER BY year DESC";
        return $this->select_all($sql);
    }

    /**
     * Calcula los totales por tipo de log para un año concreto.
     *
     * @param int $year
     * @return array
     */
    public function getLogTotalsByYear(int $year)
    {
        $sql = "SELECT
                    tl.idTypeLog,
                    tl.tl_name,
                    COUNT(tbl.idLog) AS total
                FROM tb_typelog AS tl
                LEFT JOIN tb_log AS tbl
                    ON tbl.typelog_id = tl.idTypeLog
                    AND YEAR(tbl.l_registrationDate) = ?
                GROUP BY tl.idTypeLog, tl.tl_name
                ORDER BY tl.idTypeLog";
        return $this->select_all($sql, [$year]);
    }

    /**
     * Obtiene el comportamiento mensual de los logs por tipo para el año indicado.
     *
     * @param int $year
     * @return array
     */
    public function getLogMonthlyTrend(int $year)
    {
        $sql = "SELECT
                    tl.idTypeLog,
                    tl.tl_name,
                    MONTH(tbl.l_registrationDate) AS month,
                    COUNT(tbl.idLog) AS total
                FROM tb_typelog AS tl
                LEFT JOIN tb_log AS tbl
                    ON tbl.typelog_id = tl.idTypeLog
                    AND YEAR(tbl.l_registrationDate) = ?
                WHERE tbl.idLog IS NOT NULL
                GROUP BY tl.idTypeLog, tl.tl_name, month
                ORDER BY tl.idTypeLog, month";
        return $this->select_all($sql, [$year]);
    }
}
