<?php

namespace app\api\controller;

use app\admin\model\Playlet;
use app\api\basic\Base;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use support\Request;

class PlayletController extends Base
{

    function getPlayletList(Request $request)
    {
        $type = $request->post('type');#类型:1=本周热门 2=本周排行 3=最近热播
        $tag_ids = $request->post('tag_ids');#标签ids [1,2,3]
        $rows = Playlet::where('status',1)
            ->withCount('detail')
            ->when(!empty($type),function (Builder $builder)use($type){
                if ($type == 1){
                    // 获取本周的开始和结束日期
                    $startOfWeek = Carbon::now()->startOfWeek();
                    $endOfWeek = Carbon::now()->endOfWeek();
                        $builder->whereBetween('created_at', [$startOfWeek, $endOfWeek])->orderByDesc('like_num');
                }
                if ($type == 2){
                    // 获取本周的开始和结束日期
                    $startOfWeek = Carbon::now()->startOfWeek();
                    $endOfWeek = Carbon::now()->endOfWeek();
                    $builder->whereBetween('created_at', [$startOfWeek, $endOfWeek])->orderByDesc('hot');
                }
                if ($type == 3){
                    $builder->orderByDesc('id');
                }
            })
            ->when(!empty($tag_count), function (Builder $builder) use ($tag_ids) {
                $builder->whereHas('tags', function ($query) use ($tag_ids) {
                    $query->whereIn('wa_classify.id', $tag_ids);
                });
            })
            ->paginate()
            ->items();
        return $this->success('成功',$rows);
    }


    function getPlayletDetail(Request $request)
    {
        $playlet_id = $request->post('playlet_id');
        $row = Playlet::find($playlet_id);
        return $this->success('成功',$row);
    }

}
