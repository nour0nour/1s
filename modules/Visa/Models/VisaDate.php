<?php
namespace Modules\Visa\Models;

use App\BaseModel;

class VisaDate extends BaseModel
{
    protected $table = 'bravo_visa_dates';

    protected $casts = [
        'person_types'=>'array',
        'price'=>'float',
        'sale_price'=>'float',
    ];

    public static function getDatesInRanges($start_date,$end_date,$id){
        return static::query()->where([
            ['start_date','>=',$start_date],
            ['end_date','<=',$end_date],
            ['target_id','=',$id],
        ])->take(100)->get();
    }
}
