<?php

namespace app\api\controller;

use app\admin\model\RechargeOrders;
use app\admin\model\UsersScoreLog;
use app\api\basic\Base;
use app\api\service\Pay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\common\Util;
use support\Request;

class MoneyController extends Base
{
    function recharge(Request $request)
    {
        $amount = $request->post('amount');
        $pay_type = $request->post('pay_type');#支付方式:1=微信,2=支付宝
        $ordersn = Util::generateOrdersn();
        RechargeOrders::create([
            'user_id' => $request->user_id,
            'pay_type' => $pay_type,
            'ordersn' => $ordersn,
            'pay_amount' => $amount,
        ]);
        try {
            $result = Pay::pay($pay_type, $amount, $ordersn, '充值金币', 'recharge');
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        return $this->success('成功', $result);
    }

    function getMoneyLog(Request $request)
    {
        $type = $request->post('type');
        $date = $request->post('date');
        $status = $request->post('status'); #0=全部 1=支出，2=收入
        $date = Carbon::parse($date);
        // 提取年份和月份
        $year = $date->year;
        $month = $date->month;
        $rows = UsersScoreLog::where(['type' => $type])
            ->when(!empty($status), function (Builder $query) use ($status) {
                if ($status == 1) {
                    $query->where('score', '<', 0);
                } else {
                    $query->where('score', '>', 0);
                }
            })
            ->whereYear('created_at',$year)
            ->whereMonth('created_at',$month)
            ->where('user_id', $request->user_id)
            ->orderByDesc('id')
            ->paginate()
            ->getCollection()
            ->each(function (UsersScoreLog $item) {
                if ($item->score > 0) {
                    $item->score = '+' . $item->score;
                }
            });
        return $this->success('获取成功', $rows);
    }

}
