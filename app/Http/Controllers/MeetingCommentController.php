<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeetingComment;
use App\Models\MeetingComment;
use Illuminate\Http\RedirectResponse;

class MeetingCommentController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(MeetingComment::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreMeetingComment $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreMeetingComment $request): RedirectResponse
    {
        $meeting_comment = MeetingComment::create([
            'user_id' => auth()->id(),
            'meeting_id' => $request->validated()["meeting_id"],
            'comment' => $request->validated()["comment"],
        ]);

        return redirect()->route('meetings.show', ['meeting' => $meeting_comment->meeting_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MeetingComment  $meetingComment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MeetingComment $meetingComment): RedirectResponse
    {
        $meeting_id = $meetingComment->meeting_id;

        $meetingComment->delete();

        return redirect()->route('meetings.show', ['meeting' => $meeting_id]);
    }
}
