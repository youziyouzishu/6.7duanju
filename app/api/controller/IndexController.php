<?php

namespace app\api\controller;

use app\admin\model\Novel;
use app\admin\model\User;
use app\api\basic\Base;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use support\Request;

class IndexController extends Base
{
    protected $noNeedLogin = ['*'];
    function index(Request $request)
    {
        $userinfo = User::where(['mobile'=>'11111','type'=>22222])->first();
    }

}
