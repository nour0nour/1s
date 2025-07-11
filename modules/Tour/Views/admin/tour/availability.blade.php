<div class="panel">
    <div class="panel-title"><strong>{{__("Availability")}}</strong></div>
    <div class="panel-body">

        <h3 class="panel-body-title">{{__('Fixed dates')}}</h3>
        <div class="form-group">
            <label>
                <input type="checkbox" name="enable_fixed_date" @if(!empty($row->enable_fixed_date)) checked @endif value="1"> {{__('Enable Fixed Date')}}
            </label>
        </div>
        <?php $old = $row->meta->open_hours ?? [];?>
        <div class="row" data-condition="enable_fixed_date:is(1)">
            <div class="col-lg-3">
                <div class="form-group" >
                    <label for="">{{__("Start Date")}}</label>
                    <input type="text" name="start_date" id=" start_date" class="form-control has-datepicker" value="{{ old('start_date',!empty($row->start_date)?$row->start_date->format("Y-m-d"):"")}}">

                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group" >
                    <label for="">{{__("End Date")}}</label>
                    <input type="text" name="end_date" id=" end_date"  class="form-control has-datepicker" value="{{ old('end_date',!empty($row->end_date)?$row->end_date->format("Y-m-d"):"")}}">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group" >
                    <label for="">{{__("Last Booking Date")}}</label>
                    <input type="text" name="last_booking_date" id=" last_booking_date" class="form-control has-datepicker" value="{{ old('last_booking_date',!empty($row->last_booking_date)?$row->last_booking_date->format("Y-m-d"):"")}}">
                </div>
            </div>
        </div>



        <h3 class="panel-body-title">{{__('Open Hours')}}</h3>
        <div class="form-group">
            <label>
                <input type="checkbox" name="enable_open_hours" @if(!empty($row->meta->enable_open_hours)) checked @endif value="1"> {{__('Enable Open Hours')}}
            </label>
        </div>
        <?php $old = $row->meta->open_hours ?? [];?>
        <div class="table-responsive form-group" data-condition="enable_open_hours:is(1)">
            <table class="table">
                <thead>
                <tr>
                    <th>{{__('Enable?')}}</th>
                    <th>{{__('Day of Week')}}</th>
                    <th>{{__('Open')}}</th>
                    <th>{{__('Close')}}</th>
                </tr>
                </thead>
                @for($i = 1 ; $i <=7 ; $i++)
                    <tr>
                        <td>
                            <input style="display: inline-block" type="checkbox" @if($old[$i]['enable']  ?? false ) checked @endif name="open_hours[{{$i}}][enable]" value="1">
                        </td>
                        <td><strong>
                                @switch($i)
                                    @case(1)
                                    {{__('Monday')}}
                                    @break
                                    @case(2)
                                    {{__('Tuesday')}}
                                    @break
                                    @case (3)
                                    {{__('Wednesday')}}
                                    @break
                                    @case (4)
                                    {{__('Thursday')}}
                                    @break
                                    @case (5)
                                    {{__('Friday')}}
                                    @break
                                    @case (6)
                                    {{__('Saturday')}}
                                    @break
                                    @case (7)
                                    {{__('Sunday')}}
                                    @break
                                @endswitch
                            </strong></td>
                        <td>
                            <select class="form-control" name="open_hours[{{$i}}][from]">
                                <?php
                                $time = strtotime('2019-01-01 00:00:00');
                                for($k = 0; $k <= 23; $k++):

                                $val = date('H:i', $time + 60 * 60 * $k);
                                ?>
                                <option @if(isset($old[$i]) and $old[$i]['from'] == $val) selected @endif value="{{$val}}">{{$val}}</option>

                                <?php endfor;?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="open_hours[{{$i}}][to]">
                                <?php
                                $time = strtotime('2019-01-01 00:00:00');
                                for($k = 0; $k <= 23; $k++):

                                $val = date('H:i', $time + 60 * 60 * $k);
                                ?>
                                <option @if(isset($old[$i]) and  $old[$i]['to'] == $val ) selected @endif value="{{$val}}">{{$val}}</option>

                                <?php endfor;?>
                            </select>
                        </td>
                    </tr>
                @endfor
            </table>
        </div>

        <h3 class="panel-body-title" data-condition="enable_fixed_date:not(1)">{{__('Date select type:')}}</h3>
        <div class="form-group" data-condition="enable_fixed_date:not(1)">
            <div class="row">
                <div class="col-md-3">
                    <select name="date_select_type" class="form-control">
                        <option @if( $row->date_select_type == "datepicker") selected @endif value="datepicker">{{ __("Date picker") }}</option>
                        <option @if( $row->date_select_type == "dropdown") selected @endif value="dropdown">{{ __("Dropdown") }}</option>
                    </select>
                </div>
            </div>
        </div>

    </div>
</div>
