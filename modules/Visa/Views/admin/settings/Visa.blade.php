<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__("Page Search")}}</h3>
        <p class="form-group-desc">{{__('Config page search of your website')}}</p>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-title"><strong>{{__("General Options")}}</strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="" >{{__("Title Page")}}</label>
                    <div class="form-controls">
                        <input type="text" name="visa_page_search_title" value="{{setting_item_with_lang('visa_page_search_title',request()->query('lang'))}}" class="form-control">
                    </div>
                </div>
                @if(is_default_lang())
                    <div class="form-group">
                        <label class="" >{{__("Banner Page")}}</label>
                        <div class="form-controls form-group-image">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('visa_page_search_banner',$settings['visa_page_search_banner'] ?? "") !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" >{{__("Layout Search")}}</label>
                        <div class="form-controls">
                            <select name="visa_layout_search" class="form-control" >
                                @foreach(config('visa.layouts',['normal'=>__("Normal Layout"),'map'=>__("Map Layout")]) as $id=>$name))
                                    <option value="{{$id}}" {{ setting_item('visa_layout_search','normal') == $id ? 'selected' : ''  }}>{{$name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <?php do_action(\Modules\Visa\Hook::VISA_SETTING_AFTER_LAYOUT_SEARCH) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="" >{{__("Location Search Style")}}</label>
                                <div class="form-controls">
                                    <select name="visa_location_search_style" class="form-control">
                                        <option {{ ($settings['visa_location_search_style'] ?? '') == 'normal' ? 'selected' : ''  }}      value="normal">{{__("Normal")}}</option>
                                        <option {{ ($settings['visa_location_search_style'] ?? '') == 'autocomplete' ? 'selected' : '' }} value="autocomplete">{{__('Autocomplete from locations')}}</option>
                                        <option {{ ($settings['visa_location_search_style'] ?? '') == 'autocompletePlace' ? 'selected' : '' }} value="autocompletePlace">{{__('Autocomplete from Gmap Place')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="" >{{__("Limit item per Page")}}</label>
                                <div class="form-controls">
                                    <input type="number" min="1" name="visa_page_limit_item" placeholder="{{ __("Default: 9") }}" value="{{setting_item_with_lang('visa_page_limit_item',request()->query('lang'), 9)}}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" data-condition="visa_location_search_style:is(autocompletePlace)">
                            <label class="" >{{__("Radius options")}}</label>
                            <div class="input-group mb-3">
                                <input type="number" name="visa_location_radius_value" min="0" value="{{ setting_item('visa_location_radius_value',1)}}" class="form-control" >
                                <div class="input-group-append">
                                    <select name="visa_location_radius_type" id="">
                                        <option  {{ (setting_item('visa_location_radius_type') ?? '') == 3959 ? 'selected' : ''  }} value="3959">{{__('Miles')}}</option>
                                        <option  {{ (setting_item('visa_location_radius_type') ?? '') == 6371 ? 'selected' : ''  }} value="6371">{{__('Km')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="" >{{__("Layout Map Option")}}</label>
                        <div class="form-controls">
                            <select name="visa_layout_map_option" class="form-control">
                                <option {{ (setting_item_with_lang('visa_layout_map_option',request()->query('lang')) ?? '') == 'map_left' ? 'selected' : '' }} value="map_left">{{__('Map Left')}}</option>
                                <option {{ (setting_item_with_lang('visa_layout_map_option',request()->query('lang')) ?? '') == 'map_right' ? 'selected' : ''  }} value="map_right">{{__("Map Right")}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{__("Map Lat Default")}}</label>
                            <div class="form-controls">
                                <input type="text" name="visa_map_lat_default" value="{{$settings['visa_map_lat_default'] ?? ''}}" class="form-control" placeholder="21.030513">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>{{__("Map Lng Default")}}</label>
                            <div class="form-controls">
                                <input type="text" name="visa_map_lng_default" value="{{$settings['visa_map_lng_default'] ?? ''}}" class="form-control" placeholder="105.840565">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>{{__("Map Zoom Default")}}</label>
                            <div class="form-controls">
                                <input type="text" name="visa_map_zoom_default" value="{{$settings['visa_map_zoom_default'] ?? ''}}" class="form-control" placeholder="13">
                            </div>
                        </div>
                        <div class="col-md-12 mt-1">
                            <i> {{ __('Get lat - lng in here') }} <a href="https://www.latlong.net" target="_blank">https://www.latlong.net</a></i>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="" >{{__("Icon Marker in Map")}}</label>
                        <div class="form-controls form-group-image">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('visa_icon_marker_map',$settings['visa_icon_marker_map'] ?? "") !!}
                        </div>
                    </div>
                    @php do_action(\Modules\Visa\Hook::VISA_SETTING_AFTER_MAP) @endphp
                @endif
            </div>
        </div>
        @include('Visa::admin.settings.form-search')
        @include('Visa::admin.settings.map-search')
        <div class="panel">
            <div class="panel-title"><strong>{{__("SEO Options")}}</strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#seo_1">{{__("General Options")}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#seo_2">{{__("Share Facebook")}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#seo_3">{{__("Share Twitter")}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" >
                        <div class="tab-pane active" id="seo_1">
                            <div class="form-group" >
                                <label class="control-label">{{__("Seo Title")}}</label>
                                <input type="text" name="visa_page_list_seo_title" class="form-control" placeholder="{{__("Enter title...")}}" value="{{ setting_item_with_lang('visa_page_list_seo_title',request()->query('lang'))}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{__("Seo Description")}}</label>
                                <input type="text" name="visa_page_list_seo_desc" class="form-control" placeholder="{{__("Enter description...")}}" value="{{setting_item_with_lang('visa_page_list_seo_desc',request()->query('lang'))}}">
                            </div>
                            @if(is_default_lang())
                                <div class="form-group form-group-image">
                                    <label class="control-label">{{__("Featured Image")}}</label>
                                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('visa_page_list_seo_image', $settings['visa_page_list_seo_image'] ?? "" ) !!}
                                </div>
                            @endif
                        </div>
                        @php
                            $seo_share = json_decode(setting_item_with_lang('visa_page_list_seo_share',request()->query('lang'),'[]'),true);
                        @endphp
                        <div class="tab-pane" id="seo_2">
                            <div class="form-group">
                                <label class="control-label">{{__("Facebook Title")}}</label>
                                <input type="text" name="visa_page_list_seo_share[facebook][title]" class="form-control" placeholder="{{__("Enter title...")}}" value="{{$seo_share['facebook']['title'] ?? "" }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{__("Facebook Description")}}</label>
                                <input type="text" name="visa_page_list_seo_share[facebook][desc]" class="form-control" placeholder="{{__("Enter description...")}}" value="{{$seo_share['facebook']['desc'] ?? "" }}">
                            </div>
                            @if(is_default_lang())
                                <div class="form-group form-group-image">
                                    <label class="control-label">{{__("Facebook Image")}}</label>
                                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('visa_page_list_seo_share[facebook][image]',$seo_share['facebook']['image'] ?? "" ) !!}
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane" id="seo_3">
                            <div class="form-group">
                                <label class="control-label">{{__("Twitter Title")}}</label>
                                <input type="text" name="visa_page_list_seo_share[twitter][title]" class="form-control" placeholder="{{__("Enter title...")}}" value="{{$seo_share['twitter']['title'] ?? "" }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{__("Twitter Description")}}</label>
                                <input type="text" name="visa_page_list_seo_share[twitter][desc]" class="form-control" placeholder="{{__("Enter description...")}}" value="{{$seo_share['twitter']['title'] ?? "" }}">
                            </div>
                            @if(is_default_lang())
                                <div class="form-group form-group-image">
                                    <label class="control-label">{{__("Twitter Image")}}</label>
                                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('visa_page_list_seo_share[twitter][image]', $seo_share['twitter']['image'] ?? "" ) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(is_default_lang())
    <hr>
    <div class="row">
        <div class="col-sm-4">
            <h3 class="form-group-title">{{__("Review Options")}}</h3>
            <p class="form-group-desc">{{__('Config review for visa')}}</p>
        </div>
        <div class="col-sm-8">
            <div class="panel">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="" >{{__("Enable review system for Visa?")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_enable_review" value="1" @if(!empty($settings['visa_enable_review'])) checked @endif /> {{__("Yes, please enable it")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("Turn on the mode for reviewing visa")}}</small>
                        </div>
                    </div>
                    <div class="form-group" data-condition="visa_enable_review:is(1)">
                        <label class="" >{{__("Customer must book a visa before writing a review?")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_enable_review_after_booking" value="1"  @if(!empty($settings['visa_enable_review_after_booking'])) checked @endif /> {{__("Yes please")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("ON: Only post a review after booking - Off: Post review without booking")}}</small>
                        </div>
                    </div>
                    <div class="form-group" data-condition="visa_enable_review:is(1),visa_enable_review_after_booking:is(1)">
                        <label>{{__("Allow review after making Completed Booking?")}}</label>
                        <div class="form-controls">
                            @php
                                $status = config('booking.statuses');
                                $settings_status = !empty($settings['visa_allow_review_after_making_completed_booking']) ? json_decode($settings['visa_allow_review_after_making_completed_booking']) : [];
                            @endphp
                            <div class="row">
                                @foreach($status as $item)
                                    <div class="col-md-4">
                                        <label><input type="checkbox" name="visa_allow_review_after_making_completed_booking[]" @if(in_array($item,$settings_status)) checked @endif value="{{$item}}"  /> {{booking_status_to_text($item)}} </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">{{__("Pick to the Booking Status, that allows reviews after booking")}}</small>
                            <small class="form-text text-muted">{{__("Leave blank if you allow writing the review with all booking status")}}</small>
                        </div>
                    </div>
                    <div class="form-group" data-condition="visa_enable_review:is(1)">
                        <label class="" >{{__("Review must be approval by admin")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_review_approved" value="1"  @if(!empty($settings['visa_review_approved'])) checked @endif /> {{__("Yes please")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("ON: Review must be approved by admin - OFF: Review is automatically approved")}}</small>
                        </div>
                    </div>
                    <div class="form-group" data-condition="visa_enable_review:is(1)">
                        <label class="" >{{__("Review number per page")}}</label>
                        <div class="form-controls">
                            <input type="number" class="form-control" name="visa_review_number_per_page" value="{{ $settings['visa_review_number_per_page'] ?? 5 }}" />
                            <small class="form-text text-muted">{{__("Break comments into pages")}}</small>
                        </div>
                    </div>
                    <div class="form-group" data-condition="visa_enable_review:is(1)">
                        <label class="" >{{__("Review criteria")}}</label>
                        <div class="form-controls">
                            <div class="form-group-item">
                                <div class="g-items-header">
                                    <div class="row">
                                        <div class="col-md-5">{{__("Title")}}</div>
                                        <div class="col-md-1"></div>
                                    </div>
                                </div>
                                <div class="g-items">
                                    <?php
                                    if(!empty($settings['visa_review_stats'])){
                                    $social_share = json_decode($settings['visa_review_stats']);
                                    ?>
                                    @foreach($social_share as $key=>$item)
                                        <div class="item" data-number="{{$key}}">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <input type="text" name="visa_review_stats[{{$key}}][title]" class="form-control" value="{{$item->title}}" placeholder="{{__('Eg: Service')}}">
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <?php } ?>
                                </div>
                                <div class="text-right">
                                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
                                </div>
                                <div class="g-more hide">
                                    <div class="item" data-number="__number__">
                                        <div class="row">
                                            <div class="col-md-11">
                                                <input type="text" __name__="visa_review_stats[__number__][title]" class="form-control" value="" placeholder="{{__('Eg: Service')}}">
                                            </div>
                                            <div class="col-md-1">
                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(is_default_lang())
    <hr>
    <div class="row">
        <div class="col-sm-4">
            <h3 class="form-group-title">{{__("Booking Buyer Fees Options")}}</h3>
            <p class="form-group-desc">{{__('Config buyer fees for visa')}}</p>
        </div>
        <div class="col-sm-8">
            <div class="panel">
                <div class="panel-body">
                    <div class="form-group-item">
                        <label class="control-label">{{__('Buyer Fees')}}</label>
                        <div class="g-items-header">
                            <div class="row">
                                <div class="col-md-5">{{__("Name")}}</div>
                                <div class="col-md-3">{{__('Price')}}</div>
                                <div class="col-md-3">{{__('Type')}}</div>
                                <div class="col-md-1"></div>
                            </div>
                        </div>
                        <div class="g-items">
                            <?php  $languages = \Modules\Language\Models\Language::getActive();  ?>
                            @if(!empty($settings['visa_booking_buyer_fees']))
                                <?php $visa_booking_buyer_fees = json_decode($settings['visa_booking_buyer_fees'],true); ?>
                                @foreach($visa_booking_buyer_fees as $key=>$buyer_fee)
                                    <div class="item" data-number="{{$key}}">
                                        <div class="row">
                                            <div class="col-md-5">
                                                @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                                    @foreach($languages as $language)
                                                        <?php $key_lang = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                        <div class="g-lang">
                                                            <div class="title-lang">{{$language->name}}</div>
                                                            <input type="text" name="visa_booking_buyer_fees[{{$key}}][name{{$key_lang}}]" class="form-control" value="{{$buyer_fee['name'.$key_lang] ?? ''}}" placeholder="{{__('Fee name')}}">
                                                            <input type="text" name="visa_booking_buyer_fees[{{$key}}][desc{{$key_lang}}]" class="form-control" value="{{$buyer_fee['desc'.$key_lang] ?? ''}}" placeholder="{{__('Fee desc')}}">
                                                        </div>

                                                    @endforeach
                                                @else
                                                    <input type="text" name="visa_booking_buyer_fees[{{$key}}][name]" class="form-control" value="{{$buyer_fee['name'] ?? ''}}" placeholder="{{__('Fee name')}}">
                                                    <input type="text" name="visa_booking_buyer_fees[{{$key}}][desc]" class="form-control" value="{{$buyer_fee['desc'] ?? ''}}" placeholder="{{__('Fee desc')}}">
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" min="0" step="0.1" name="visa_booking_buyer_fees[{{$key}}][price]" class="form-control" value="{{$buyer_fee['price']}}">
                                                <select name="visa_booking_buyer_fees[{{$key}}][unit]" class="form-control">
                                                    <option @if(($buyer_fee['unit'] ?? "") ==  'fixed') selected @endif value="fixed">{{ __("Fixed") }}</option>
                                                    <option @if(($buyer_fee['unit'] ?? "") ==  'percent') selected @endif value="percent">{{ __("Percent") }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="visa_booking_buyer_fees[{{$key}}][type]" class="form-control d-none">
                                                    <option @if($buyer_fee['type'] ==  'one_time') selected @endif value="one_time">{{__("One-time")}}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
                        </div>
                        <div class="g-more hide">
                            <div class="item" data-number="__number__">
                                <div class="row">
                                    <div class="col-md-5">
                                        @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                            @foreach($languages as $language)
                                                <?php $key = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                <div class="g-lang">
                                                    <div class="title-lang">{{$language->name}}</div>
                                                    <input type="text" __name__="visa_booking_buyer_fees[__number__][name{{$key}}]" class="form-control" value="" placeholder="{{__('Fee name')}}">
                                                    <input type="text" __name__="visa_booking_buyer_fees[__number__][desc{{$key}}]" class="form-control" value="" placeholder="{{__('Fee desc')}}">
                                                </div>

                                            @endforeach
                                        @else
                                            <input type="text" __name__="visa_booking_buyer_fees[__number__][name]" class="form-control" value="" placeholder="{{__('Fee name')}}">
                                            <input type="text" __name__="visa_booking_buyer_fees[__number__][desc]" class="form-control" value="" placeholder="{{__('Fee desc')}}">
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" min="0" step="0.1"  __name__="visa_booking_buyer_fees[__number__][price]" class="form-control" value="">
                                        <select __name__="visa_booking_buyer_fees[__number__][unit]" class="form-control">
                                            <option value="fixed">{{ __("Fixed") }}</option>
                                            <option value="percent">{{ __("Percent") }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select __name__="visa_booking_buyer_fees[__number__][type]" class="form-control d-none">
                                            <option value="one_time">{{__("One-time")}}</option>
                                        </select>
{{--                                        <label>--}}
{{--                                            <input type="checkbox" min="0" __name__="visa_booking_buyer_fees[__number__][per_person]" value="on">--}}
{{--                                            {{__("Price per person")}}--}}
{{--                                        </label>--}}
                                    </div>
                                    <div class="col-md-1">
                                        <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if(is_default_lang())
    <hr>
    <div class="row">
        <div class="col-sm-4">
            <h3 class="form-group-title">{{__("Vendor Options")}}</h3>
            <p class="form-group-desc">{{__('Vendor config for visa')}}</p>
        </div>
        <div class="col-sm-8">
            <div class="panel">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="" >{{__("Visa created by vendor must be approved by admin")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_vendor_create_service_must_approved_by_admin" value="1" @if(!empty($settings['visa_vendor_create_service_must_approved_by_admin'])) checked @endif /> {{__("Yes please")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("ON: When vendor posts a service, it needs to be approved by administrator")}}</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" >{{__("Allow vendor can change their booking status")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_allow_vendor_can_change_their_booking_status" value="1" @if(!empty($settings['visa_allow_vendor_can_change_their_booking_status'])) checked @endif /> {{__("Yes please")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("ON: Vendor can change their booking status")}}</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" >{{__("Allow vendor can change their booking paid amount")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_allow_vendor_can_change_paid_amount" value="1" @if(!empty($settings['visa_allow_vendor_can_change_paid_amount'])) checked @endif /> {{__("Yes please")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("ON: Vendor can change their booking paid amount")}}</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" >{{__("Allow vendor can add service fee")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" name="visa_allow_vendor_can_add_service_fee" value="1" @if(!empty($settings['visa_allow_vendor_can_add_service_fee'])) checked @endif /> {{__("Yes please")}} </label>
                            <br>
                            <small class="form-text text-muted">{{__("ON: Vendor can add service fee")}}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


@if(is_default_lang())
<hr>

<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__("Booking Deposit")}}</h3>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-title"><strong>{{__("Booking Deposit Options")}}</strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="form-controls">
                    <label><input type="checkbox" name="visa_deposit_enable" value="1" @if(setting_item('visa_deposit_enable')) checked @endif > {{__('Yes, please enable it')}}</label>
                    </div>
                </div>
                <div class="form-group" data-condition="visa_deposit_enable:is(1)">
                    <label >{{__('Deposit Amount')}}</label>
                    <div class="form-controls">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="number" name="visa_deposit_amount" class="form-control" step="0.1" value="{{old('visa_deposit_amount',setting_item('visa_deposit_amount'))}}" >
                                    <select name="visa_deposit_type"  class="form-control">
                                        <option value="fixed">{{__("Fixed")}}</option>
                                        <option @if(old('visa_deposit_type',setting_item('visa_deposit_type')) == 'percent') selected @endif value="percent">{{__("Percent")}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" data-condition="visa_deposit_enable:is(1)">
                    <label class="" >{{__("Deposit Fomular")}}</label>
                    <div class="form-controls">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="visa_deposit_fomular" class="form-control" >
                                    <option value="default" {{($settings['visa_deposit_fomular'] ?? '') == 'default' ? 'selected' : ''  }}>{{__('Default')}}</option>
                                    <option value="deposit_and_fee" {{ ($settings['visa_deposit_fomular'] ?? '') == 'deposit_and_fee' ? 'selected' : ''  }}>{{__("Deposit amount + Buyer free")}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__("Disable visa module?")}}</h3>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-title"><strong>{{__("Disable visa module")}}</strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="form-controls">
                    <label><input type="checkbox" name="visa_disable" value="1" @if(setting_item('visa_disable')) checked @endif > {{__('Yes, please disable it')}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

