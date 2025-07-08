<?php
namespace Modules\Visa;
use Modules\Visa\Models\Visa;
use Modules\ModuleServiceProvider;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        PermissionHelper::add([
            // Visa
            'visa_view',
            'visa_create',
            'visa_update',
            'visa_delete',
            'visa_manage_others',
            'visa_manage_attributes',
        ]);
    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }

    public static function getAdminMenu()
    {
        if(!Visa::isEnable()) return [];
        return [
            'visa'=>[
                "position"=>45,
                'url'        => route('visa.admin.index'),
                'title'      => __('visa'),
                'icon'       => 'ion-logo-model-s',
                'permission' => 'visa_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('visa.admin.index'),
                        'title'      => __('All Visas'),
                        'permission' => 'visa_view',
                    ],
                    'create'=>[
                        'url'        => route('visa.admin.create'),
                        'title'      => __('Add new visa'),
                        'permission' => 'visa_create',
                    ],
                    'attribute'=>[
                        'url'        => route('visa.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'visa_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('visa.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'visa_create',
                    ],
                    'recovery'=>[
                        'url'        => route('visa.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'visa_view',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Visa::isEnable()) return [];
        return [
            'visa'=>Visa::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Visa::isEnable()) return [];
        return [
            'visa'=>[
                'class' => Visa::class,
                'name'  => __("Visa"),
                'items' => Visa::searchForMenu(),
                'position'=>51
            ]
        ];
    }

    public static function getUserMenu()
    {
        $res = [];
        if(Visa::isEnable()){
            $res['visa'] = [
                'url'   => route('visa.vendor.index'),
                'title'      => __("Manage Visa"),
                'icon'       => Visa::getServiceIconFeatured(),
                'position'   => 70,
                'permission' => 'visa_view',
                'children' => [
                    [
                        'url'   => route('visa.vendor.index'),
                        'title'  => __("All Visas"),
                    ],
                    [
                        'url'   => route('visa.vendor.create'),
                        'title'      => __("Add Visa"),
                        'permission' => 'visa_create',
                    ],
                    [
                        'url'        => route('visa.vendor.availability.index'),
                        'title'      => __("Availability"),
                        'permission' => 'visa_create',
                    ],
                    [
                        'url'   => route('visa.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'visa_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getTemplateBlocks(){
        if(!visa::isEnable()) return [];
        return [
            'form_search_visa'=>"\\Modules\\Visa\\Blocks\\FormSearchVisa",
            'list_visa'=>"\\Modules\\Visa\\Blocks\\ListVisa",
            'visa_term_featured_box'=>"\\Modules\\Visa\\Blocks\\VisaTermFeaturedBox",
        ];
    }
}
