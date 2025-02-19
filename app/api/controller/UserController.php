<?php

namespace app\api\controller;

use app\admin\model\Classify;
use app\admin\model\Novel;
use app\admin\model\NovelOrders;
use app\admin\model\PlayletOrders;
use app\admin\model\RechargeOrders;
use app\admin\model\Sms;
use app\admin\model\User;
use app\admin\model\UsersBookrack;
use app\admin\model\UsersClass;
use app\admin\model\UsersLayer;
use app\admin\model\UsersPlayletLike;
use app\admin\model\UsersPlayletLog;
use app\admin\model\UsersReadLog;
use app\api\basic\Base;
use app\api\service\Pay;
use Carbon\Carbon;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\Option;
use support\Db;
use support\Request;
use support\Response;
use Tinywan\Jwt\JwtToken;

class UserController extends Base
{
    protected $noNeedLogin = ['login', 'register'];

    function login(Request $request): Response
    {
        $mobile = $request->post('mobile');
        $captcha = $request->post('captcha');
        $password = $request->post('password');
        $code = $request->post('code');
        $login_type = $request->post('login_type');# 1=密码登录 2=微信登陆 3=手机号登录
        if ($login_type == 1) {
            $user = User::where(['mobile' => $mobile])->first();
            if (!$user || !Util::passwordVerify($password, $user->password)) {
                return $this->fail('账户不存在或密码错误');
            }
        } elseif ($login_type == 2) {

            if ($request->client_type == 'app') {
                $config = config('wechat.OpenPlatform');
                $app = new \EasyWeChat\OpenPlatform\Application($config);
                $oauth = $app->getOauth();
                try {
                    $response = $oauth->userFromCode($code);
                } catch (\Throwable $e) {
                    return $this->fail($e->getMessage());
                }
                $openid = $response->getId();
                $wechat_unionid = '';
                $user = User::where(['wechat_unionid' => $wechat_unionid])->first();
            } else {
                //小程序
                $config = config('wechat.MiniApp');
                $app = new \EasyWeChat\MiniApp\Application($config);
                $util = $app->getUtils();
                try {
                    $response = $util->codeToSession($code);
                } catch (\Throwable $e) {
                    return $this->fail($e->getMessage());
                }
                $openid = $response['openid'];
                $wechat_unionid = '';
                $user = User::where(['wechat_unionid' => $wechat_unionid])->first();
            }
            if (!$user) {
                $user = User::create([
                    'nickname' => '用户' . Util::alnum(),
                    'avatar' => '/app/admin/avatar.png',
                    'join_time' => Carbon::now()->toDateTimeString(),
                    'join_ip' => $request->getRealIp(),
                    'invitecode' => Util::generateInvitecode(),
                    'platform_open_id' => $request->client_type == 'app' ? $openid : '',
                    'mini_open_id' => $request->client_type == 'mini' ? $openid : '',
                    'wechat_unionid' => $wechat_unionid,
                ]);
            } else {
                if ($request->client_type == 'app' && empty($user->platform_open_id)) {
                    $user->platform_open_id = $openid;
                }
                if ($request->client_type == 'mini' && empty($user->mini_open_id)) {
                    $user->mini_open_id = $openid;
                }
            }
        } elseif ($login_type == 3) {
            $user = User::where(['mobile' => $mobile])->first();
            if (!$user) {
                return $this->fail('账户不存在');
            }
            $smsResult = Sms::check($mobile, $captcha, 'login');
            if (!$smsResult) {
                return $this->fail('验证码错误');
            }
        } else {
            return $this->fail('登陆方式错误');
        }
        $user->last_time = Carbon::now()->toDateTimeString();
        $user->last_ip = $request->getRealIp();
        $user->save();
        $token = JwtToken::generateToken([
            'id' => $user->id,
            'client' => JwtToken::TOKEN_CLIENT_MOBILE
        ]);
        return $this->success('登陆成功', ['user' => $user, 'token' => $token]);
    }

    function register(Request $request): Response
    {
        $mobile = $request->post('mobile');
        $captcha = $request->post('captcha');
        $password = $request->post('password');
        $password_confirm = $request->post('password_confirm');
        $invitecode = $request->post('invitecode');
        if ($password !== $password_confirm) {
            return $this->fail('两次密码不一致');
        }
        $captchaResult = Sms::check($mobile, $captcha, 'register');
        if (!$captchaResult) {
            return $this->fail('验证码错误');
        }

        if (!empty($invitecode) && !$parent = User::where('invitecode', $invitecode)->first()) {
            return $this->fail('邀请码不存在');
        }
        $user = User::create([
            'nickname' => '用户' . Util::alnum(),
            'avatar' => '/app/admin/avatar.png',
            'join_time' => Carbon::now()->toDateTimeString(),
            'join_ip' => $request->getRealIp(),
            'last_time' => Carbon::now()->toDateTimeString(),
            'last_ip' => $request->getRealIp(),
            'username' => $mobile,
            'mobile' => $mobile,
            'password' => Util::passwordHash($password),
            'parent_id' => isset($parent) ? $parent->id : 0,
            'invitecode' => Util::generateInvitecode(),
        ]);

        if (isset($parent)) {
            // 增加直推关系
            UsersLayer::create([
                'user_id' => $user->id,
                'parent_id' => $parent->id,
                'layer' => 1
            ]);
            // 收集多层关系数据
            $layersToInsert = [];
            UsersLayer::where('user_id', $parent->id)->get()->each(function (UsersLayer $item) use ($user, &$layersToInsert) {
                $layersToInsert[] = [
                    'user_id' => $user->id,
                    'parent_id' => $item->parent_id,
                    'layer' => $item->layer + 1
                ];
            });
            // 批量插入多层关系
            if (!empty($layersToInsert)) {
                UsersLayer::insert($layersToInsert);
            }
        }
        $token = JwtToken::generateToken([
            'id' => $user->id,
            'client' => JwtToken::TOKEN_CLIENT_MOBILE
        ]);
        return $this->success('注册成功', ['user' => $user, 'token' => $token]);
    }



    function getUserInfo(Request $request)
    {
        $user_id = $request->post('user_id');
        if (!empty($user_id)) {
            $request->user_id = $user_id;
        }
        $row = User::find($request->user_id);
        return $this->success('成功', $row);
    }

    function editUserInfo(Request $request)
    {
        $data = $request->post();
        $row = User::find($request->user_id);
        if (!$row) {
            return $this->fail('用户不存在');
        }

        $userAttributes = $row->getAttributes();
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $userAttributes) && (!empty($value) || $value === 0)) {
                $row->setAttribute($key, $value);
            }
        }
        $row->save();
        return $this->success('成功');
    }

    #获取海报
    function getPoster(Request $request)
    {
        $user = User::find($request->user_id);
        if ($request->client_type == 'mini'){
            $config = config('wechat.MiniApp');
            $app = new \EasyWeChat\MiniApp\Application($config);
            $data = [
                'scene' => '1',
                'page' => 'pages/home',
                'width' => 280,
                'check_path' => !config('app.debug'),
            ];
            $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', $data);
            $base64 = "data:image/png;base64,".base64_encode($response->getContent());
        }else{
            $writer = new PngWriter();
            $qrCode = new QrCode(
                data: 'https://longh.top/register/register.html#/?invitecode=' . $user->invitecode,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 100,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );
            $base64 = $writer->write($qrCode)->getDataUri();
        }
        return $this->success('获取成功', ['base64' => $base64, 'invitecode' => $user->invitecode]);
    }


    #浏览历史
    function history(Request $request)
    {
        $type = $request->post('type');#类型  1=书籍   2=短剧
        if ($type == 1){
            $list = UsersReadLog::with(['novel'])->where('user_id', $request->user_id)->orderBy('id', 'desc')->paginate()->items();
            foreach ($list as $item){
                $bookrack = UsersBookrack::where('user_id', $request->user_id)->where('novel_id',$item->novel_id)->exists();
                $item->setAttribute('bookrack_status',$bookrack);
            }
        }else{
            $list = UsersPlayletLog::where('user_id', $request->user_id)->orderBy('id', 'desc')->paginate()->items();
        }
        return $this->success('获取成功', $list);
    }

    #删除历史记录
    function deleteHistory(Request $request)
    {
        $ids = $request->post('ids');
        $type = $request->post('type');#类型  1=书籍   2=短剧
        if ($type == 1){
            UsersReadLog::whereIn('id', $ids)->delete();
        }else{
            UsersPlayletLog::whereIn('id', $ids)->delete();
        }
        return $this->success('删除成功');
    }


    #短剧点赞记录
    function getPlayletLikeList(Request $request)
    {
        // 子查询：获取每个 playlet_id 对应的最大 id
        $subQuery = UsersPlayletLike::select('playlet_id', DB::raw('MAX(id) as max_id'))
            ->where('user_id', $request->user_id)
            ->groupBy('playlet_id');

        $rows = UsersPlayletLike::with('playlet')
            ->whereIn('id', function ($query) use ($subQuery) {
                $query->fromSub($subQuery, 'sub')
                    ->select('max_id');
            })
            ->orderBy('id', 'desc')
            ->paginate()
            ->items();
        return $this->success('获取成功', $rows);
    }

    function getBuyList(Request $request)
    {
        $type = $request->post('type');#类型  1=书籍   2=短剧
        if ($type == 1){
            $rows = NovelOrders::with(['novel'])->where(['user_id' => $request->user_id])->orderBy('id', 'desc')->groupBy('novel_id')->paginate()->items();
        }else{
            $rows = PlayletOrders::with(['playlet'])->where(['user_id' => $request->user_id])->orderBy('id', 'desc')->groupBy('playlet_id')->paginate()->items();
        }
        return $this->success('获取成功', $rows);
    }

    #获取用户内容偏好
    function getUserClass(Request $request)
    {
        $data = [];
        $user_class = UsersClass::where('user_id',$request->user_id)->pluck('class_id')->toArray();
        $data['type_1'] = Classify::where('pid','<>',0)->where('type',1)->get()->each(function ($item)use($user_class){
            if (in_array($item->id,$user_class)){
                $item->setAttribute('exists',true);
            }else{
                $item->setAttribute('exists',false);
            }
        });
        $data['type_2'] = Classify::where('pid','<>',0)->where('type',2)->get()->each(function ($item)use($user_class){
            if (in_array($item->id,$user_class)){
                $item->setAttribute('exists',true);
            }else{
                $item->setAttribute('exists',false);
            }
        });
        $data['type_3'] = Classify::where('pid','<>',0)->where('type',3)->get()->each(function ($item)use($user_class){
            if (in_array($item->id,$user_class)){
                $item->setAttribute('exists',true);
            }else{
                $item->setAttribute('exists',false);
            }
        });
        return $this->success('获取成功', $data);
    }

    #更新用户内容偏好
    function updateUserClass(Request $request)
    {
        $class_ids = $request->post('class_ids');# array [1,2,3]
        $user = User::find($request->user_id);
        $user->class()->sync($class_ids);
        return $this->success('更新成功');
    }




}
