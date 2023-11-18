<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Restful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Account extends ResourceController
{
    private $tourist, $tourist_inf, $booking, $hotels, $hotelinfo;
    public function __construct() {
        $this->tourist = new \App\Models\TouristAccount();
        $this->tourist_inf = new \App\Models\TouristInfo();
        $this->booking = new \App\Models\BookingInfo();
        $this->hotels = new \App\Models\HotelsResorts();
        $this->hotelinfo = new \App\Models\HotelInfo();
    }

    public function Register_Tourist()
    {
        $random = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ", 60)), 0, 60);
        $json = $this->request->getJSON();
        $data = [
            'username' => $json->username,
            'email' => $json->email,
            'password' => password_hash($json->password, PASSWORD_DEFAULT),
            'token' => sha1($random),
            'phone' => $json->phone,
            'address' => $json->address,
            'status' => 'UNVERIFIED'
        ];

        $checkdupliemail = $this->tourist->where('email',$data['email'])->First();
        $checkdupliuname = $this->tourist->where('username', $data['username'])->First();
        $checkdupliphone = $this->tourist->where('phone', $data['phone'])->First();

        if($checkdupliemail) {
            return $this->respond(['msg' => 'A user exists with that email. Please change your email.'], 200);
        } else if ($checkdupliuname) {
            return $this->respond(['msg' => 'A user exists with that username. Please change your username.'], 200);
        } else if ($checkdupliphone) {
            return $this->respond(['msg' => 'A user exists with that phone number. Please change your phone number.'], 200);
        } else {
            $reg = $this->tourist->save($data);
            if ($reg) {
                return $this->respond(['msg' => 'Registered Successfully'], 200);
            }else {
                return $this->respond(['msg' => 'An error occurred, Please try again later.'], 200);
            }
        }

    }

    public function Login_Tourist() {

        $random = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ", 60)), 0, 60);
        $newToken = $random;
        if($this->request->getVar('username')) {
            $username = $this->request->getVar('username'); 
        } else {
            $username = '';
        }

        if ($this->request->getVar('email')) {
            $email = $this->request->getVar('email');
        } else {
            $email = '';
        }

        $password = $this->request->getVar('password');

        if($username != '') {
            $data = $this->tourist->where('username', $username)->first(); 
            if($data){ 

                $pass = $data['password']; 
                $authenticatePassword = password_verify($password, $pass); 
                if($authenticatePassword){ 
                    $upd = $this->tourist->set('token',sha1($newToken))->where('id', $data['id'])->update();
                    return $this->respond(['msg' => 'okay', 'token' =>$newToken], 200); 
    
                }else{ 
    
                    return $this->respond(['msg' => 'wrong password.'], 200); 
    
                } 
    
            } else {
                return $this->respond(['msg' => 'no user exists.'], 200); 
            }
        } else {
            $data = $this->tourist->where('email', $email)->first(); 
            if($data){ 

                $pass = $data['password']; 
                $authenticatePassword = password_verify($password, $pass); 
                if($authenticatePassword){ 
                    $upd = $this->tourist->set('token',sha1($newToken))->where('id', $data['id'])->update();
                    return $this->respond(['msg' => 'okay', 'token' =>$newToken], 200); 
    
                }else{ 
    
                    return $this->respond(['msg' => 'wrong password.'], 200); 
    
                } 
    
            } else {
                return $this->respond(['msg' => 'no user exists.'], 200); 
            }
        }

    }

    public function Logout_Tourist() {
        $token = $this->request->getVar('token'); 
        $data = $this->tourist->where('token', sha1($token))->First();
        $random = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ", 60)), 0, 60);
        if($data) {
            $upd = $this->tourist->set('token',sha1($random))->where('id', $data['id'])->update();
            if($upd) {
                return $this->respond(['msg' => 'okay'], 200);
            } else {
                return $this->respond(['msg' => 'an error has occurred.'], 200);
            }
        } else {
            return $this->respond(['msg' => 'an error has occurred.'], 200);
        }


    }

    public function Tourist_Info() {
        $token = $this->request->getVar('token');
        $data = $this->tourist->where('token', sha1($token))->First();
        $check = $this->tourist_inf->where('user_id', $data['id'])->First();
        if ($check){
            $info = [
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'], 
                'address' => $data['address'],
                'first_name' => $check['first_name'],
                'middle_name' => $check['middle_name'],
                'last_name' => $check['last_name'],
                'birthdate' => $check['birthdate'],
                'gender' => $check['gender'],
                'photo' => $check['photo'],
                'msg' => 'okay', 
            ];
            return $this->respond($info, 200); 
        } else {
            $info = [
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'], 
                'address' => $data['address'],
                'first_name' => 'your first name',
                'middle_name' => 'your middle name',
                'last_name' => 'your last name',
                'birthdate' => 'your birthdate',
                'gender' => 'your gender',
                'photo' => '/default.jpg', 
                'msg' => 'okay', 
            ];
            return $this->respond($info, 200);
        }
    }

    public function Tourist_Info_Save() {

        $token = $this->request->getVar('token');
        $data = $this->tourist->select('id')->where('token', sha1($token))->First();
        $check = $this->tourist_inf->where('user_id', $data['id'])->First();
        if ($check) {

            $file = $this->request->getFile('photo');
            $test = $file->move(PUBLIC_PATH.'\\'.$data['id'].'\\');
            $name = $file->getClientPath();
            $path = '/'.$data['id'].'/'.$name;

            if ($test) {
                $data1 = [
                    'first_name' => $this->request->getVar('firstname'),
                    'middle_name' => $this->request->getVar('middlename'),
                    'last_name' => $this->request->getVar('lastname'),
                    'birthdate' => $this->request->getVar('birthdate'),
                    'gender' => $this->request->getVar('gender'),
                    'photo' => $path,
                ];
                $data2 =[
                    'username' => $this->request->getVar('username'),
                    'email' => $this->request->getVar('email'),
                    'phone' => $this->request->getVar('phone'),
                    'address' => $this->request->getVar('address'),
                ];
                $res = $this->tourist_inf->set($data1)->where('user_id', $data['id'])->update();
                $acc = $this->tourist->set($data2)->where('token', sha1($token))->update();
                if ($res && $acc) {
                    return $this->respond(['msg' => 'okay'], 200);
                } else {
                    return $this->respond(['msg' => 'an error has occurred'], 200);
                }

            } else {
                return $this->respond(['msg' => 'Can\'t save file.'], 200);
            }
        } else {
            $file = $this->request->getFile('photo');
            $test = $file->move(PUBLIC_PATH.'\\'.$data['id'].'\\');
            $name = $file->getClientPath();
            $path = '/'.$data['id'].'/'.$name;

            if ($test) {
                $data1 = [
                    'first_name' => $this->request->getVar('firstname'),
                    'middle_name' => $this->request->getVar('middlename'),
                    'last_name' => $this->request->getVar('lastname'),
                    'birthdate' => $this->request->getVar('birthdate'),
                    'gender' => $this->request->getVar('gender'),
                    'photo' => $path,
                    'user_id' => $data['id'],
                ];
                $data2 =[
                    'username' => $this->request->getVar('username'),
                    'email' => $this->request->getVar('email'),
                    'phone' => $this->request->getVar('phone'),
                    'address' => $this->request->getVar('address'),
                ];
                $res = $this->tourist_inf->save($data1);
                $acc = $this->tourist->set($data2)->where('token', sha1($token))->update();
                if ($res && $acc) {
                    return $this->respond(['msg' => 'okay'], 200);
                } else {
                    return $this->respond(['msg' => 'an error has occurred'], 200);
                }

            } else {
                return $this->respond(['msg' => 'Can\'t save file.'], 200);
            }
        }
        
    }
    public function Past_Booking() {
        $token = $this->request->getVar('token');
        $data = $this->tourist->select('tourists_account.id')->join('tourists_info','tourists_info.user_id = tourists_account.id','inner')->where('token', sha1($token))->First();
        $cond = array('tourist_id'=> $data['id'], 'status' => 'COMPLETED');
        $check = $this->booking->select('pax, reservation_date, transaction_date, room_id')->where($cond)->GetAll();
        $getHotelname = $this->join('rooms','rooms.id = booking_info.room_id','inner')->where()->First();
        if($check) {
            return $this->respond($check, 200);
        } else {
            return $this->respond(['msg' => 'an error has occured'], 200);
        }
    }

}
