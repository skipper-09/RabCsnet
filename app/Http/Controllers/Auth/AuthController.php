<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(){
        $data = [
            'tittle'=>'Login',
        ];
        return view('pages.auth.login',$data);
    }
}
