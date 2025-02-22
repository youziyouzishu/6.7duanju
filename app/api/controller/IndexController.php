<?php

namespace app\api\controller;

use app\admin\model\Banner;
use app\api\basic\Base;
use Illuminate\Support\Lottery;
use support\Db;
use support\Request;

class IndexController extends Base
{
    protected $noNeedLogin = ['*'];
    function index(Request $request)
    {

    }

}
