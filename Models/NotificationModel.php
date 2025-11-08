<?php
class NotificationModel extends Mysql
{
    private int $id;
    private int $read;
    private string $title;
    private string $description;
    private int $priority;
    private string $type;
    private string $color;
    private string $icon;
    private string $link;
    private string $status;
    private string $notificationEmail;
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Metodo que se encarga de obtener las notificaciones de un usuario mediante su id
     * @param int $idUser
     * @return void
     */
    public function select_notifications_by_id_of_user(int $idUser, int $limit = 5)
    {
        if ($limit == 0) {
            $textLimit = "";
        } else {
            $textLimit = "LIMIT $limit";
        }
        $this->id = $idUser;
        $sql = "SELECT
                    *
                FROM
                    tb_notification AS tbn
                WHERE
                    tbn.user_id = ?
                    AND
                    tbn.n_status='Activo'
                ORDER BY
                    tbn.n_created_at DESC
              $textLimit;";
        $request = $this->select_all($sql, array($this->id));
        return $request;
    }
    /**
     * Metodo que se encarga de actualizar si la notifiacion fue leida o no a travez de su id
     * @param int $id
     * @param int $read 
     * @return void
     */
    public function update_notification_read(int $id, int $read = 1)
    {
        $this->id = $id;
        $this->read = $read;
        $sql = "UPDATE `tb_notification` SET `n_is_read`=? WHERE  `idNotification`=?;";
        $request = $this->update($sql, [$this->read, $this->id]);
        return $request;
    }
    /**
     * Summary of insert_notification
     *
     * Description of insert_notification
     *
     * @param int $id
     * @param string $title
     * @param string $description
     * @param int $priority
     * @param string $type
     * @param string $color
     * @param string $icon
     * @param string $link
     * @return void
     */
    public function insert_notification(int $id, string $title, string $description, int $priority = 1, string $type = "info", string $color = "info", string $icon = "fa-bell", string $link = "", string $notificationEmail = "No")
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->type = $type;
        $this->color = $color;
        $this->icon = $icon;
        $this->link = $link;
        $this->notificationEmail = $notificationEmail;
        $arrValues = array(
            $this->id,
            $this->title,
            $this->description,
            $this->link,
            $this->icon,
            $this->color,
            $this->type,
            $this->priority,
            $this->notificationEmail
        );
        $sql = "INSERT INTO `tb_notification` (`user_id`, `n_title`, `n_description`, `n_link`, `n_icon`, `n_color`, `n_type`, `n_priority`,`n_notification_email`) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?);";
        $request = $this->insert($sql, $arrValues);
        return $request;
    }
    /**
     * Funcion que se encargar de obtener todas la notificaciones 
     * con los usuarios a los que se les envio la notificacion
     * @return void
     */
    public function select_all_notifications()
    {
        $sql = "SELECT tbu.idUser,tbu.u_fullname,tbu.u_profile,tbu.u_email,tbn.* FROM tb_user AS tbu
            INNER JOIN tb_notification AS tbn ON tbn.user_id=tbu.idUser
            ORDER BY tbn.n_created_at DESC;";
        $request = $this->select_all($sql);
        return $request;
    }
    /**
     * Metodo que se encarga de consultar todas las notificaciones
     * mediante su id el cual recibe como parametro
     * @param int $id
     * @return void
     */
    public function select_notification_by_id(int $id)
    {
        $sql = "SELECT * FROM tb_notification WHERE idNotification = ?";
        $request = $this->select($sql, [$id]);
        return $request;
    }
    /**
     * Metodo que sencarga de eliminar una notificacion mediante su id
     * @param int $id
     * @return void
     */
    public function delete_notification(int $id)
    {
        $sql = "DELETE FROM tb_notification WHERE idNotification = ?";
        $request = $this->delete($sql, [$id]);
        return $request;
    }
    /**
     * Metodo que se encargar de actualizar una notificacion mediante su id
     * @param int $id
     * @param string $title
     * @param string $description
     * @param int $priority
     * @param string $type
     * @param string $color
     * @param string $icon
     * @param string $link
     * 
     * @return void
     */
    public function update_notification(string $updateTxtTitle, string $updateTxtDescription, int $updateSlctPriority, string $updateSlctType, string $updateSlctColor, string $updateSlctIcon, string $updateTxtLink, string $updatePreviewStatus, int $updateTxtIdNotification)
    {
        $this->id = $updateTxtIdNotification;
        $this->title = $updateTxtTitle;
        $this->description = $updateTxtDescription;
        $this->priority = $updateSlctPriority;
        $this->type = $updateSlctType;
        $this->color = $updateSlctColor;
        $this->icon = $updateSlctIcon;
        $this->link = $updateTxtLink;
        $this->status = $updatePreviewStatus;
        $arrValues = array(
            $this->title,
            $this->description,
            $this->priority,
            $this->type,
            $this->color,
            $this->icon,
            $this->link,
            $this->status,
            $this->id
        );
        $sql = "UPDATE tb_notification SET n_title=?, n_description=?, n_priority=?, n_type=?, n_color=?, n_icon=?, n_link=?, n_status=? WHERE idNotification=?";
        $request = $this->update($sql, $arrValues);
        return $request;
    }

}