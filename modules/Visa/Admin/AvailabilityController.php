<?php
namespace Modules\Visa\Admin;

use Modules\Booking\Models\Booking;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaDate;

class AvailabilityController extends \Modules\Visa\Controllers\AvailabilityController
{
    protected $visaClass;
    protected $visaDateClass;
    protected $bookingClass;
    protected $indexView = 'Visa::admin.availability';

    public function __construct(Visa $visaClass, VisaDate $visaDateClass, Booking $bookingClass)
    {
        $this->setActiveMenu(route('visa.admin.index'));
        $this->middleware('dashboard');
        $this->visaClass = $visaClass;
        $this->visaDateClass = $visaDateClass;
        $this->bookingClass = $bookingClass;
    }

}
