<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoleInvite;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RoleInviteRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class RoleInviteCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;

    // email address and role cannot be updated after sending invitation, nothing can be edited,
    // better disable Edit feature to avoid possible confusion
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    use DeleteOperation { destroy as traitDestroy; }
    use ShowOperation { show as traitShow; }

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(\App\Models\RoleInvite::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/role-invite');
        CRUD::setEntityNameStrings('role invite', 'role invites');
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', RoleInvite::class);

        CRUD::column('email')->label('Sent to');
        CRUD::column('role')->type('relationship')->label('Role invited to');
        CRUD::column('inviter')->type('relationship')->label('Invited by');
        CRUD::column('invite_day')->type('date')->label('Invite sent on');
        CRUD::column('is_confirmed')->type('boolean')->label('Invite Accepted?');
    }

    protected function setupCreateOperation()
    {
        $this->authorize('create', RoleInvite::class);

        CRUD::setValidation(RoleInviteRequest::class);

        CRUD::field('email');

        // Role selection box showS Site Admin, Site Manager only
        // Invitation for institutional admin, assessor, member will be sent in Institution Members page
        $this->crud->addFields([
            [
                'name' => 'role_id',
                'type' => 'select',
                'label' => 'Role',
                'options'   => (function ($query) {
                    return $query->where('name', 'like', 'Site%')->orderBy('name', 'ASC')->get();
                }),
            ],
        ]);

        CRUD::field('inviter_id')->type('hidden')->default(Auth::user()->id);
        CRUD::field('token')->type('hidden')->default(Str::random(24));
    }

    public function show($id)
    {
        $this->authorize('view', RoleInvite::find($id));

        return $this->traitShow($id);
    }

    public function destroy($id)
    {
        $this->authorize('delete', RoleInvite::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }

}
