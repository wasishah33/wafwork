<?php

namespace App\Controllers;

use WAFWork\Http\Controller;
use WAFWork\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return $this->view('home');
    }
} 