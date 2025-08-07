<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Log;


class ScheduleController extends Controller
{
    /**
     * スケジュール一覧を取得（指定月の前後2ヶ月分を含む）
     */
    public function index(Request $request)
    {
        $month = $request->input('month') ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json(['error' => 'Invalid month format'], 400);
        }

        $baseDate = Carbon::parse($month . '-01');
        $start = $baseDate->copy()->subMonths(2)->startOfMonth();
        $end = $baseDate->copy()->addMonths(2)->endOfMonth();

        $schedules = Schedule::where('user_id', auth()->id())
            ->whereBetween('date', [$start, $end])
            ->get();

        $events = $schedules->map(function ($s) {
            $date = $s->date->format('Y-m-d');

            return [
                'id' => $s->id,
                'name' => $s->title,
                'content' => $s->content,
                'start' =>  $date,
                'end' => $date,
                'allDay' => true,
                'color' => '#3a875d',
            ];
        });

        return response()->json($events);
    }

    /**
     * スケジュールを新規登録
     */
    public function store(Request $request)
    {
        $validated = $this->validateSchedule($request);

        Log::debug($request->all());

        $schedule = new Schedule();
        $schedule->fill($validated);
        $schedule->user_id = auth()->id();
        $schedule->save();

        return response()->json([
            'message' => 'スケジュールを登録しました',
            'schedule' => $schedule,
        ]);
    }

    /**
     * スケジュールを更新
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateSchedule($request);

        $schedule = Schedule::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $schedule->update($validated);

        return response()->json([
            'message' => 'スケジュールを更新しました',
            'schedule' => $schedule,
        ]);
    }

    /**
     * スケジュール削除
     */
    public function destroy($id)
    {
        $schedule = Schedule::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $schedule->delete();

        return response()->json([
            'message' => 'スケジュールを削除しました',
        ]);
    }

    /**
     * 入力バリデーションと補正
     */
    private function validateSchedule(Request $request): array
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:1000',
        ]);


        return $validated;
    }
}
