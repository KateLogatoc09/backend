<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Restful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MainModel;

class MainController extends ResourceController
{
    public function index()
    {
        //
    }
    public function getData()
    {
        $main = new MainModel();
        $data = $main->find();
        return $this->respond($data, 200);
    }
    public function save(){
        $json = $this->request->getJSON();
        $data = [
            'name' => $json->name,
            'logo' => $json->logo,
            'about' => $json->about,
            'DateAdded' => $json->DateAdded,
        ];
        $main = new MainModel();
        $r = $main->save($data);
        return $this->respond($r, 200);
    }
    public function del(){
        $json = $this->request->getJSON();
        $id = $json->id;
        $main = new MainModel();
        $r = $main->delete($id);
        return $this->respond($r, 200);
    }
}
