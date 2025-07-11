<?php
namespace Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use Modules\Event\Models\Event;
use Illuminate\Http\Request;
use Modules\Location\Models\Location;
use Modules\Location\Models\LocationCategory;
use Modules\Review\Models\Review;
use Modules\Core\Models\Attributes;
use DB;

class EventController extends Controller
{
    protected $eventClass;
    protected $locationClass;
    /**
     * @var string
     */
    private $locationCategoryClass;

    public function __construct(Event $eventClass, Location $locationClass,LocationCategory $locationCategoryClass)
    {
        $this->eventClass = $eventClass;
        $this->locationClass = $locationClass;
        $this->locationCategoryClass = $locationCategoryClass;
    }

    public function callAction($method, $parameters)
    {
        if(!$this->eventClass::isEnable())
        {
            return redirect('/');
        }
        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }
    public function index(Request $request)
    {
        $layout = setting_item("event_layout_search", 'normal');
        if ($request->query('_layout')) {
            $layout = $request->query('_layout');
        }
        $is_ajax = $request->query('_ajax');
        $for_map = $request->query('_map',$layout === 'map');

        if(!empty($request->query('limit'))){
            $limit = $request->query('limit');
        }else{
            $limit = !empty(setting_item("event_page_limit_item"))? setting_item("event_page_limit_item") : 9;
        }

        $query = $this->eventClass->search($request->input());
        $list = $query->paginate($limit);

        $markers = [];
        if (!empty($list) and $for_map) {
            foreach ($list as $row) {
                $markers[] = [
                    "id"      => $row->id,
                    "title"   => $row->title,
                    "lat"     => (float)$row->map_lat,
                    "lng"     => (float)$row->map_lng,
                    "gallery" => $row->getGallery(true),
                    "infobox" => view('Event::frontend.layouts.search.loop-grid', ['row' => $row,'disable_lazyload'=>1,'wrap_class'=>'infobox-item'])->render(),
                    'marker' => get_file_url(setting_item("event_icon_marker_map"),'full') ?? url('images/icons/png/pin.png'),
                ];
            }
        }
        $data = [
            'rows' => $list,
            'layout'=>$layout
        ];
        if ($is_ajax) {
            return $this->sendSuccess([
                "markers" => $markers,
                'fragments'=>[
                    '.ajax-search-result'=>view('Event::frontend.ajax.search-result'.($for_map ? '-map' : ''), $data)->render(),
                    '.result-count'=>$list->total() ? ($list->total() > 1 ? __(":count events found",['count'=>$list->total()]) : __(":count event found",['count'=>$list->total()])) : '',
                    '.count-string'=> $list->total() ? __("Showing :from - :to of :total Events",["from"=>$list->firstItem(),"to"=>$list->lastItem(),"total"=>$list->total()]) : ''
                ]
            ]);
        }
        $data = [
            'rows'               => $list,
            'list_location'      => $this->locationClass::where('status', 'publish')->limit(1000)->with(['translation'])->get()->toTree(),
            'event_min_max_price' => $this->eventClass::getMinMaxPrice(),
            'markers'            => $markers,
            "blank" => setting_item('search_open_tab') == "current_tab" ? 0 : 1 ,
            "seo_meta"           => $this->eventClass::getSeoMetaForPageList()
        ];
        $data['layout'] = $layout;
        $data['attributes'] = Attributes::where('service', 'event')->orderBy("position","desc")->with(['terms'=>function($query){
            $query->withCount('event');
        },'translation'])->get();

        if ($layout == "map") {
            $data['body_class'] = 'has-search-map';
            $data['html_class'] = 'full-page';
            return view('Event::frontend.search-map', $data);
        }
        return view('Event::frontend.search', $data);
    }

    public function detail(Request $request, $slug)
    {
        $row = $this->eventClass::where('slug', $slug)->with(['location','translation','hasWishList'])->first();;
        if ( empty($row) or !$row->hasPermissionDetailView()) {
            return redirect('/');
        }
        $translation = $row->translate();
        $event_related = [];
        $location_id = $row->location_id;
        if (!empty($location_id)) {
            $event_related = $this->eventClass::where('location_id', $location_id)->where("status", "publish")->take(4)->whereNotIn('id', [$row->id])->with(['location','translation','hasWishList'])->get();
        }
        $review_list = $row->getReviewList();
        $data = [
            'row'          => $row,
            'translation'       => $translation,
            'event_related' => $event_related,
            'location_category'=>$this->locationCategoryClass::where("status", "publish")->with('location_category_translations')->get(),
            'booking_data' => $row->getBookingData(),
            'review_list'  => $review_list,
            'seo_meta'  => $row->getSeoMetaWithTranslation(app()->getLocale(),$translation),
            'body_class'=>'is_single',
            'breadcrumbs'       => [
                [
                    'name'  => __('Event'),
                    'url'  => route('event.search'),
                ],
            ],
        ];
        $data['breadcrumbs'] = array_merge($data['breadcrumbs'],$row->locationBreadcrumbs());
        $data['breadcrumbs'][] = [
            'name'  => $translation->title,
            'class' => 'active'
        ];
        $this->setActiveMenu($row);
        return view('Event::frontend.detail', $data);
    }
}
