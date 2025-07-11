<?php
namespace Modules\Hotel\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Modules\FrontendController;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomBooking;
use Modules\Hotel\Models\HotelRoomDate;

class AvailabilityController extends FrontendController{

    protected $roomClass;
    /**
     * @var HotelRoomDate
     */
    protected $roomDateClass;

    /**
     * @var Booking
     */
    protected $bookingClass;
    protected $hotelClass;
    protected $currentHotel;
    protected $roomBookingClass;

    protected $indexView = 'Hotel::frontend.user.availability';

    public function __construct()
    {
        parent::__construct();
        $this->roomClass = HotelRoom::class;
        $this->roomDateClass = HotelRoomDate::class;
        $this->bookingClass = Booking::class;
        $this->hotelClass = Hotel::class;
        $this->roomBookingClass = HotelRoomBooking::class;
    }
    public function callAction($method, $parameters)
    {
        if(!Hotel::isEnable())
        {
            return redirect('/');
        }
        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }

    protected function hasHotelPermission($hotel_id = false){
        if(empty($hotel_id)) return false;

        $hotel = $this->hotelClass::find($hotel_id);
        if(empty($hotel)) return false;

        if(!$this->hasPermission('hotel_update') and $hotel->author_id != Auth::id()){
            return false;
        }

        $this->currentHotel = $hotel;
        return true;
    }

    public function index(Request $request,$hotel_id){

        $this->checkPermission('hotel_update');

        if(!$this->hasHotelPermission($hotel_id))
        {
            abort(403);
        }

        $q = $this->roomClass::query();

        if($request->query('s')){
            $q->where('title','like','%'.$request->query('s').'%');
        }

        $q->orderBy('id','desc');
        $q->where('parent_id',$hotel_id);

        $rows = $q->paginate(15);

        $current_month = strtotime(date('Y-m-01',time()));

        if($request->query('month')){
            $date = date_create_from_format('m-Y',$request->query('month'));
            if(!$date){
                $current_month = time();
            }else{
                $current_month = $date->getTimestamp();
            }
        }
        $breadcrumbs = [
            [
                'name' => __('Hotels'),
                'url'  => route('hotel.vendor.index')
            ],
            [
                'name' => __('Hotel: :name',['name'=>$this->currentHotel->title]),
                'url'  => route('hotel.vendor.edit',[$this->currentHotel->id])
            ],
            [
                'name'  => __('Availability'),
                'class' => 'active'
            ],
        ];
        $hotel = $this->currentHotel;
        $page_title = __('Room Availability');

        return view($this->indexView,compact('rows','breadcrumbs','current_month','page_title','request','hotel'));
    }

    public function loadDates(Request $request,$hotel_id){

        $request->validate([
            'id'=>'required',
            'start'=>'required',
            'end'=>'required',
        ]);

        if(!$this->hasHotelPermission($hotel_id))
        {
            return $this->sendError(__("Hotel not found"));
        }
        /**
         * @var $room HotelRoom
         */

        $room = $this->roomClass::find($request->query('id'));
        if(empty($room)){
            return $this->sendError(__('room not found'));
        }

        $is_single = $request->query('for_single');
        $query = $this->roomDateClass::query();
        $query->where('target_id',$request->query('id'));
        $query->where('start_date','>=',date('Y-m-d H:i:s',strtotime($request->query('start'))));
        $query->where('end_date','<=',date('Y-m-d H:i:s',strtotime($request->query('end'))));

        $rows =  $query->take(100)->get();
        $allDates = [];

        $period = periodDate($request->input('start'),$request->input('end'),false);
        foreach ($period as $dt){
            $date = [
                'id'=>rand(0,999),
                'active'=>0,
                'price'=> $room->price,
                'number'=> $room->number,
                'is_instant'=>0,
                'is_default'=>true,
                'textColor'=>'#2791fe'
            ];
            $date['price_html'] = format_money($date['price']);
            if(!$is_single){
                $date['price_html'] = format_money_main($date['price']);
            }
            $date['title'] = $date['event']  = $date['price_html'].' x '.$room->number;
            $date['start'] = $date['end'] = $dt->format('Y-m-d');

            $date['active'] = 1;
            $allDates[$dt->format('Y-m-d')] = $date;
        }
        if(!empty($rows))
        {
            foreach ($rows as $row)
            {
                $row->start = date('Y-m-d',strtotime($row->start_date));
                $row->end = date('Y-m-d',strtotime($row->start_date));
                $row->textColor = '#2791fe';
                $price = $row->price;
                if(empty($price)){
                    $price = $room->price;
                }
                $row->title = $row->event = format_money($price);
                if(!$is_single){
                    $row->title = $row->event = format_money_main($price).' x '.$row->number;
                }
                $row->price = $price;

                if(!$row->active)
                {
                    $row->title = $row->event = __('Blocked');
                    $row->backgroundColor = '#fe2727';
                    $row->classNames = ['blocked-event'];
                    $row->textColor = '#fe2727';
                    $row->active = 0;
                }else{
                    $row->classNames = ['active-event'];
                    $row->active = 1;
//                    if($row->is_instant){
//                        $row->title = '<i class="fa fa-bolt"></i> '.$row->title;
//                    }
                }

                $allDates[date('Y-m-d',strtotime($row->start_date))] = $row->toArray();

            }
        }
        $bookings = $room->getBookingsInRange($request->query('start'),$request->query('end'));
        if(!empty($bookings))
        {
            foreach ($bookings as $booking){
                $period = periodDate($booking->start_date,$booking->end_date,false);
                foreach ($period as $dt){
                    $date = $dt->format('Y-m-d');
                    if(isset($allDates[$date])){
                        $allDates[$date]['number'] -= $booking->number;
                        $allDates[$date]['event'] = $allDates[$date]['title'] = format_money_main($allDates[$date]['price'] ). ' x '.$allDates[$date]['number'];
                        if($allDates[$date]['number'] <=0 ){
                            $allDates[$date]['active'] = 0;
                            $allDates[$date]['event'] = __('Full Book');
                            $allDates[$date]['title'] = __('Full Book');
                            $allDates[$date]['classNames'] = ['full-book-event'];
                        }
                    }
                }
            }
        }
        $data = array_values($allDates);

        return response()->json($data);
    }

    public function store(Request $request,$hotel_id){

        if(!$this->hasHotelPermission($hotel_id))
        {
            return $this->sendError(__("Hotel not found"));
        }

        $request->validate([
            'target_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required'
        ]);

        $room = $this->roomClass::find($request->input('target_id'));
        $target_id = $request->input('target_id');

        if(empty($room)){
            return $this->sendError(__('Room not found'));
        }

        if(!$this->hasPermission('hotel_manage_others')){

            if($this->currentHotel->author_id != Auth::id()){
                return $this->sendError("You do not have permission to access it");
            }
        }

        $dayOfWeek =$request->input("day_of_week_select",[]);

        $postData = $request->input();
        $period = periodDate($request->input('start_date'),$request->input('end_date'));
        foreach ($period as $dt){
            $date = $this->roomDateClass::where('start_date',$dt->format('Y-m-d'))->where('target_id',$target_id)->first();

            if(empty($date)){
                $date = new $this->roomDateClass();
                $date->target_id = $target_id;
            }
            $postData['start_date'] = $dt->format('Y-m-d H:i:s');
            $postData['end_date'] = $dt->format('Y-m-d H:i:s');


            $date->fillByAttr([
                'start_date','end_date','price',
                'is_instant','active',
                'number'
            ],$postData);

            if(empty($dayOfWeek)){
                $date->save();
            }elseif(in_array(date('N', strtotime($dt->format('Y-m-d H:i:s')) ),$dayOfWeek)){
                $date->save();
            }
        }

        return $this->sendSuccess([],__("Update Success"));

    }
}
