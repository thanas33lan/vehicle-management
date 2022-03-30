<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class VehicleBrandsController extends AbstractActionController
{
    public function indexAction()
    {
        $vehicleBrandsService = $this->getServiceLocator()->get('VehicleBrandsService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $vehicleBrandsService->getVehicleListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else {
            return new ViewModel(array(
                'vehiclelist' => $vehicleBrandsService->getAllActiveVehicleBrands()
            ));
        }
    }

    public function addAction()
    {
        $vehicleBrandsService = $this->getServiceLocator()->get('VehicleBrandsService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $vehicleBrandsService->saveVehicleData($params);
            return $this->_redirect()->toRoute('admin-vehicle-brands');
        } else {
            return new ViewModel(array(
                'vehiclelist' => $vehicleBrandsService->getAllActiveVehicleBrands()
            ));
        }
    }

    public function editAction()
    {
        $vehicleBrandsService = $this->getServiceLocator()->get('VehicleBrandsService');
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $vehicleBrandsService->saveVehicleData($param);
            return $this->redirect()->toRoute('admin-vehicle-brands');
        } else {
            $vbId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'vehiclelist' => $vehicleBrandsService->getAllActiveVehicleBrands(),
                'result' => $vehicleBrandsService->getVehicleById($vbId)
            ));
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $vehicleBrandsService = $this->getServiceLocator()->get('VehicleBrandsService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $vehicleBrandsService->deleteById($param['vbId'])))->setTerminal(true);
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $vehicleBrandsService = $this->getServiceLocator()->get('VehicleBrandsService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $vehicleBrandsService->changeStatusById($param)))->setTerminal(true);
        }
    }
}
