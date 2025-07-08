<?php

namespace  Modules\Visa;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        $configs = [
            'visa'=>[
                'id'   => 'visa',
                'title' => __("Visa Settings"),
                'position'=>20,
                'view'=>"Visa::admin.settings.visa",
                "keys"=>[
                    'visa_disable',
                    'visa_page_search_title',
                    'visa_page_search_banner',
                    'visa_layout_search',
                    'visa_location_search_style',
                    'visa_page_limit_item',

                    'visa_enable_review',
                    'visa_review_approved',
                    'visa_enable_review_after_booking',
                    'visa_review_number_per_page',
                    'visa_review_stats',

                    'visa_page_list_seo_title',
                    'visa_page_list_seo_desc',
                    'visa_page_list_seo_image',
                    'visa_page_list_seo_share',

                    'visa_booking_buyer_fees',
                    'visa_vendor_create_service_must_approved_by_admin',
                    'visa_allow_vendor_can_change_their_booking_status',
                    'visa_allow_vendor_can_change_paid_amount',
                    'visa_allow_vendor_can_add_service_fee',
                    'visa_search_fields',
                    'visa_map_search_fields',

                    'visa_allow_review_after_making_completed_booking',
                    'visa_deposit_enable',
                    'visa_deposit_type',
                    'visa_deposit_amount',
                    'visa_deposit_fomular',

                    'visa_layout_map_option',
                    'visa_icon_marker_map',

                    'visa_map_lat_default',
                    'visa_map_lng_default',
                    'visa_map_zoom_default',

                    'visa_location_radius_value',
                    'visa_location_radius_type',
                ],
                'html_keys'=>[

                ],
                'filter_demo_mode'=>[
                ]
            ]
        ];
        return apply_filters(Hook::VISA_SETTING_CONFIG,$configs);
    }
}
