<?php

class Profile extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena la información del usuario en POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    /**
     * Nombre de la variable de sesión que almacena el negocio activo en POS.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
        $this->nameVarBusiness  = $sessionName . 'business_active';
    }

    /**
     * Muestra el perfil del usuario autenticado en el POS.
     *
     * @return void
     */
    public function profile(): void
    {
        $userInfo   = $_SESSION[$this->nameVarLoginInfo] ?? [];
        $userAppId  = (int) ($userInfo['idUser'] ?? 0);

        $profileData      = $this->model->selectUserProfile($userAppId) ?? [];
        $subscriptionData = $this->model->selectLatestSubscription($userAppId) ?? [];
        $businesses       = $this->model->selectBusinesses($userAppId);

        $data = [
            'page_id'          => 0,
            'page_title'       => 'Perfil de usuario',
            'page_description' => 'Resumen de identidad, facturación y plan activo en el POS.',
            'page_container'   => 'Profile',
            'page_view'        => 'profile',
            'page_js_css'      => 'profile',
            'user'             => $this->formatUserProfile($profileData, $userInfo),
            'subscription'     => $this->formatSubscription($subscriptionData),
            'businesses'       => $this->formatBusinesses($businesses),
        ];

        $this->views->getView($this, 'profile', $data, 'POS');
    }

    /**
     * Normaliza la información de usuario combinando sesión y base de datos.
     *
     * @param array $profileData Datos provenientes del modelo.
     * @param array $sessionData Datos de sesión almacenados al iniciar.
     * @return array Información preparada para la vista.
     */
    private function formatUserProfile(array $profileData, array $sessionData): array
    {
        if (empty($profileData)) {
            return [
                'fullname'      => trim(($sessionData['name'] ?? '') . ' ' . ($sessionData['lastname'] ?? '')),
                'email'         => $sessionData['email'] ?? '',
                'user'          => $sessionData['user'] ?? '',
                'status'        => $sessionData['status'] ?? '',
                'planExpiresAt' => null,
                'phone'         => null,
                'country'       => null,
                'registeredAt'  => null,
                'updatedAt'     => null,
                'birthDate'     => null,
            ];
        }

        $email   = !empty($profileData['email']) ? decryption($profileData['email']) : ($sessionData['email'] ?? '');
        $user    = !empty($profileData['user']) ? decryption($profileData['user']) : ($sessionData['user'] ?? '');
        $phone   = trim(($profileData['telephone_prefix'] ?? '') . ' ' . ($profileData['phone_number'] ?? ''));

        return [
            'fullname'      => trim(($profileData['names'] ?? '') . ' ' . ($profileData['lastname'] ?? '')),
            'email'         => $email,
            'user'          => $user,
            'status'        => $profileData['user_status'] ?? ($profileData['people_status'] ?? ''),
            'planExpiresAt' => $profileData['plan_expiration_date'] ?? null,
            'phone'         => $phone,
            'country'       => $profileData['country'] ?? null,
            'registeredAt'  => $profileData['user_registered_at'] ?? null,
            'updatedAt'     => $profileData['user_updated_at'] ?? null,
            'birthDate'     => $profileData['date_of_birth'] ?? null,
        ];
    }

    /**
     * Formatea la suscripción y plan activo del usuario.
     *
     * @param array $subscriptionData Datos crudos de la suscripción.
     * @return array Información preparada para la vista.
     */
    private function formatSubscription(array $subscriptionData): array
    {
        if (empty($subscriptionData)) {
            return [
                'plan'          => 'Sin plan asignado',
                'price'         => null,
                'billingPeriod' => null,
                'status'        => 'sin datos',
                'startDate'     => null,
                'endDate'       => null,
                'nextBilling'   => null,
                'autoRenew'     => null,
                'discount'      => null,
            ];
        }

        $discount = null;
        if (!empty($subscriptionData['discount_code'])) {
            $discountValue = (float) ($subscriptionData['discount_value'] ?? 0);
            $discountType  = $subscriptionData['discount_type'] === 'percentage' ? '%' : getCurrency();
            $discount = sprintf(
                '%s (%s%s)%s',
                $subscriptionData['discount_code'],
                $discountValue,
                $discountType,
                !empty($subscriptionData['discount_recurring']) ? ' - recurrente' : ''
            );
        }

        return [
            'plan'          => $subscriptionData['plan_name'] ?? 'Plan desconocido',
            'price'         => $subscriptionData['price_per_cycle'] ?? $subscriptionData['base_price'] ?? null,
            'billingPeriod' => $subscriptionData['billing_period'] ?? null,
            'status'        => $subscriptionData['status'] ?? 'sin datos',
            'startDate'     => $subscriptionData['start_date'] ?? null,
            'endDate'       => $subscriptionData['end_date'] ?? null,
            'nextBilling'   => $subscriptionData['next_billing_date'] ?? null,
            'autoRenew'     => $subscriptionData['auto_renew'] ?? null,
            'discount'      => $discount,
        ];
    }

    /**
     * Formatea los negocios del usuario.
     *
     * @param array $businesses Lista de negocios asociados.
     * @return array Lista preparada para la vista.
     */
    private function formatBusinesses(array $businesses): array
    {
        if (empty($businesses)) {
            return [];
        }

        return array_map(static function ($business) {
            $phone = trim(($business['telephone_prefix'] ?? '') . ' ' . ($business['phone_number'] ?? ''));

            return [
                'id'          => $business['idBusiness'] ?? null,
                'name'        => $business['business'] ?? '',
                'category'    => $business['category'] ?? '',
                'document'    => $business['document_number'] ?? '',
                'email'       => $business['email'] ?? '',
                'phone'       => $phone,
                'address'     => $business['direction'] ?? '',
                'city'        => $business['city'] ?? '',
                'country'     => $business['country'] ?? '',
                'status'      => $business['status'] ?? '',
                'registered'  => $business['registration_date'] ?? null,
            ];
        }, $businesses);
    }
}
