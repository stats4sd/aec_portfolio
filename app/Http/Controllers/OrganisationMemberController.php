<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Organisation;
use App\Models\OrganisationMember;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\OrganisationMemberStoreRequest;
use App\Http\Requests\OrganisationMemberUpdateRequest;

class OrganisationMemberController extends Controller
{
    public function create(Organisation $organisation)
    {
        $this->authorize('create', OrganisationMember::class);

        $users = User::whereDoesntHave('organisations', function (Builder $query) use ($organisation) {
            $query->where('organisations.id', '=', $organisation->id);
        })->get();

        $institutionalRoles = Role::where('name', 'like', 'Institutional%')->orderBy('name', 'ASC')->get();

        return view('organisations.create-members', [
            'organisation' => $organisation,
            'users' => $users,
            'institutionalRoles' => $institutionalRoles,
        ]);
    }


    public function store(OrganisationMemberStoreRequest $request, Organisation $organisation)
    {
        $this->authorize('create', OrganisationMember::class);

        $data = $request->validated();

        if (isset($data['users'])) {
            $organisation->users()->syncWithoutDetaching($data['users']);
        }

        if (isset($data['emails']) && count(array_filter($data['emails'])) > 0) {
            $organisation->sendInvites($data['emails'], $data['role_id']);
        }

        return redirect()->route('organisation.show', ['id' => $organisation->id]);
    }


    public function edit(Organisation $organisation, User $user)
    {
        $this->authorize('update', OrganisationMember::class);

        //use the relationship to get the pivot attributes for user
        $user = $organisation->users->find($user->id);

        $institutionalRoles = Role::where('name', 'like', 'Institutional%')->orderBy('name', 'ASC')->get();

        return view('organisations.edit-members', [
            'organisation' => $organisation,
            'user' => $user,
            'institutionalRoles' => $institutionalRoles,
        ]);
    }


    public function update(OrganisationMemberUpdateRequest $request, Organisation $organisation, User $user)
    {
        $this->authorize('update', OrganisationMember::class);

        $data = $request->validated();

        // remove old role
        $user->removeRole($data['old_system_role']);

        // add new role
        $user->assignRole($data['new_system_role']);

        return redirect()->route('organisation.show', ['id' => $organisation->id]);
    }


    public function destroy(Organisation $organisation, User $user)
    {
        $this->authorize('delete', OrganisationMember::class);

        $admins = $organisation->admins()->get();
        // if the $user is a $organisation admin AND is the ONLY organisation admin... prevent
        if ($admins->contains($user) && $admins->count() == 1) {
            \Alert::add('error', 'User not removed - you must keep at least one institution admin to manage your institution')->flash();
        } else {
            $organisation->users()->detach($user->id);
            //ShareFormsWithExistingOrganisationMembers::dispatch($organisation);
            \Alert::add('success', 'User ' . $user->name . ' successfully removed from the institution')->flash();
        }

        return redirect()->route('organisation.show', [$organisation, 'members']);
    }


    // redirect to organisation members page of the selected organisation
    public function show()
    {
        $this->authorize('viewAny', OrganisationMember::class);

        $selectedOrganisationId = Session::get('selectedOrganisationId');

        return redirect('admin/organisation/' . $selectedOrganisationId . '/show');

    }
}
