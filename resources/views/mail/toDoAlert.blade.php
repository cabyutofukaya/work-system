{{$count}}件のTODOがあります。<br><br>

@if(!$sales_todo->isEmpty())

営業TODO(<a href="https://grouptube.biz/sales-todos">確認</a>)<br>

@foreach ($sales_todo as $sale)
{{date('n月j日 H:i',strtotime($sale->scheduled_at))}}   {{$sale['client']['name']}} {{$sale->contact_person}}   {{$sale->description}}<br>
@endforeach

<br>

@endif

@if(!$office_todo->isEmpty())
業務TODO(<a href="https://grouptube.biz/office-todos">確認</a>)<br>
@foreach ($office_todo as $office)
{{date('n月j日 H:i',strtotime($office->scheduled_at))}}   {{$office->title}}  {{$office->description}}<br>
@endforeach
<br>
@endif

===========================================<br>
サイト運営：株式会社キャブステーション<br>
不具合やアイデアはこちら　info.ccd@cab-station.com<br>
===========================================<br>