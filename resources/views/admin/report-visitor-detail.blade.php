<div class="box box-default" style="padding:10px">

    <div class="form-horizontal">

        <div class="box-body">

            <div class="fields-group">

                <div class="form-group ">


                    <div class="form-group ">
                        <label class="col-sm-3 control-label">ユーザー名</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$user_data->name}} 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-3 control-label">日報数(全体)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_report_count}} 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-3 control-label">日報数(自分)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_my_report_count}} 
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group ">
                        <label class="col-sm-3 control-label">日報閲覧率(全体)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_visitor_rate}} %  
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group ">
                        <label class="col-sm-3 control-label">日報閲覧率(直近3ヶ月)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$thress_month_data['visitor_rate']}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-3 control-label">いいねを受けた数(全体)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_likes_receive}}      
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-3 control-label">いいねを受けた数(直近3ヶ月)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                     {{$thress_month_data['likes_receive']}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-3 control-label">いいねをした数(全体)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$all_likes_give}} 
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group ">
                        <label class="col-sm-3 control-label">いいねをした数(直近3ヶ月)</label>
                        <div class="col-sm-7">
                            <div class="box box-solid box-default no-margin box-show">
                                <div class="box-body">
                                    {{$thress_month_data['likes_give']}}
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
            <td>{{$month['likes_receive']}} </td>
            <td>{{$month['likes_give']}}</td>
        </tr>

        @endforeach

    </table>

</div>

<div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
    <a href="/admin/report-visitors/show/{{$user_data->id}}" class="btn btn-sm btn-success" title="全データの集計を表示" target="_blank">
        <span class="hidden-xs">全データの集計を表示</span>
    </a>
</div>