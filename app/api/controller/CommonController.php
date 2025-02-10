<?php

namespace app\api\controller;

use app\admin\model\Banner;
use app\admin\model\Classify;
use app\admin\model\Vip;
use app\api\basic\Base;
use Illuminate\Database\Eloquent\Builder;
use Intervention\Image\Gd\Driver;

use Intervention\Image\ImageManager;
use support\Request;

class CommonController extends Base
{
    protected $noNeedLogin = ['*'];

    #获取轮播图
    function getBannerList(Request $request)
    {
        $type = $request->post('type');#板块:1=推荐,2=短剧,3=男生,4=女生
        $rows = Banner::where(['type'=>$type])->orderByDesc('weigh')->get();
        return $this->success('成功',$rows);
    }

    #获取vip列表
    function getVipList(Request $request)
    {
        $rows = Vip::all();
        return $this->success('成功',$rows);
    }


    #获取分类
    function getClass(Request $request)
    {
        $type = $request->post('type');#类型:1=男生,2=女生,3=短剧分类,4=推荐分类
        $rows = Classify::when(!empty($type),function (Builder $builder)use($type){
            if ($type == 4){
                $builder->where('pid','<>',0)->whereIn('type',[1,2]);
            }else{
                $builder->with(['children'])->where('pid',0)->where(['type'=>$type]);
            }
        })->orderByDesc('weigh')->get();
        return $this->success('成功',$rows);
    }

}
