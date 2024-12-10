<div class="box box-default" style="padding:10px">

    <p>※既読率は、既読日報数 / 閲覧可能日報数になります。<br/>※削除された日報、非表示の日報の既読データも計算に含まれるため、100%より大きくなる場合がありますので、正確なデータは各自詳細画面にてご確認お願いします。</p>
    <table class="table table-hover grid-table" style="width: 50%;">

        <tr>
            <th>ユーザー名</th>
            <th>閲覧率(※参考値)</th>
            {{-- <th>3ヶ月直近の既読率</th> --}}
            <th></th>
        </tr>
        @foreach ($users as $user)
        <tr>
            <td>{{$user['name']}}</td>
            <td>
                @if($user['visitor_rate'] != 0)
                {{$user['visitor_rate']}}%
                @else
                -
                @endif

            </td>
            {{-- <td>{{$user['three_visitor_rate']}}</td> --}}
            {{-- <td>{{$user['likes_give']}}</td> --}}
            <td>
                <a href="/admin/report-visitors/{{$user['id']}}" class="grid-row-view" target="_blank">
                    <i>詳細</i>
                </a>
            </td>
        </tr>
        @endforeach
    </table>

</div>