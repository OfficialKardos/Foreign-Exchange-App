<?php

namespace App\Lib;

use Exception;
use PDO;
use PDOException;

class Orders
{
    private $db = null;

    private $db_ro = null;

    public $id = null;

    public $currency_id = null;

    public $exchange_rate = null;

    public $surcharge_percentage = null;

    public $discount_percentage = null;

    public $foreign_amount = null;

    public $zar_amount = null;

    public $surcharge_amount = null;

    public $created_at = null;

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
                'SELECT o.*
                FROM orders o
                WHERE o.id = :id;'
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
            $this->currency_id = $result['currency_id'];
            $this->exchange_rate = $result['exchange_rate'];
            $this->surcharge_percentage = $result['surcharge_percentage'];
            $this->discount_percentage = $result['discount_percentage'];
            $this->foreign_amount = $result['foreign_amount'];
            $this->zar_amount = $result['zar_amount'];
            $this->surcharge_amount = $result['surcharge_amount'];
            $this->created_at = $result['created_at'];
        } else {
            $this->id = 0;
        }
    }

    public function create() 
    {

        if ($this->id != null) {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO `currencies`.`orders`
                (currency_id,
                 exchange_rate,
                 surcharge_percentage,
                 discount_percentage,
                 foreign_amount,
                 zar_amount,
                 surcharge_amount)
                VALUES
                (:currency_id,
                 :exchange_rate,
                 :surcharge_percentage,
                 :discount_percentage,
                 :foreign_amount,
                 :zar_amount,
                 :surcharge_amount);'
            );
            $stmt->bindValue(':currency_id', $this->currency_id);
            $stmt->bindValue(':exchange_rate', $this->exchange_rate);
            $stmt->bindValue(':surcharge_percentage', $this->surcharge_percentage);
            $stmt->bindValue(':discount_percentage', $this->discount_percentage);
            $stmt->bindValue(':foreign_amount', $this->foreign_amount);
            $stmt->bindValue(':zar_amount', $this->zar_amount);
            $stmt->bindValue(':surcharge_amount', $this->surcharge_amount);
            if ($stmt->execute()) {
                $this->id = $GLOBALS['db']->lastInsertId();
                return $this->id;
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