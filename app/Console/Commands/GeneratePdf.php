<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class ResetDatabase extends Command
{

    protected $signature = 'generatepdf';

    protected $description = 'Generate PDF';

    public function handle(): int
    {
        try{
            Browsershot::url('http://aec.test/admin/organisation/2/portfolio')
            ->savePdf('c:\temp\portfolio_01.pdf');

            Browsershot::url('http://aec.test/admin/organisation/2/portfolio')
            ->authenticate('dan@stats4sd.org', 'password')
            ->savePdf('c:\temp\portfolio_02.pdf');
        }
        catch(\Exception $e)
        {
            $this->info($e->getmessage());

            return false;
        }

        return self::SUCCESS;
    }
}
