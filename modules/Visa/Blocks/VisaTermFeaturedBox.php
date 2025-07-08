<?php
namespace Modules\Visa\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Core\Models\Terms;

class VisaTermFeaturedBox extends BaseBlock
{
    public function getOptions(){
        return [
            'settings' => [
                [
                    'id'        => 'title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title')
                ],
                [
                    'id'        => 'desc',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Desc')
                ],
                [
                    'id'           => 'term_visa',
                    'type'         => 'select2',
                    'label'        => __('Select term visa'),
                    'select2'      => [
                        'ajax'     => [
                            'url'      => route('visa.admin.attribute.term.getForSelect2', ['type' => 'visa']),
                            'dataType' => 'json'
                        ],
                        'width'    => '100%',
                        'multiple' => "true",
                    ],
                    'pre_selected' => route('visa.admin.attribute.term.getForSelect2', [
                        'type'         => 'visa',
                        'pre_selected' => 1
                    ])
                ],
            ],
            'category'=>__("Service Visa")
        ];
    }

    public function getName()
    {
        return __('Visa: Term Featured Box');
    }

    public function content($model = [])
    {
        if (empty($term_visa = $model['term_visa'])) {
            return "";
        }
        $list_term = Terms::whereIn('id',$term_visa)->with('translation')->get();
        $model['list_term'] = $list_term;
        return view('Visa::frontend.blocks.term-featured-box.index', $model);
    }

    public function contentAPI($model = []){
        $model['list_term'] = null;
        if (!empty($term_visa = $model['term_visa'])) {
            $list_term = Terms::whereIn('id',$term_visa)->get();
            if(!empty($list_term)){
                foreach ( $list_term as $item){
                    $model['list_term'][] = [
                        "id"=>$item->id,
                        "attr_id"=>$item->attr_id,
                        "name"=>$item->name,
                        "image_id"=>$item->image_id,
                        "image_url"=>get_file_url($item->image_id,"full"),
                        "icon"=>$item->icon,
                    ];
                }
            }
        }
        return $model;
    }
}
