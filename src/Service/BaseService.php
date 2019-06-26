<?php


namespace Service;


class BaseService
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
}