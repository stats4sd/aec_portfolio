<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Browsershot\Browsershot;

class GeneratePdfFileController extends Controller
{

    public function generatePdfFile(Request $request) {
        // get the URL that sent PDF generation request
        $url = $request->header('referer');

        try
        {
            // visit URL with logged in session, get HTML body content
            $opts = ['http' => ['header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"]];
            $context = stream_context_create($opts);
            $htmlContent = file_get_contents($url, false, $context);

            // pass HTML body content to Browsershot, output as PDF
            $pdf = Browsershot::html($htmlContent)->pdf();

            // return PDF file to browser
            $headers = ['Content-Type' => 'application/pdf'];

            return response()->stream(function () use ($pdf) {
                echo $pdf;
            }, Response::HTTP_OK, $headers);

        }
        catch(\Exception $e)
        {
            logger($e->getMessage());

            return false;
        }

    }

}
