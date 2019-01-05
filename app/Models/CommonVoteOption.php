<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CommonVoteOption extends Model
{
    protected $table = 'common_vote_option';
    //

    public static function store($voteId, Request $request)
    {
        $fields = $request->only(['name', 'phone', 'email', 'xuanyan']);

        $vote = CommonVote::find($voteId);

        if (time() < strtotime($vote->start_at)) {
            return '活动还未开始';
        }
        if (time() > strtotime($vote->end_at)) {
            return '感谢您的关注，活动已结束';
        }


        if (!$request->hasFile('image')) {
            return '请上传图片再提交!';
        }
        $file = $request->file('image');
        if ($file->getSize() > 3*1024*1024) {
            return '图片大小请控制在3M以内!';
        }
        $filePath = '/'.$file->store('uploads');

        $last = CommonVoteOption::where('vote_id', $voteId)
            ->orderBy('id', 'desc')
            ->first();

        $voteNum = $vote->wx_vote_number . '0001';

        if ($last) {
            $voteNum = intval($last->vote_num) + 1;
        }

        $user = new CommonVoteOption();
        $user->name = $fields['name'];
        $user->phone = $fields['phone'];
        $user->email = $fields['email'];
        $user->xuanyan = $fields['xuanyan'];
        $user->vote_id = $voteId;
        $user->vote_num = $voteNum;
        $user->img = $filePath;
        if (!$user->save()) {
            return '报名失败';
        }

        return 0;
    }
}
