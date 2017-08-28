<?php
namespace app\back\controller;

use think\Controller;

/*
*
*
*
*/
class Manage extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}