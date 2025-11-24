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
        $historyData      = $this->model->selectSubscriptionHistory($userAppId) ?? [];
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
            'invoices'         => $this->formatSubscriptionHistory($historyData),
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
            $discountType  = $subscriptionData['discount_type'] ?? 'fixed';
            $discount = $discountType === 'percentage'
                ? "{$discountValue}% ({$subscriptionData['discount_code']})"
                : getCurrency() . " " . number_format($discountValue, 2) . " ({$subscriptionData['discount_code']})";
        }

        return [
            'plan'          => $subscriptionData['plan_name'] ?? 'Sin plan asignado',
            'price'         => $subscriptionData['price_per_cycle'] ?? null,
            'billingPeriod' => $subscriptionData['billing_period'] ?? null,
            'status'        => $subscriptionData['status'] ?? 'sin datos',
            'startDate'     => $subscriptionData['start_date'] ?? null,
            'endDate'       => $subscriptionData['end_date'] ?? null,
            'nextBilling'   => $subscriptionData['next_billing_date'] ?? null,
            'autoRenew'     => !empty($subscriptionData['auto_renew']),
            'discount'      => $discount,
        ];
    }

    /**
     * Formatea el historial de facturación para mostrarlo en la vista.
     *
     * @param array $historyData Lista de facturas.
     * @return array Lista formateada.
     */
    private function formatSubscriptionHistory(array $historyData): array
    {
        if (empty($historyData)) {
            return [];
        }
        $status = [
            'pending' => ['Pendiente', 'bg-warning', 'bi-pause-fill'],
            'paid'    => ['Pagado', 'bg-success', 'bi-check-circle-fill'],
            'failed'  => ['Fallido', 'bg-danger', 'bi-x-circle-fill'],
        ];

        $formatted = [];
        foreach ($historyData as $invoice) {
            $invoice['status'] = $status[$invoice['status']] ?? 'unknown';
            $formatted[] = [
                'id'            => $invoice['idInvoice'],
                'plan'          => $invoice['plan_name'] ?? 'Plan desconocido',
                'subtotal'      => $invoice['subtotal'] ?? 0,
                'total'         => $invoice['total'] ?? 0,
                'status'        => $invoice['status'] ?? 'unknown',
                'startDate'     => $invoice['period_start'] ?? null,
                'endDate'       => $invoice['period_end'] ?? null,
                'billingPeriod' => $invoice['billing_period'] ?? null,
                'discount'      => $invoice['discount_amount'] ?? null,
            ];
        }

        return $formatted;
    }

    /**
     * Formatea la lista de negocios asociados.
     *
     * @param array $businesses Lista de negocios cruda.
     * @return array Lista formateada.
     */
    private function formatBusinesses(array $businesses): array
    {
        if (empty($businesses)) {
            return [];
        }

        $formatted = [];
        foreach ($businesses as $business) {
            $formatted[] = [
                'id'         => $business['idBusiness'],
                'name'       => $business['business'] ?? 'Sin nombre',
                'document'   => $business['document_number'] ?? 'Sin documento',
                'email'      => $business['email'] ?? 'Sin correo',
                'phone'      => trim(($business['telephone_prefix'] ?? '') . ' ' . ($business['phone_number'] ?? '')),
                'city'       => $business['city'] ?? '',
                'country'    => $business['country'] ?? '',
                'status'     => $business['status'] ?? 'Desconocido',
                'registered' => $business['registration_date'] ?? null,
                'category'   => $business['category'] ?? 'Sin categoría',
            ];
        }

        return $formatted;
    }
}
