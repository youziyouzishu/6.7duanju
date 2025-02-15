<?php

namespace app\api\controller;

use app\admin\model\NovelDetail;
use app\admin\model\Playlet;
use app\admin\model\PlayletDetail;
use app\admin\model\UsersPlayletFollow;
use app\admin\model\UsersPlayletLike;
use app\admin\model\UsersPlayletLog;
use app\admin\model\UsersReadLog;
use app\api\basic\Base;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use support\Request;

class PlayletController extends Base
{


    #获取列表
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


    #获取详情
    function getPlayletDetail(Request $request)
    {
        $playlet_id = $request->post('playlet_id');
        $row = Playlet::with(['tags'])->withCount(['detail'])->find($playlet_id);
        $log = UsersPlayletLog::where('user_id',$request->user_id)->where('playlet_id',$playlet_id)->first();
        if ($log){
            $row->setAttribute('video', $log->playletDetail);
        }else{
            $row->setAttribute('video', $row->detail()->orderBy('index')->first());
        }
        return $this->success('成功',$row);
    }

    function getSerieList(Request $request)
    {
        $playlet_id = $request->post('playlet_id');
        $row = Playlet::find($playlet_id);
        $rows = $row->detail()->orderBy('index')->get();
        $follow_status = UsersPlayletFollow::where('user_id',$request->user_id)->where('playlet_id',$playlet_id)->exists();
        foreach ($rows as $detail){
            $detail->setAttribute('follow_status', $follow_status);
            $detail->setAttribute('like_status',UsersPlayletLike::where('user_id',$request->user_id)->where('playlet_detail_id',$detail->id)->exists());
        }
        return $this->success('成功',$rows);
    }

    function getSerieDetail(Request $request)
    {
        $detail_id = $request->post('detail_id');
        $row = PlayletDetail::find($detail_id);
        $row->increment('play_num');
        $row->playlet()->increment('play_num');
        return $this->success('成功',$row);
    }

    #同步短剧观看进度
    function updatePlayletLog(Request $request)
    {
        $detail_id = $request->post('detail_id');
        $rate = $request->post('rate');
        $detail = PlayletDetail::find($detail_id);
        UsersPlayletLog::updateOrCreate(['user_id' => $request->user_id, 'playlet_id' => $detail->playlet_id], ['playlet_detail_id' => $detail_id, 'playlet_id' => $detail->playlet_id, 'user_id' => $request->user_id, 'rate' => $rate]);
        return $this->success('成功');
    }

    #点赞
    function like(Request $request)
    {
        $detail_id = $request->post('detail_id');
        $detail = PlayletDetail::find($detail_id);
        $row = UsersPlayletLike::where('user_id', $request->user_id)->where('playlet_detail_id', $detail->id)->first();
        if ($row) {
            $row->playlet()->decrement('like_num');
            $row->playletDetail()->increment('like_num');
            $row->delete();
            $result = false;
        } else {

            $row = UsersPlayletLike::create(['user_id' => $request->user_id, 'playlet_detail_id' => $detail->id,'playlet_id' => $detail->playlet_id]);
            $row->playlet()->increment('like_num');
            $row->playletDetail()->increment('like_num');
            $result = true;
        }
        return $this->success('成功', $result);
    }

    #追剧
    function follow(Request $request)
    {
        $playlet_id = $request->post('playlet_id');
        $row = UsersPlayletFollow::where('user_id', $request->user_id)->where('playlet_id', $playlet_id)->first();
        if ($row) {
            $row->delete();
            $result = false;
        } else {
            UsersPlayletFollow::create(['user_id' => $request->user_id, 'playlet_id' => $playlet_id]);
            $result = true;
        }
        return $this->success('成功', $result);
    }








}
