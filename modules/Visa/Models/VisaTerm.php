<?php
namespace Modules\Visa\Models;

use App\BaseModel;

class VisaTerm extends BaseModel
{
    protected $table = 'bravo_visa_term';
    protected $fillable = [
        'term_id',
        'target_id'
    ];
}