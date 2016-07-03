<?php
namespace App\Controllers;

use App\Models\Archive;
use Core\BaseController;
use Core\Http\Exception\NotFoundException;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'pages' => Archive::findMostRecent(9),
        ];

        return $this->view('home.html', $data);
    }
}