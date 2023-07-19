<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Support\Str;

trait UsesSaveAndNextAction
{

    // helper function - written so the removeAllSaveActions is more inline with the others below
    public function removeAllSaveActions(): void
    {
        $this->crud->removeAllSaveActions();
    }


    // You MUST call this AFTER creating all the tabbed fields
    public function addSaveAndNextAction($operationRoute = 'edit', $finalRedirect = null): void
    {

        $tabs = $this->crud->getTabs();
        $this->crud->set('tabs', $tabs);
        $this->crud->set('operationRoute', $operationRoute);

        $this->crud->addSaveAction([
            'name' => 'save_and_next',
            'redirect' => function ($crud, $request, $itemId) use ($finalRedirect) {
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
                return $finalRedirect ?? $crud->route ;
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

    public function addSaveAndReturnToProjectListAction(): void
    {
        $this->crud->addSaveAction([
            'name' => 'save_and_return_to_projects',
            'redirect' => function ($crud, $request, $itemId){
                return backpack_url('project');
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Save and Return', // override text appearing on the button
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
