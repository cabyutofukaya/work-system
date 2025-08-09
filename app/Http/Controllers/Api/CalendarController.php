<?php

namespace App\Http\Controllers\Api;

use App\Models\Calendar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Log;


class CalendarController extends Controller
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

        $selectUser = $request->input('user_id') ?? 1;
        $categoryColors = config('calendar.category');

        $calendars = Calendar::where('user_id', $selectUser)
            ->whereBetween('date', [$start, $end])
            ->get();

        $events = $calendars->map(function ($s) use ($categoryColors){

            $date = $s->date->format('Y-m-d');

            return [
                'id' => $s->id,
                'title' => $s->title,
                'content' => $s->content,
                'start' =>  $date,
                'end' =>  $date,
                'allDay' => true,
                'color' => $categoryColors[$s->category] ?? '',
                'category' => $s->category,
                'user_id' => $s->user_id,
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

        $calendar = new Calendar();
        $calendar->fill($validated);
        $calendar->user_id = auth()->id();
        $calendar->save();

        return response()->json([
            'message' => 'スケジュールを登録しました',
            'calendar' => $calendar,
        ]);
    }

    /**
     * スケジュールを更新
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateSchedule($request);

        $calendar = Calendar::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $calendar->update($validated);

        return response()->json([
            'message' => 'スケジュールを更新しました',
            'calendar' => $calendar,
        ]);
    }

    /**
     * スケジュール削除
     */
    public function destroy($id)
    {
        $calendar = Calendar::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $calendar->delete();

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
            'category' => 'nullable|string',
        ]);


        return $validated;
    }
}
