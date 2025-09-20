<?php

namespace App\Lib;

use Exception;
use PDO;
use PDOException;

class Currencies
{
    private $db = null;

    private $db_ro = null;

    public $id = null;

    public $code = null;

    private $name = null;

    public $exchange_rate = null;

    public $surcharge_percentage = null;

    public $discount_percentage = null;

    public $last_update = null;

    public function __construct($params = [], $id = 0)
    {

        foreach ($params as $key => $value) {
            $this->$key = $value;
        }

        $this->id = $id;

        if ($this->id > 0) {
            $this->getDetailsById();
        }
    }

    private function getDetailsById()
    {
        try {
            $stmt = $this->db_ro->prepare(
                'SELECT c.*
                FROM currencies c
                WHERE c.id = :id;'
            );
            $stmt->bindValue(':id', $this->id);
            $stmt->execute();
            $result = $stmt->fetch();
        } catch (Exception $e) {
            $this->logException($e);
            return false;
        }

        $this->populateData($result);
    }

    private function populateData($result)
    {
        if (!empty($result)) {
            $this->id = $result['id'];
            $this->code = $result['code'];
            $this->name = $result['name'];
            $this->exchange_rate = $result['exchange_rate'];
            $this->surcharge_percentage = $result['surcharge_percentage'];
            $this->discount_percentage = $result['discount_percentage'];
            $this->last_updated = $result['last_updated'];
        } else {
            $this->id = 0;
        }
    }

    public static function getAllCurrencies()
    {
        try {
            $stmt = $GLOBALS['db_ro']->prepare(
                'SELECT c.*
                FROM currencies c;'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } catch (Exception $e) {
            self::logException($e);
            return false;
        }
    }

    public static function updateCurrencies($code, $rate) 
    {

        try {
            $stmt = $GLOBALS['db']->prepare(
                'UPDATE currencies
                        SET exchange_rate = :rate,
                        last_updated = NOW()
                        WHERE code = :code;'
            );
            $stmt->bindValue(':rate', $rate);
            $stmt->bindValue(':code', $code);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            self::logException($e);
            return false;
        }
    }

    private static function logException(Exception $e)
    {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/error_' . date('Y-m-d') . '.log';

        $logMessage = sprintf(
            "[%s] %s:%d - %s\nStack Trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $e->getTraceAsString()
        );

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}