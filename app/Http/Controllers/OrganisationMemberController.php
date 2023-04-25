<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\OrganisationMemberStoreRequest;
use App\Http\Requests\OrganisationMemberUpdateRequest;

class OrganisationMemberController extends Controller
{
    public function create(Organisation $organisation)
    {
        $users = User::whereDoesntHave('organisations', function (Builder $query) use ($organisation) {
            $query->where('organisations.id', '=', $organisation->id);
        })->get();

        return view('organisations.create-members', ['organisation' => $organisation, 'users' => $users]);
    }


    /**
     * Attach users to the organisation, or send email invites to non-users.
     * New Members are automatically not admins.
     *
     * @param OrganisationMemberStoreRequest $request
     * @param Organisation $organisation
     * @return void
     */

    public function store(OrganisationMemberStoreRequest $request, Organisation $organisation)
    {
        $this->authorize('update', $organisation);

        $data = $request->validated();

        if (isset($data['users'])) {
            $organisation->users()->syncWithoutDetaching($data['users']);
        }

        if (isset($data['emails']) && count(array_filter($data['emails'])) > 0) {
            $organisation->sendInvites($data['emails']);
        }

        return redirect()->route('organisation.show', ['id' => $organisation->id]);
    }

    public function edit(Organisation $organisation, User $user)
    {

        //use the relationship to get the pivot attributes for user
        $user = $organisation->users->find($user->id);

        return view('organisations.edit-members', ['organisation' => $organisation, 'user' => $user]);
    }


    /**
     * Update the access level for existing organisation member
     *
     * @param OrganisationMemberUpdateRequest $request
     * @param Organisation $organisation
     * @param User $user
     */

    public function update(OrganisationMemberUpdateRequest $request, Organisation $organisation, User $user)
    {
        $data = $request->validated();

        $organisation->users()->syncWithoutDetaching([$user->id => ['role' => $data['role']]]);

        return redirect()->route('organisation.show', ['id' => $organisation->id]);
    }

    /**
     * Remove a user from the organisation.
     *
     * @param Organisation $organisation
     * @param User $user
     * @return void
     */
    public function destroy(Organisation $organisation, User $user)
    {
        $this->authorize('update', $organisation);

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
    public function show() {
        if (!Session::exists('selectedOrganisation')) {
            return redirect(backpack_url('select_organisation'));

        } else {
            $selectedOrganisationId = Session::get('selectedOrganisationId');

            return redirect('admin/organisation/' . $selectedOrganisationId . '/show');
        }
    }
}
