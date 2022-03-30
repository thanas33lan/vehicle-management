<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class VehicleController extends AbstractActionController
{
    public function indexAction()
    {
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $vehicleService->getVehicleListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else {
            return new ViewModel(array(
                'Vehiclelist' => $vehicleService->getAllActiveVehicle()
            ));
        }
    }

    public function addAction()
    {
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $vehicleService->saveVehicleData($params);
            return $this->_redirect()->toRoute('admin-Vehicle');
        } else {
            return new ViewModel(array(
                'Vehiclelist' => $vehicleService->getAllActiveVehicle()
            ));
        }
    }

    public function editAction()
    {
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $vehicleService->saveVehicleData($param);
            return $this->redirect()->toRoute('admin-Vehicle');
        } else {
            $vehicleId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'Vehiclelist' => $vehicleService->getAllActiveVehicle(),
                'result' => $vehicleService->getVehicleById($vehicleId)
            ));
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $vehicleService = $this->getServiceLocator()->get('VehicleService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $vehicleService->deleteById($param['VehicleId'])))->setTerminal(true);
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $vehicleService = $this->getServiceLocator()->get('VehicleService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $vehicleService->changeStatusById($param)))->setTerminal(true);
        }
    }
}
