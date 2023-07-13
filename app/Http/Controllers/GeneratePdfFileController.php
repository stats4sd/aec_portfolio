<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class GeneratePdfFileController extends Controller
{

    public function generatePdfFile(Request $request, string $name = null)
    {
        // get the URL that sent PDF generation request
        $url = $request->header('referer');

        // visit URL with logged in session, get HTML body content
        $opts = ['http' => ['header' => 'Cookie: ' . $_SERVER['HTTP_COOKIE'] . "\r\n"]];
        $context = stream_context_create($opts);
        $htmlContent = file_get_contents($url, false, $context);

        $path = $name ?? Auth::id() . '__' . Carbon::now()->timestamp;

        // pass HTML body content to Browsershot, output as PDF
        Browsershot::html($htmlContent)
//            ->landscape()
//            ->margins(0, 0, 0, 0)
//            ->waitUntilNetworkIdle()
            ->save(Storage::path("prints/$path.pdf"));

        return redirect(Storage::url("prints/$path.pdf"));


    }

    public function generateInitiativeSummary(Project $project)
    {
        $filename = $project->name . '-summary-' . Carbon::now()->toDateString();

        return $this->generatePdfFile(request(), $filename);
    }

    public function download(string $filename)
    {
        return Storage::download($filename);
    }

}
