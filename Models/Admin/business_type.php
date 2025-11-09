<?php

class business_typeModel extends Mysql
{
    private int $idBusinessType;
    private string $name;
    private string $description; // Permitir que la descripción pueda ser NULL
    private string $status;

    public function __construct()
    {
        parent::__construct();
    }
}