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
}
