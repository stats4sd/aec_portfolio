<?php

namespace App\Http\Controllers;

use Spatie\Browsershot\Browsershot;

class GeneratePdfFileController extends Controller
{

    public function generatePdfFile() {
        logger("GeneratePdfFileController.generatePdfFile()...");

        try
        {
            Browsershot::url('http://aec.test/admin/organisation/2/portfolio')
            ->savePdf('c:\temp\portfolio_01.pdf');

            Browsershot::url('http://aec.test/admin/organisation/2/portfolio')
            ->authenticate('dan@stats4sd.org', 'password')
            ->savePdf('c:\temp\portfolio_02.pdf');

            Browsershot::html('<h1>Hello World</h1>')
            ->savePdf('c:\temp\html_01.pdf');
        }
        catch(\Exception $e)
        {
            logger($e->getmessage());

            return false;
        }

    }

}
