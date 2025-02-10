<?php

namespace app\api\controller;

use app\admin\model\Novel;
use app\admin\model\NovelDetail;
use app\api\basic\Base;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use support\Request;

class NovelController extends Base
{
    #获取小说列表
    function getNovelList(Request $request)
    {
        $sex = $request->post('sex');#性别:1=男,2=女
        $class_id = $request->post('class_id');#分类id
        $tag_ids = $request->post('tag_ids');#标签ids [1,2,3]
        $type = $request->post('type');#类型:1=推荐榜,2=人气榜,3=新书榜,4=完结榜,5=高分榜,6=热搜榜,7=会员榜
        $keyword = $request->post('keyword');#关键字
        $text_num = $request->post('text_num');#文字数:0=不限,1=10万字以内,2=30万字以内,3=50万字以内,4=30万字以上,5=50万字以上
        $creation_status = $request->post('creation_status');#作品状态:0=不限,1=完结,2=半年内完结,3=连载中,4=3日内更新,5=7日内更新,6=1月内更新
        $tag_count = count($tag_ids);
        if ($tag_count >= 4){
            return $this->fail('标签不能超过4个');
        }
        $rows = Novel::
        when(!empty($tag_ids), function (Builder $builder) use ($tag_ids) {
            $builder->where(function ($query) use ($tag_ids) {
                foreach ($tag_ids as $tagId) {
                    $query->whereHas('tags', function ($query) use ($tagId) {
                        $query->where('id', $tagId);
                    });
                }
            });
        })
            ->when(!empty($creation_status), function (Builder $builder) use ($creation_status) {
                if ($creation_status == 1) {
                    $builder->where('creation_status', 1);
                }
                if ($creation_status == 2) {
                    $builder->where('finish_time', '>', Carbon::now()->subMonths(6));
                }
                if ($creation_status == 3) {
                    $builder->where('creation_status', 0);
                }
                if ($creation_status == 4) {
                    $builder->where('end_time', '>', Carbon::now()->subDays(3));
                }
                if ($creation_status == 5) {
                    $builder->where('end_time', '>', Carbon::now()->subDays(7));
                }
                if ($creation_status == 6) {
                    $builder->where('end_time', '>', Carbon::now()->subMonths(1));
                }
            })
            ->when(!empty($text_num), function (Builder $builder) use ($text_num) {
                if ($text_num == 1) {
                    $builder->where('text_num', '<=', 100000);
                }
                if ($text_num == 2) {
                    $builder->where('text_num', '<=', 300000);
                }
                if ($text_num == 3) {
                    $builder->where('text_num', '<=', 500000);
                }
                if ($text_num == 4) {
                    $builder->where('text_num', '>=', 300000);
                }
                if ($text_num == 5) {
                    $builder->where('text_num', '>=', 500000);
                }
            })
            ->when(!empty($keyword), function (Builder $query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')->orWhere('name', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($sex), function (Builder $query) use ($sex) {
                $query->where('sex', $sex);
            })
            ->when(!empty($class_id), function (Builder $query) use ($class_id) {
                $query->where('class_id', $class_id);
            })
            ->when(!empty($type), function (Builder $query) use ($type) {
                if ($type == 1) {
                    $query->orderByDesc('read_num');
                }
                if ($type == 2) {
                    $query->orderByDesc('hot');
                }
                if ($type == 3) {
                    $query->where('created_at', '>', Carbon::now()->subDays(7))->orderByDesc('hot');
                }
                if ($type == 4) {
                    $query->where('creation_status', 1)->orderByDesc('read_num');
                }
                if ($type == 5) {
                    $query->orderByDesc('score');
                }
                if ($type == 6) {
                    $query->orderByDesc('search_num');
                }
                if ($type == 7) {
                    $query->where('vip', 1)->orderByDesc('read_num');
                }
            })
            ->paginate()
            ->items();
        return $this->success('成功', $rows);
    }

    #获取小说详情
    function getNovelDetail(Request $request)
    {
        $novel_id = $request->post('novel_id');
        $novel = Novel::find($novel_id);
        return $this->success('成功', $novel);
    }

    #章节列表
    function getChapterList(Request $request)
    {
        $novel_id = $request->post('novel_id');
        $rows = NovelDetail::where('novel_id', $novel_id)->paginate()->items();
        return $this->success('成功', $rows);
    }

    #章节详情
    function getChapterDetail(Request $request)
    {
        $detail_id = $request->post('detail_id');
        $detail = NovelDetail::find($detail_id);
        return $this->success('成功', $detail);
    }


}
