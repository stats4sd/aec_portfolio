<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class GeneratePdfFileController extends Controller
{

    public function generatePdfFile(string $url, string $name = null)
    {
        // get the URL that sent PDF generation request


        // visit URL with logged in session, get HTML body content
        //$opts = ['http' => ['header' => 'Cookie: ' . $_SERVER['HTTP_COOKIE'] . "\r\n"]];
        //$context = stream_context_create($opts);
        //$htmlContent = file_get_contents($url, false, $context);

        $path = $name ?? Auth::id() . '__' . Carbon::now()->timestamp;

        // pass HTML body content to Browsershot, output as PDF
        Browsershot::url($url)
                ->authenticate(config('services.browsershot.auth.email'), config('services.browsershot.auth.password'))
 //           ->landscape()
//            ->margins(0, 0, 0, 0)
//            ->waitUntilNetworkIdle()
            ->save(Storage::path("prints/$path.pdf"));

        return redirect(Storage::url("prints/$path.pdf"));


    }

    public function generateInitiativeSummary(Project $project)
    {
        $filename = $project->name . '-summary-' . Carbon::now()->toDateString();

        return $this->generatePdfFile(route('project.show-as-pdf', ['id' => $project->id]), $filename);
    }

    public function generateAssessmentSummary(Assessment $assessment)
    {
        $filename = $assessment->project->name . '-assessed-on-' . $assessment->completed_at . '-summary-' . Carbon::now()->toDateString();

        return $this->generatePdfFile(route('assessment.show-as-pdf', ['id' => $assessment->id]), $filename);
    }

    public function download(string $filename)
    {
        return Storage::download($filename);
    }

}
