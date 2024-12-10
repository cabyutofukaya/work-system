<div class="box box-default" style="padding:10px">

    <div class="form-horizontal">

        <div class="box-body">

            <div class="fields-group">

                <div class="form-group ">


                    <div class="form-group ">
                        <label class="col-sm-2 control-label">ユーザー名</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$user_data->name}} 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-2 control-label">日報数(全体)</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_report_count}} 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-2 control-label">日報数(自分)</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_my_report_count}} 
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group ">
                        <label class="col-sm-2 control-label">日報閲覧(全体)</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_visitor_rate}} %
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-2 control-label">いいねを受けた数</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_likes_receive}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-2 control-label">いいねをした数</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_likes_give}}
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>

<div class="box box-default" style="padding:10px">
    <table class="table table-hover grid-table">

        <tr>
            <th>月</th>
            <th>日報数</th>
            <th>自分の日報数</th>
            <th>閲覧率(全体)</th>
            <th>いいねを受けた数</th>
            <th>いいねをした数</th>
            <th></th>
        </tr>

        @foreach($month_data as $month)

        <tr>
            <td>{{$month['month']}}</td>

            <td>{{$month['report_count']}}</td>
            <td>{{$month['my_report_count']}}</td>

            <td>
                @if($month['visitor_rate'] != 0)
                {{$month['visitor_rate']}}%
                @else
                -
                @endif

            </td>
            <td>{{$month['likes_receive']}}</td>
            <td>{{$month['likes_give']}}</td>
        </tr>

        @endforeach

    </table>

</div>

