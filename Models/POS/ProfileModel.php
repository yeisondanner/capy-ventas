<?php

class ProfileModel extends Mysql
{
    /**
     * Obtiene los datos detallados del usuario y su información personal.
     *
     * @param int $userAppId Identificador del usuario que inició sesión en el POS.
     * @return array|null Datos combinados del usuario y la persona o null si no existe.
     */
    public function selectUserProfile(int $userAppId): ?array
    {
        $sql = <<<SQL
            SELECT
                ua.idUserApp,
                ua.user,
                ua.status              AS user_status,
                ua.registration_date   AS user_registered_at,
                ua.update_date         AS user_updated_at,
                ua.plan_expiration_date,
                p.names,
                p.lastname,
                p.email,
                p.date_of_birth,
                p.country,
                p.telephone_prefix,
                p.phone_number,
                p.status               AS people_status,
                p.registration_date    AS people_registered_at,
                p.update_date          AS people_updated_at
            FROM user_app AS ua
            INNER JOIN people AS p ON p.idPeople = ua.people_id
            WHERE ua.idUserApp = ?
            LIMIT 1;
        SQL;

        $request = $this->select($sql, [$userAppId]);
        return $request ?: null;
    }

    /**
     * Obtiene la suscripción activa más reciente del usuario junto con el plan y descuento aplicado.
     *
     * @param int $userAppId Identificador del usuario que inició sesión en el POS.
     * @return array|null Información de la suscripción y plan asociado o null si no existe.
     */
    public function selectLatestSubscription(int $userAppId): ?array
    {
        $sql = <<<SQL
            SELECT
                s.idSubscription,
                s.start_date,
                s.end_date,
                s.next_billing_date,
                s.price_per_cycle,
                s.status,
                s.auto_renew,
                p.idPlan,
                p.name             AS plan_name,
                p.description      AS plan_description,
                p.base_price,
                p.billing_period,
                p.is_active        AS plan_is_active,
                d.code             AS discount_code,
                d.type             AS discount_type,
                d.value            AS discount_value,
                d.is_recurring     AS discount_recurring
            FROM subscriptions AS s
            INNER JOIN plans AS p ON p.idPlan = s.plan_id
            LEFT JOIN discounts AS d ON d.idDiscount = s.discount_id
            WHERE s.user_app_id = ?
            ORDER BY s.start_date DESC
            LIMIT 1;
        SQL;

        $request = $this->select($sql, [$userAppId]);
        return $request ?: null;
    }

    /**
     * Devuelve los negocios asociados al usuario para mostrar el contexto comercial.
     *
     * @param int $userAppId Identificador del usuario que inició sesión en el POS.
     * @return array Lista de negocios encontrados.
     */
    public function selectBusinesses(int $userAppId): array
    {
        $sql = <<<SQL
            SELECT
                b.idBusiness,
                b.`name`              AS business,
                b.document_number,
                b.email,
                b.direction,
                b.city,
                b.country,
                b.phone_number,
                b.telephone_prefix,
                b.status,
                b.registration_date,
                bt.`name`             AS category
            FROM business AS b
            LEFT JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
            WHERE b.userapp_id = ?
            ORDER BY b.registration_date ASC;
        SQL;

        $request = $this->select_all($sql, [$userAppId]);
        return $request ?? [];
    }

    /**
     * Obtiene el historial de facturación (invoices) del usuario.
     *
     * @param int $userAppId Identificador del usuario.
     * @return array Lista de facturas ordenadas por fecha de inicio descendente.
     */
    public function selectSubscriptionHistory(int $userAppId): array
    {
        $sql = <<<SQL
            SELECT
                i.idInvoice,
                i.period_start,
                i.period_end,
                i.subtotal,
                i.total,
                i.status,
                i.discount_amount,
                p.name AS plan_name,
                p.billing_period
            FROM invoices AS i
            INNER JOIN subscriptions AS s ON i.subscription_id = s.idSubscription
            INNER JOIN plans AS p ON s.plan_id = p.idPlan
            WHERE s.user_app_id = ?
            ORDER BY i.period_start DESC;
        SQL;

        $request = $this->select_all($sql, [$userAppId]);
        return $request ?? [];
    }
    /**
     * Actualiza solo los datos básicos del perfil (campos del formulario).
     *
     * @param int   $userAppId ID del usuario en user_app.
     * @param array $data      names, lastnames, email, phone, country, birthDate, username.
     * @return bool
     */
    public function updateUserProfile(int $userAppId, array $data): bool
    {
        $names     = trim($data['names'] ?? '');
        $lastnames = trim($data['lastnames'] ?? '');
        $email     = trim($data['email'] ?? '');
        $phone     = trim($data['phone'] ?? '');
        $birthDate = $data['birthDate'] ?? null;
        $username  = trim($data['username'] ?? '');

        $phoneNumber = $phone; // Actualizar solo el número de teléfono

        // Encriptar campos que están cifrados en BD
        $emailEncrypted = !empty($email) ? encryption($email) : null;
        $userEncrypted  = !empty($username) ? encryption($username) : null;

        $sql = <<<SQL
            UPDATE user_app AS ua
            INNER JOIN people AS p ON p.idPeople = ua.people_id
            SET
                ua.user            = COALESCE(?, ua.user),
                ua.update_date     = NOW(),
                p.names            = COALESCE(?, p.names),
                p.lastname         = COALESCE(?, p.lastname),
                p.email            = COALESCE(?, p.email),
                p.date_of_birth    = COALESCE(?, p.date_of_birth),
                p.phone_number     = COALESCE(?, p.phone_number),      
                p.update_date      = NOW()
            WHERE ua.idUserApp = ?;
        SQL;

        $params = [
            $userEncrypted,
            $names ?: null,
            $lastnames ?: null,
            $emailEncrypted,
            !empty($birthDate) ? $birthDate : null,
            $phoneNumber ?: null,
            $userAppId,
        ];

        $result = $this->update($sql, $params);
        return $result > 0;
    }

    /**
     * Verifica si la contraseña actual ingresada coincide con la del usuario.
     *
     * @param int    $userAppId ID del usuario en user_app.
     * @param string $currentPassword Contraseña actual en texto plano.
     * @return bool
     */
    public function verifyUserPassword(int $userAppId, string $currentPassword): bool
    {
        $sql = "SELECT password FROM user_app WHERE idUserApp = ? LIMIT 1";
        $row = $this->select($sql, [$userAppId]);

        if (empty($row) || empty($row['password'])) {
            return false;
        }

        $storedEncrypted = (string) $row['password'];
        $storedPlain     = (string) decryption($storedEncrypted);

        return hash_equals($storedPlain, (string) $currentPassword);
    }

    /**
     * Cambia la contraseña del usuario después de verificar la actual.
     *
     * @param int    $userAppId       ID del usuario en user_app.
     * @param string $currentPassword Contraseña actual en texto plano.
     * @param string $newPassword     Nueva contraseña en texto plano.
     * @return bool
     */
    public function changeUserPassword(int $userAppId, string $currentPassword, string $newPassword): bool
    {
        // Verificar la contraseña actual
        $sql = "SELECT password FROM user_app WHERE idUserApp = ? LIMIT 1";
        $row = $this->select($sql, [$userAppId]);

        if (empty($row) || empty($row['password'])) {
            return false;
        }

        $storedEncrypted = (string) $row['password'];
        $storedPlain     = (string) decryption($storedEncrypted);

        // Comparar contraseñas
        if (!hash_equals($storedPlain, (string) $currentPassword)) {
            return false;
        }

        // Actualizar a la nueva contraseña
        $newEncrypted = encryption($newPassword);

        $upd = "UPDATE user_app SET password = ?, update_date = NOW() WHERE idUserApp = ? LIMIT 1";
        $result = $this->update($upd, [$newEncrypted, $userAppId]);

        return $result > 0;
    }
}
