<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Restful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Listing extends ResourceController
{
    private $hotels, $rating;

    public function __construct() {
        $this->hotels = new \App\Models\HotelsResorts();
        $this->rating = new \App\Models\Ratings();
    }

    public function ListAll() {
        $data = $this->hotels->orderBy('name')->FindAll();
        return $this->respond($data, 200);
    }

    public function Ratings() {
        $data = $this->rating->selectMax('ratings.account_id')->select('name')->selectAvg('rating')->join('hotels_resorts','hotels_resorts.account_id = ratings.account_id','inner')->groupBy('name')->orderBy('name')->FindAll();
        return $this->respond($data, 200);
    }


}
