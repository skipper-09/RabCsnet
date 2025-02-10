<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhpInfoController extends Controller
{
    public function showInfo()
    {
      
        ob_start(); 
        phpinfo();  
        $phpInfo = ob_get_clean(); 
        // dd($phpInfo);

        $maxExecutionTime = ini_get('max_execution_time');
      
        return view('phpinfo', compact('phpInfo', 'maxExecutionTime'));
    }
}
