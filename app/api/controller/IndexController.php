<?php

namespace app\api\controller;

use app\admin\model\Banner;
use app\admin\model\Novel;
use app\admin\model\NovelDetail;
use app\admin\model\NovelOrders;
use app\admin\model\Playlet;
use app\admin\model\SystemNotice;
use app\admin\model\UsersReadLog;
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
