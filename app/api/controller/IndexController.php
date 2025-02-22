<?php

namespace app\api\controller;

use app\admin\model\Banner;
use app\admin\model\Novel;
use app\admin\model\NovelDetail;
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
        $request->user_id = 1;
        $detail_id = 1;
        $rate = 22;
        $detail = NovelDetail::find($detail_id);
        UsersReadLog::updateOrCreate(['user_id' => $request->user_id, 'novel_id' => $detail->novel_id], ['novel_detail_id' => $detail_id, 'novel_id' => $detail->novel_id, 'user_id' => $request->user_id, 'rate' => $rate]);
        return $this->success('成功');
    }

}
