<?php

namespace app\api\controller;

use app\admin\model\Banner;
use app\admin\model\Novel;
use app\admin\model\NovelDetail;
use app\admin\model\NovelOrders;
use app\admin\model\SystemNotice;
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
        $novel_id = 1;
        $request->user_id = 1;
        $novel = Novel::find($novel_id);
        if (!$novel) {
            return $this->fail('小说不存在');
        }
        // 查询小说的所有章节
        $chapters = NovelDetail::where('novel_id', $novel_id)->orderBy('index')->get();
        $lock = NovelOrders::where('user_id', $request->user_id)
            ->where('novel_id', $novel_id)
            ->where('type', 2)
            ->exists();#整本
        // 批量查询用户是否购买了各个章节
        $purchasedChapters = NovelOrders::where('user_id', $request->user_id)
            ->where('novel_id', $novel_id)
            ->where('type', 1)
            ->pluck('novel_detail_id')
            ->toArray();

        // 将每30章节算一个集合
        $rows = $chapters->chunk(30)->map(function ($chunk) use ($purchasedChapters, $lock) {
            $chunk->each(function ($chapter) use (&$purchasedChapters, &$lock) {
                $chapter->setAttribute('lock', !($lock || in_array($chapter->id, $purchasedChapters) || $chapter->price == 0));
            });

            $startIndex = $chunk->first()->index;
            $endIndex = $chunk->last()->index;
            return [
                'title' => "第{$startIndex}章 - 第{$endIndex}章",
                'children' => $chunk->toArray()
            ];
        });
        return $this->success('成功', $rows);
    }

}
