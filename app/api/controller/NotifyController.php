<?php

namespace app\api\controller;

use app\admin\model\RechargeOrders;
use app\admin\model\User;
use app\admin\model\VipOrders;
use app\api\basic\Base;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use EasyWeChat\MiniApp\Application;
use support\Db;
use support\Request;
use Yansongda\Pay\Pay;

class NotifyController extends Base
{

    protected $noNeedLogin = ['*'];

    function alipay(Request $request)
    {
        $request->setParams('get', ['paytype' => 'alipay']);
        try {
            $this->pay($request);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        return response('success');
    }

    function wechat(Request $request)
    {
        $request->setParams('get', ['paytype' => 'wechat']);
        try {
            $this->pay($request);
        } catch (\Throwable $e) {
            return json(['code' => 'FAIL', 'message' => $e->getMessage()]);
        }
        return json(['code' => 'SUCCESS', 'message' => '成功']);
    }

    function balance(Request $request)
    {
        $request->setParams('get', ['paytype' => 'balance']);
        try {
            $this->pay($request);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        return $this->success();
    }

    /**
     * 接受回调
     * @throws \Throwable
     */
    private function pay(Request $request)
    {
        Db::connection('plugin.admin.mysql')->beginTransaction();
        try {
            $paytype = $request->input('paytype');
            $config = config('payment');
            switch ($paytype) {
                case 'wechat':
                    $pay = Pay::wechat($config);
                    $res = $pay->callback($request->post());
                    $res = $res->resource;
                    $res = $res['ciphertext'];
                    $out_trade_no = $res['out_trade_no'];
                    $attach = $res['attach'];
                    $mchid = $res['mchid'];
                    $transaction_id = $res['transaction_id'];
                    $openid = $res['payer']['openid'] ?? '';

//                    $app = new Application(config('wechat'));
//                    $api = $app->getClient();
//                    $date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('Asia/Shanghai'));
//                    $formatted_date = $date->format('c');
//                    $api->postJson('/wxa/sec/order/upload_shipping_info', [
//                        'order_key' => ['order_number_type' => 1, 'mchid' => $mchid, 'out_trade_no' => $out_trade_no],
//                        'logistics_type' => 3,
//                        'delivery_mode' => 1,
//                        'shipping_list' => [[
//                            'item_desc' => '发货'
//                        ]],
//                        'upload_time' => $formatted_date,
//                        'payer' => ['openid' => $openid]
//                    ]);
                    break;
                case 'alipay':
                    $pay = Pay::alipay($config);
                    $res = $pay->callback($request->post());
                    $out_trade_no = $res->out_trade_no;
                    $attach = $res->passback_params;
                    break;
                case 'balance':
                    $out_trade_no = $request->input('out_trade_no');
                    $attach = $request->input('attach');
                    break;
                default:
                    throw new \Exception('支付类型错误');
            }

            switch ($attach) {
                case 'vip':
                    $order = VipOrders::where(['ordersn' => $out_trade_no, 'status' => 0])->first();
                    if (!$order) {
                        throw new \Exception('订单不存在');
                    }
                    $order->status = 1;
                    $order->pay_time = date('Y-m-d H:i:s');
                    $order->save();
                    //增加用户会员时间
                    if (empty($order->user->vip_expire)) {
                        $order->user->vip_expire = $order->vip_id == 1 ? Carbon::now()->addMonths(1)->format('Y-m-d H:i:s') : ($order->vip_id == 2 ? Carbon::now()->addMonths(3)->format('Y-m-d H:i:s') : Carbon::now()->addYears()->format('Y-m-d H:i:s'));
                    } else {
                        if ($order->user->vip_expire->timestamp >= Carbon::now()->timestamp) {
                            $order->user->vip_expire = $order->vip_id == 1 ? Carbon::parse($order->user->vip_expire)->addMonths(1)->format('Y-m-d H:i:s') : ($order->vip_id == 2 ? Carbon::parse($order->user->vip_expire)->addMonths(3)->format('Y-m-d H:i:s') : Carbon::parse($order->user->vip_expire)->addYears()->format('Y-m-d H:i:s'));
                        } else {
                            $order->user->vip_expire = $order->vip_id == 1 ? Carbon::now()->addMonths(1)->format('Y-m-d H:i:s') : ($order->vip_id == 2 ? Carbon::now()->addMonths(3)->format('Y-m-d H:i:s') : Carbon::now()->addYears()->format('Y-m-d H:i:s'));
                        }
                    }
                    $order->user->save();
                    //给上级反佣金
                    if ($order->user->parent) {
                        User::score(round($order->pay_amount * 0.2), $order->user->parent_id, '开通会员返佣', 'money');
                    }
                    break;
                case 'recharge':
                    $order = RechargeOrders::where(['ordersn' => $out_trade_no, 'status' => 0])->first();
                    if (!$order) {
                        throw new \Exception('订单不存在');
                    }
                    $order->status = 1;
                    $order->pay_time = date('Y-m-d H:i:s');
                    $order->save();
                    $inc_jinbi = $order->pay_amount * 100;
                    //增加用户金币
                    User::score($inc_jinbi, $order->user_id, $order->pay_type_text . '充值', 'money');
                    //给上级反佣金
                    if ($order->user->parent) {
                        User::score(round($inc_jinbi * 0.2), $order->user->parent_id, '充值金币返佣', 'money');
                    }
                    break;
                default:
                    throw new \Exception('回调错误');
            }
            Db::connection('plugin.admin.mysql')->commit();
        } catch (\Throwable $e) {
            Db::connection('plugin.admin.mysql')->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

}
