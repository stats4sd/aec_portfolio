<?php

namespace App\Http\Controllers;

use App\Exports\Assessment\AssessmentExportWorkbook;
use App\Http\Requests\OrganisationRequest;
use App\Jobs\ExportOrgData;
use App\Models\Organisation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class OrganisationController extends Controller
{

    public function publicIndex()
    {
        return Organisation::withCount(['projects'])->get();
    }

    public function show()
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'))->load('portfolios.projects');

        return view('organisations.show', ['organisation' => $organisation]);

    }

    // can only be used when an organisation is selected in the current session
    public function update(OrganisationRequest $request)
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'));


        $validated = $request->validated();
        if(!$request->has('has_additional_criteria')) {
            $validated['has_additional_criteria'] = false;
        }

        $organisation->update($validated);

        return redirect()->route('organisation.self.show');
    }

    public function export()
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'));

        return Excel::download(new AssessmentExportWorkbook($organisation), "AEC - Data Export - " . $organisation->name . "-" .
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


}
