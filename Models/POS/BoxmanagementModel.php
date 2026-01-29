<?php

class BoxmanagementModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    //Listar cajas
    public function select_boxes(int $businessId): array
    {
        $sql = "SELECT
                    b.idBox,
                    b.name,
                    b.description,
                    b.status,
                    b.registrationDate
                FROM box b
                WHERE b.business_id = ?
                  AND b.status = 'Activo'
                ORDER BY b.idBox DESC";

        return $this->select_all($sql, [$businessId]);
    }

    public function select_box(int $idBox, int $businessId): array
    {
        $sql = "SELECT b.*
                FROM box b
                WHERE b.idBox = ? AND b.business_id = ?
                LIMIT 1";

        $result = $this->select($sql, [$idBox, $businessId]);
        return is_array($result) ? $result : [];
    }

    //Insertar nueva caja
    public function insert_box(array $data): int
    {
        $sql = "INSERT INTO box (name, description, business_id, status)
                VALUES (?, ?, ?, 'Activo')";

        $arrData = [
            $data['name'],
            $data['description'] !== '' ? $data['description'] : null,
            $data['business_id'],
        ];

        return (int) $this->insert($sql, $arrData);
    }

    //Actualizar caja
    public function update_box(array $data): bool
    {
        $sql = "UPDATE box SET
                    name = ?,
                    description = ?
                WHERE idBox = ? AND business_id = ?
                LIMIT 1";

        $arrData = [
            $data['name'],
            $data['description'] !== '' ? $data['description'] : null,
            $data['idBox'],
            $data['business_id']
        ];

        return (bool) $this->update($sql, $arrData);
    }

    public function delete_box(int $idBox, int $businessId): bool
    {
        $sql = "DELETE FROM box WHERE idBox = ? AND business_id = ? LIMIT 1";
        return (bool) $this->delete($sql, [$idBox, $businessId]);
    }

    public function selectBoxByName(string $name, int $businessId): array
    {
        $sql = "SELECT b.idBox
                FROM box AS b
                WHERE b.business_id = ?
                  AND b.name = ?
                LIMIT 1";

        $result = $this->select($sql, [$businessId, $name]);
        return is_array($result) ? $result : [];
    }

    //ESTO ME AYUDO CHATGPT PRO PLUS MAX PRO MAX
    // Inactivar la caja (cambiar estado a 'Inactivo')
    public function inactivate_box(int $idBox, int $businessId): bool
    {
        $sql = "UPDATE box
                SET status = 'Inactivo'
                WHERE idBox = ? AND business_id = ?
                LIMIT 1";
        return (bool) $this->update($sql, [$idBox, $businessId]);
    }

    // Verificar si la caja tiene referencias en otras tablas
    public function box_has_references(int $idBox): bool
    {
        // Obtener todas las tablas y columnas que referencian a box.idBox
        $sql = "SELECT TABLE_NAME, COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
                  AND REFERENCED_TABLE_NAME = 'box'
                  AND REFERENCED_COLUMN_NAME = 'idBox'";

        $refs = $this->select_all($sql);

        if (!is_array($refs) || empty($refs)) {
            return false;
        }

        foreach ($refs as $ref) {
            $table = $ref['TABLE_NAME'] ?? '';
            $col   = $ref['COLUMN_NAME'] ?? '';

            if ($table === '' || $col === '') continue;

            // Validar nombres de tabla y columna para evitar inyección SQL
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) continue;
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) continue;

            $checkSql = "SELECT 1 FROM `$table` WHERE `$col` = ? LIMIT 1";
            $row = $this->select($checkSql, [$idBox]);

            if (!empty($row)) {
                return true;
            }
        }

        return false;
    }
}
