<?php
class BoxModel extends Mysql
{
    protected int $businessId;
    protected int $boxId;
    protected string $status;
    protected int $userId;
    protected float $initialAmount;
    protected int $boxSessionsId;
    protected string $typeMovement;
    protected string $concept;
    protected string $paymentMethod;
    protected int $limit;
    protected int $boxCashCountsId;
    protected string $type;
    protected float $expectedAmount;
    protected float $countedAmount;
    protected float $difference;
    protected string $notes;
    protected int $currencyDenominationId;
    protected int $quantity;
    protected float $total;
    protected string $closingDate;
    protected float $amount;
    protected int $customerId;
    protected int $paymentMethodId;

    protected $referenceTable = null;
    protected $referenceId = null;
    
    // Variables de tabla voucher header
    protected string $nameCustomer;
    protected string $directionCustomer;
    protected string $nameBussines;
    protected string $documentBussines;
    protected string $directionBussines;
    protected string $dateTime;
    protected string $voucherName;



    // ? Funciones get
    public function getUsingBox(int $boxId, string $status)
    {
        $this->boxId = $boxId;
        $this->status = $status;
        $sql = <<<SQL
            SELECT
                *
            FROM box_sessions
            WHERE box_id = ? AND `status` = ?
            ORDER BY box_id ASC;
        SQL;

        return $this->select($sql, [$this->boxId, $this->status]);
    }

    public function getBox(int $boxId)
    {
        $this->boxId = $boxId;
        $sql = <<<SQL
            SELECT
                name,
                status
            FROM `box`
            WHERE idBox = ?
            LIMIT 1;
        SQL;

        return $this->select($sql, [$this->boxId]);
    }

    public function getBoxsById(int $boxId, int $businessId)
    {
        $this->boxId = $boxId;
        $this->businessId = $businessId;
        $sql = <<<SQL
            SELECT
                name,
                status
            FROM `box`
            WHERE idBox = ? AND business_id = ?
            LIMIT 1;
        SQL;

        return $this->select($sql, [$this->boxId, $this->businessId]);
    }

    public function getBoxByIdAndBusinessId(int $boxId, int $businessId)
    {
        $this->boxId = $boxId;
        $this->businessId = $businessId;
        $sql = <<<SQL
            SELECT
                *
            FROM box
            WHERE idBox = ? AND business_id = ?
            ORDER BY idBox ASC;
        SQL;

        return $this->select($sql, [$this->boxId, $this->businessId]);
    }

    public function getBoxByStatusAndBoxId(string $status, $boxId)
    {
        $this->status = $status;
        $this->boxId = $boxId;
        $sql = <<<SQL
            SELECT
                *
            FROM box_sessions
            WHERE `status` != ? AND box_id = ?
            ORDER BY box_id ASC;
        SQL;

        return $this->select($sql, [$this->status, $this->boxId]);
    }

    public function getBoxSessionsByUserId(int $userId)
    {
        $this->userId = $userId;
        $sql = <<<SQL
            SELECT
                *
            FROM box_sessions
            WHERE userapp_id = ? AND `status` != "Cerrada"
            ORDER BY box_id ASC;
        SQL;

        return $this->select($sql, [$this->userId]);
    }

    public function getLastCashCount(int $boxSessionsId)
    {
        $this->boxSessionsId = $boxSessionsId;
        $sql = <<<SQL
            SELECT
                *
            FROM box_cash_counts
            WHERE box_session_id = ?
            ORDER BY idBoxCashCounts DESC
            LIMIT 1;
        SQL;

        return $this->select($sql, [$this->boxSessionsId]);
    }

    public function issetCustomer(int $businessId, int $customerId)
    {
        $this->businessId = $businessId;
        $this->customerId = $customerId;
        $sql = <<<SQL
            SELECT
                *
            FROM customer
            WHERE business_id = ? AND idCustomer = ?
            LIMIT 1;
        SQL;

        return $this->select($sql, [$this->businessId, $this->customerId]);
    }

    public function issetPaymentMethod(int $paymentMethodId)
    {
        $this->paymentMethodId = $paymentMethodId;
        $sql = <<<SQL
            SELECT
                *
            FROM payment_method
            WHERE idPaymentMethod = ?
            LIMIT 1;
        SQL;

        return $this->select($sql, [$this->paymentMethodId]);
    }

    // ? Funciones getAll
    public function getBoxs(int $businessId)
    {
        $this->businessId = $businessId;
        $sql = <<<SQL
            SELECT
                idBox,
                `name`,
                `status`
            FROM `box`
            WHERE business_id = ?
            ORDER BY idBox ASC;
        SQL;

        return $this->select_all($sql, [$this->businessId]);
    }

    public function getCustomersByBusiness(int $businessId)
    {
        $this->businessId = $businessId;
        $sql = <<<SQL
            SELECT
                idCustomer,
                fullname,
                document_number
            FROM customer
            WHERE business_id = ?
            ORDER BY idCustomer ASC;
        SQL;

        return $this->select_all($sql, [$this->businessId]);
    }

    public function getBoxMovements(int $boxSessionsId)
    {
        $this->boxSessionsId = $boxSessionsId;
        $sql = <<<SQL
            SELECT
                type_movement,
                concept,
                amount,
                payment_method,
                movement_date
            FROM box_movements
            WHERE boxSessions_id = ?
            ORDER BY type_movement ASC;
        SQL;

        return $this->select_all($sql, [$this->boxSessionsId]);
    }

    public function getBoxMovementsByLimit(int $boxSessionsId, int $limit)
    {
        $this->boxSessionsId = $boxSessionsId;
        $this->limit = $limit;
        $sql = <<<SQL
            SELECT
                type_movement,
                concept,
                amount,
                payment_method,
                movement_date
            FROM box_movements
            WHERE boxSessions_id = ?
            ORDER BY movement_date DESC
            LIMIT $this->limit;
        SQL;

        return $this->select_all($sql, [$this->boxSessionsId]);
    }

    public function getPaymentMethods()
    {
        $sql = <<<SQL
            SELECT
                idPaymentMethod,
                icon,
                icon,
                `name`
            FROM payment_method
            ORDER BY idPaymentMethod ASC;
        SQL;

        return $this->select_all($sql, []);
    }

    public function getCurrencyDenominations(string $status = "Activo")
    {
        $this->status = $status;
        $sql = <<<SQL
            SELECT
                *
            FROM currency_denominations
            WHERE `status` = ?
            ORDER BY type ASC, value DESC;
        SQL;

        return $this->select_all($sql, [$this->status]);
    }

    public function getMovementsForHours(int $boxSessionsId)
    {
        $this->boxSessionsId = $boxSessionsId;
        $sql = "SELECT 
            DATE_FORMAT(created_at, '%H:00') as hora, 
            SUM(total) as total 
        FROM sales 
        WHERE box_session_id = ? 
        GROUP BY hora 
        ORDER BY hora ASC";

        $sql = <<<SQL
            SELECT 
                DATE_FORMAT(movement_date, '%H:00') as hora, 
                SUM(amount) as total
            FROM box_movements 
            WHERE boxSessions_id = ? AND type_movement = 'Ingreso' 
            GROUP BY DATE_FORMAT(movement_date, '%H:00') 
            ORDER BY hora ASC;
        SQL;

        return $this->select_all($sql, [$this->boxSessionsId]);
    }

    // ? Funciones insert
    public function insertBoxSessions(int $boxId, int $userId, float $initialAmount)
    {
        $this->boxId = $boxId;
        $this->userId = $userId;
        $this->initialAmount = $initialAmount;
        $sql = <<<SQL
            INSERT INTO box_sessions
                (box_id, userapp_id, initial_amount)
            VALUES
                (?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->boxId, $this->userId, $this->initialAmount]);
    }

    public function insertBoxCashCount(int $boxSessionsId, string $type, float $expectedAmount, float $countedAmount, float $difference, string $notes)
    {
        $this->boxSessionsId = $boxSessionsId;
        $this->type = $type;
        $this->expectedAmount = $expectedAmount;
        $this->countedAmount = $countedAmount;
        $this->difference = $difference;
        $this->notes = $notes;
        $sql = <<<SQL
            INSERT INTO box_cash_counts
                (box_session_id, `type`, expected_amount, counted_amount, difference, notes)
            VALUES
                (?, ?, ?, ?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->boxSessionsId, $this->type, $this->expectedAmount, $this->countedAmount, $this->difference, $this->notes]);
    }

    public function insertBoxCashCountDetails(int $boxCashCountsId, int $currencyDenominationId, int $quantity, float $total)
    {
        $this->boxCashCountsId = $boxCashCountsId;
        $this->currencyDenominationId = $currencyDenominationId;
        $this->quantity = $quantity;
        $this->total = $total;
        $sql = <<<SQL
            INSERT INTO box_cash_count_details
                (box_cash_count_id, currency_denomination_id, quantity, total)
            VALUES
                (?, ?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->boxCashCountsId, $this->currencyDenominationId, $this->quantity, $this->total]);
    }

    public function insertBoxMovement(int $boxSessionsId, string $type, string $notes, float $amount, string $paymentMethod, $referenceTable = null, $referenceId = null)
    {
        $this->boxSessionsId = $boxSessionsId;
        $this->type = $type;
        $this->notes = $notes;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->referenceTable = $referenceTable;
        $this->referenceId = $referenceId;
        $sql = <<<SQL
            INSERT INTO box_movements
                (boxSessions_id, type_movement, concept, amount, payment_method, reference_table, reference_id)
            VALUES
                (?, ?, ?, ?, ?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->boxSessionsId, $this->type, $this->notes, $this->amount, $this->paymentMethod, $this->referenceTable, $this->referenceId]);
    }

    public function insertVoucherHeader(string $nameCustomer, string $directionCustomer, string $nameBussines, string $documentBussines, string $directionBussines, string $dateTime, float $amount, string $voucherName, int $paymentMethod, int $businessId, int $userId)
    {
        $this->nameCustomer = $nameCustomer;
        $this->directionCustomer = $directionCustomer;
        $this->nameBussines = $nameBussines;
        $this->documentBussines = $documentBussines;
        $this->directionBussines = $directionBussines;
        $this->dateTime = $dateTime;
        $this->amount = $amount;
        $this->voucherName = $voucherName;
        $this->paymentMethod = $paymentMethod;
        $this->businessId = $businessId;
        $this->userId = $userId;
        $sql = <<<SQL
            INSERT INTO voucher_header
                (name_customer, direction_customer, name_bussines, document_bussines, direction_bussines, date_time, amount, voucher_name, payment_method_id, business_id, user_app_id)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->nameCustomer, $this->directionCustomer, $this->nameBussines, $this->documentBussines, $this->directionBussines, $this->dateTime, $this->amount, $this->voucherName, $this->paymentMethod, $this->businessId, $this->userId]);
    }

    // ? Funciones update
    public function updateCloseSession(int $boxSessionsId, string $closingDate, $notes, string $status): bool
    {
        $this->boxSessionsId = $boxSessionsId;
        $this->closingDate = $closingDate;
        $this->status = $status;

        $sql = <<<SQL
            UPDATE box_sessions
            SET
                closing_date = ?,
                closing_notes = ?,
                `status` = ?
            WHERE idBoxSessions = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$this->closingDate, $notes, $this->status, $this->boxSessionsId]);
    }

    // ? Funciones delete
}
