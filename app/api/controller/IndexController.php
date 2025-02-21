<?php

namespace app\api\controller;

use app\admin\model\Novel;
use app\admin\model\Playlet;
use app\admin\model\User;
use app\api\basic\Base;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use support\Request;
use Tinywan\Validate\Helper\Str;

class IndexController extends Base
{
    protected $noNeedLogin = ['*'];
    function index(Request $request)
    {

        $row = User::find(1);
        $row->created_at->addDays(7);

        dump();
//        $client = new Client();
//        $response = $client->get( 'https://image.sinajs.cn/newchart/min/n/sh603000.gif', [
//            'headers' => [
//                'user-agent'=>'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1'
//            ]
//        ]);
//        $response = $response->getBody()->getContents();
//        dump(base64_encode($response));
    }

}
