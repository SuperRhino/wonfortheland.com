<?php
namespace App\Controllers;

use Core\BaseController;
use Core\Http\Exception\NotFoundException;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            //'pages' => Page::findMostRecent(6),
        ];

        return $this->view('home.html', $data);
    }
}