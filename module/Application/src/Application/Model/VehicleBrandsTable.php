<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;


class VehicleBrandsTable extends AbstractTableGateway
{

    protected $table = 'vehicles_brands';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
    public function fetchAllState()
    {
        return $this->select(array('vb_status' => 'active'))->toArray();
    }

    public function saveVehicleDetails($params)
    {

        # save Vehicle details code...
        $common = new CommonService();
        $sessionLogin = new Container('user');
        $data = array(
            'vb_name'     => $params['name'],
            'vb_slug'     => $params['slug'],
            'description' => $params['description'],
            'parent_id'   => (isset($params['parentId']) && $params['parentId'] != '') ? base64_decode($params['parentId']) : null,
            'vb_status'   => $params['status']
        );
        /* \Zend\Debug\Debug::dump($data);
        die; */
        if (isset($params['vbId']) && $params['vbId'] != '') {
            $data['modified_on'] = $common->getDateTime();
            $data['modified_by'] = $sessionLogin->userId;
            $this->update($data, array('vb_id' => base64_decode($params['vbId'])));
            $lastInsertId = base64_decode($params['vbId']);
        } else {
            $data['created_on'] = $common->getDateTime();
            $data['created_by'] = $sessionLogin->userId;
            $this->insert($data);
            $lastInsertId = $this->lastInsertValue;
        }


        if (isset($_FILES['image']['name']) && trim($_FILES['image']['name']) != '') {
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "Vehicle") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "Vehicle")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "Vehicle", 0777);
            }

            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "Vehicle" . DIRECTORY_SEPARATOR . "vehicle_" . $lastInsertId;
            if (!file_exists($pathname) && !is_dir($pathname)) {
                mkdir($pathname, 0777);
            }
            $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = $common->generateRandomString(4, 'alphanum') . "." . $extension;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $pathname . DIRECTORY_SEPARATOR . $imageName)) {
                $this->update(array('image' => $imageName), array("vb_id" => $lastInsertId));
            }
        }

        return $lastInsertId;
    }

    public function fetchAllActiveVehicleBrands()
    {
        # fetching active Vehicle list...
        $vehicleList = $this->select(array('vb_status' => 'active'))->toArray();
        $data = array();
        foreach ($vehicleList as $aRow) {
            $vehicleName = array();
            if ($aRow['parent_id'] > 0) {
                $parentId = $aRow['parent_id'];
                do {
                    $result = $this->select(array('vb_id' => $parentId))->current();
                    if ($result) {
                        $vehicleName[] =  ucwords($result['vb_name']);
                        $parentId = $result['parent_id'];
                    }
                } while ($result && $result['parent_id'] == 0);
            }
            $vehicleName[] = ucwords($aRow['vb_name']);
            $data[] = array(
                'vb_id'   => $aRow['vb_id'],
                'vb_name' => implode(' -> ', $vehicleName),
                'vb_slug' => $aRow['vb_slug'],
                'description'   => $aRow['description'],
                'parent_id'     => $aRow['parent_id']
            );
        }
        // \Zend\Debug\Debug::dump($data);
        return $data;
    }

    public function deleteByvbId($id)
    {
        # remove the Vehicle...
        return $this->delete(array('vb_id' => base64_decode($id)));
    }

    public function changeStatusByvbId($params)
    {
        # change the status of the Vehicle...
        return $this->update(array('vb_status' => $params['status']), array('vb_id' => base64_decode($params['id'])));
    }

    public function fetchVehicleListInGrid($parameters, $acl)
    {
        $aColumns = array('vb_name', 'description', 'vb_status');
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . " " . ($parameters['sSortDir_' . $i]) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);

                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }
        # Query
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from($this->table);

        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }

        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }

        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $iTotal = $this->select()->count();


        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        $sessionLogin = new Container('user');
        $role = $sessionLogin->roleCode;
        if ($acl->isAllowed($role, 'Admin\Controller\VehicleBrands', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        /* if ($acl->isAllowed($role, 'Admin\Controller\VehicleBrands', 'delete')) {
            $delete = true;
        } else {
            $delete = false;
        } */
        if ($acl->isAllowed($role, 'Admin\Controller\VehicleBrands', 'change-status')) {
            $changeStatus = true;
        } else {
            $changeStatus = false;
        }

        foreach ($rResult as $key => $aRow) {
            $row = array();
            $updateLink = '';
            $changeStatusLink = '';
            $deleteLink = '';
            $vehicleName = array();
            $result = false;
            if ($aRow['parent_id'] > 0) {
                $parentId = $aRow['parent_id'];
                do {
                    $result = $this->select(array('vb_id' => $parentId))->current();
                    if ($result) {
                        $vehicleName[] =  ucwords($result['vb_name']);
                        $parentId = $result['parent_id'];
                    }
                } while ($result && $result['parent_id'] == 0);
            }
            $vehicleName[] = ucwords($aRow['vb_name']);
            $row[] = implode(' <i class="fa fa-arrow-right"></i> ', $vehicleName);
            $row[] = $aRow['description'];
            $row[] = ucwords($aRow['vb_status']);
            if ($update) {
                $updateLink = '<a href="/admin/vehicle-brands/edit/' . base64_encode($aRow['vb_id']) . '" class="btn btn-sm btn-outline-info" style="margin-left: 2px;" title="Edit Vehicle of ' . ucwords($aRow['vb_name']) . '"><i class="far fa-edit"></i> Edit</a>';
            }
            /* if($delete){
                $deleteLink = '<a href="javascript:void(0);" onclick="deleteVehicle(\''.base64_encode($aRow['vb_id']).'\')" class="btn btn-sm btn-outline-danger" style="margin-left: 2px;" title="Delete Vehicle of '.ucwords($aRow['vb_name']).'"><i class="far fa-trash-alt"></i> Delete</a>';
            } */
            if ($changeStatus) {
                $statusTxt = (isset($aRow['vb_status']) && $aRow['vb_status'] == 'active') ? "inactive" : "active";
                $changeStatusLink = '<a href="javascript:void(0);" onclick="changeStatus(\'' . base64_encode($aRow['vb_id']) . '\',\'' . $statusTxt . '\')" class="btn btn-sm btn-outline-dark" style="margin-left: 2px;" title="Change status of ' . ucwords($aRow['vb_name']) . '"><i class="fa fa-exchange-alt"></i> Change Status</a>';
            }
            if ($changeStatus || $delete || $update) {
                $row[] = $updateLink . $deleteLink . $changeStatusLink;
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchVehicleById($id)
    {
        # fetch Vehicle by id...
        return $this->select(array('vb_id' => $id))->current();
    }

    public function fetchVehicleByIdInLinked($id)
    {
        # fetch Vehicle by id...
        $result =  $this->select(array('vb_id' => $id))->current();
        if ($result['parent_id'] > 0) {
            $name = array();
            $parentId = $result['parent_id'];
            do {
                $result = $this->select(array('id' => $parentId))->current();
                if ($result) {
                    $name[] =  ucwords($result['vb_name']);
                    $parentId = $result['parent_id'];
                }
            } while ($result && $result['parent_id'] == 0);
        } else {
            return array(
                'name'          => $result['vb_name'],
                'slug'          => $result['vb_slug'],
                'description'   => $result['description'],
                'image'         => $result['image']
            );
        }
    }
}
