<?php

namespace app\api\controller;

use app\admin\model\Novel;
use app\admin\model\User;
use app\api\basic\Base;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use support\Request;
use Tinywan\Validate\Helper\Str;

class IndexController extends Base
{
    protected $noNeedLogin = ['*'];
    function index(Request $request)
    {
        dump($request->post());
        $request->setParams('post',['aaa'=>11]);
        dump($request->post());


        return $this->success();
    }

}
