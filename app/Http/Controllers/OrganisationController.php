<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Country;
use App\Jobs\ExportOrgData;
use App\Models\Organisation;
use App\Models\InstitutionType;
use App\Enums\GeographicalReach;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\OrganisationRequest;
use App\Exports\Assessment\AssessmentExportWorkbook;

class OrganisationController extends Controller
{

    public function publicIndex()
    {
        return Organisation::withCount(['projects'])->get();
    }

    public function show()
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'))->load([
            'portfolios.projects.assessments' => [
                'failingRedlines',
                'project.organisation',
                'principles',
            ],
            'signee',
        ]);
        $institutionTypes = InstitutionType::all();
        $geographicReaches = GeographicalReach::cases();
        $countries = Country::all();

        // get session stored tab
        $tab = Str::replace('#', '', Session::get('organisation.tab'));


        return view('organisations.show', [
            'organisation' => $organisation,
            'institutionTypes' => $institutionTypes,
            'geographicReaches' => $geographicReaches,
            'countries' => $countries,
            'tab' => $tab,
        ]);
    }

    // can only be used when an organisation is selected in the current session
    // axios request
    public function update(OrganisationRequest $request)
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'));

        $validated = $request->validated();

        if (!$request->has('has_additional_criteria')) {
            $validated['has_additional_criteria'] = false;
        }

        // Agreement should be signed only when it is not yet signed.
        // Once it is signed, it is not necessary to sign again
        if (isset($validated['agreement']) && $organisation->agreement_signed_at == null) {
            unset($validated['agreement']);
            $validated['agreement_signed_at'] = Carbon::now();
            $validated['signee_id'] = Auth::user()->id;
        }

        $organisation->update($validated);

        return $organisation->load([
            'portfolios.projects.assessments' => [
                'failingRedlines',
                'project.organisation',
                'principles',
            ],
            'signee',
        ]);
    }

    public function saved()
    {
        \Alert::add('success', 'Organisation settings saved')->flash();
        return redirect('admin/organisation/show');
    }

    public function export()
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'));

        return Excel::download(new AssessmentExportWorkbook($organisation), "Agroecology Funding Tool - Export - " . $organisation->name . "-" .
            Carbon::now()->toDateTimeString() . ".xlsx");
    }

    public function exportAll()
    {
        $this->authorize('viewAny', Organisation::class);

        foreach (Organisation::all() as $organisation) {

            $timestamp = Carbon::now()->toDateTimeString();

            ExportOrgData::dispatch($organisation, $timestamp);
        }

        return 'done - queued';
    }

    public function mergeAll()
    {
        return Excel::download(new MergedExport(), 'test.xlsx');
    }


    public function storeTab()
    {
        Session::put('organisation.tab', request()?->get('tab'));

        return response('success', 200);
    }
}
