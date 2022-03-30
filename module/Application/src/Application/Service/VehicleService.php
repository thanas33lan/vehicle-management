<?php

namespace Application\Service;

use Zend\Session\Container;
use Exception;
use Zend\Db\Sql\Sql;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class VehicleBrandsService
{

    public $sm = null;

    public function __construct($sm = null)
    {
        $this->sm = $sm;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    public function saveVehicleData($params)
    {

        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $vehicleDb = $this->sm->get('VehicleBrandsTable');
            $vehicleId = $vehicleDb->saveVehicleDetails($params);
            if ($vehicleId > 0) {
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Vehicle detail saved successfully';
            }
        } catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function getVehicleListInGrid($parameters)
    {
        $vehicleDb = $this->sm->get('VehicleBrandsTable');
        $acl = $this->sm->get('AppAcl');
        return $vehicleDb->fetchVehicleListInGrid($parameters, $acl);
    }

    public function getAllActiveVehicle()
    {
        $vehicleDb = $this->sm->get('VehicleBrandsTable');
        return $vehicleDb->fetchAllActiveVehicle();
    }

    public function getVehicleById($id)
    {
        $vehicleDb = $this->sm->get('VehicleBrandsTable');
        return $vehicleDb->fetchVehicleById($id);
    }

    public function deleteById($id)
    {
        $vehicleDb = $this->sm->get('VehicleBrandsTable');
        return $vehicleDb->deleteByVehicleId($id);
    }

    public function changeStatusById($id)
    {
        $vehicleDb = $this->sm->get('VehicleBrandsTable');
        return $vehicleDb->changeStatusByVehicleId($id);
    }
}
