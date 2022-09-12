<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Support\Str;

trait UsesSaveAndNextAction
{

//    public function saveTabs()
//    {
//        $this->crud->set('tabs')
//    }

    // You MUST call this AFTER creating all the tabbed fields
    public function setupCustomSaveActions($operationRoute = 'edit')
    {

        $tabs = $this->crud->getTabs();
        $this->crud->set('tabs', $tabs);
        $this->crud->set('operationRoute', $operationRoute);

        $this->crud->replaceSaveActions([
            'name' => 'save_and_next',
            'redirect' => function ($crud, $request, $itemId){
                $tabs = $this->crud->get('tabs');
                $lastTab = $tabs[count($tabs) - 1];

                $nextTabs = [];

                for($i = 0, $iMax = count($tabs); $i < $iMax-1; $i++) {

                    $current = Str::slug($tabs[$i]);
                    $next = Str::slug($tabs[$i+1]);

                    $nextTabs[$current] = $next;

                }

                if ($request->_current_tab && $request->_current_tab !== Str::slug($lastTab)) {
                    $next_tabs = $nextTabs;
                    $route = $crud->get('operationRoute');
                    if (isset($next_tabs[$request->_current_tab])) {
                        return $crud->route . "/" . $itemId . "/". $route ."#" . $next_tabs[$request->_current_tab];
                    }
                }
                return $crud->route ;
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Save and Next', // override text appearing on the button
            // You can also provide translatable texts, for example:
            // 'button_text' => trans('backpack::crud.save_action_one'),
            'visible' => function ($crud) {
                return true;
            }, // customize when this save action is visible for the current operation
            'referrer_url' => function ($crud, $request, $itemId) {
                return $crud->route;
            }, // override http_referrer_url
            'order' => 1, // change the order save actions are in
        ]);
    }
}
