<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class GeneratePdfFileController extends Controller
{

    public function generatePdfFile(Request $request)
    {
        // get the URL that sent PDF generation request
        $url = $request->header('referer');

        // visit URL with logged in session, get HTML body content
        $opts = ['http' => ['header' => 'Cookie: ' . $_SERVER['HTTP_COOKIE'] . "\r\n"]];
        $context = stream_context_create($opts);
        $htmlContent = file_get_contents($url, false, $context);

        $path = Auth::id() . '__' . Carbon::now()->timestamp;

        // pass HTML body content to Browsershot, output as PDF
        Browsershot::html($htmlContent)
            ->landscape()
            ->margins(0, 0, 0, 0)
            ->waitUntilNetworkIdle()
            ->save(Storage::path("$path.pdf"));

        return redirect(Storage::url("$path.pdf"));


    }

    public function download(string $filename)
    {
        return Storage::download($filename);
    }

}
