<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index()
    {
        $content = view('admin/Dashboard');
        return view('admin/layout', [
            'content' => $content,
            'title' => 'Dashboard',
        ]);
    }
}

