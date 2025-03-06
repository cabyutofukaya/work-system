{!! nl2br(e($message_text)) !!}<br><br>


<div style="border: 1px solid;padding:20px 10px">

    <span style="font-size: larger">{{$schedule->title ?? '(無題)'}}<span>

    @if($schedule->category)
    (<span style="font-size: smaller">{{$schedule->category}}</span>)
    @endif

    <br>

    <span>{{date('n月j日',strtotime($schedule->date))}}</span>

    @if(!$schedule->all_day)
    <span style="font-size: smaller">{{date('G:i',strtotime($schedule->start_time))}} - {{date('G:i',strtotime($schedule->end_time))}}</span>
    @else
    @endif
    <br><br>

    @if($schedule->content)
    <span style="font-size: smaller">{{$schedule->content}}</span><br><br>
    @endif


    @if($booking)
    <span style="font-size: smaller">施設 / {{$booking->room->name}}</span><br>
    <span style="font-size: smaller">{{$booking->room->name}}</span><br><br>
    @endif


    @if($schedule->zoom_url)
    <span style="font-size: smaller">ミーティングURL</span><br>
    <a href="{{$schedule->zoom_url}}"><span style="font-size: smaller">{{$schedule->zoom_url}}</span></a><br>
    @endif

</div>

<br><br>






===========================================<br>
サイト運営：株式会社キャブステーション<br>
不具合やアイデアはこちら　info.ccd@cab-station.com<br>
===========================================<br>