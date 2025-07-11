<?php

namespace  Modules\Event;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id'   => 'event',
                'title' => __("Event Settings"),
                'position'=>20,
                'view'=>"Event::admin.settings.event",
                "keys"=>[
                    'event_disable',
                    'event_page_search_title',
                    'event_page_search_banner',
                    'event_layout_search',
                    'event_location_search_style',
                    'event_page_limit_item',

                    'event_enable_review',
                    'event_review_approved',
                    'event_enable_review_after_booking',
                    'event_review_number_per_page',
                    'event_review_stats',

                    'event_page_list_seo_title',
                    'event_page_list_seo_desc',
                    'event_page_list_seo_image',
                    'event_page_list_seo_share',

                    'event_booking_buyer_fees',
                    'event_vendor_create_service_must_approved_by_admin',
                    'event_allow_vendor_can_change_their_booking_status',
                    'event_allow_vendor_can_change_paid_amount',
                    'event_allow_vendor_can_add_service_fee',
                    'event_search_fields',
                    'event_map_search_fields',

                    'event_allow_review_after_making_completed_booking',
                    'event_deposit_enable',
                    'event_deposit_type',
                    'event_deposit_amount',
                    'event_deposit_fomular',

                    'event_layout_map_option',

                    'event_booking_type',
                    'event_icon_marker_map',

                    'event_map_lat_default',
                    'event_map_lng_default',
                    'event_map_zoom_default',

                    'event_location_search_value',
                    'event_location_search_style',
                    'event_location_radius_value',
                    'event_location_radius_type',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}
