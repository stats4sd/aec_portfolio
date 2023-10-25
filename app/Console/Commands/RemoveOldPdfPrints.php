<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveOldPdfPrints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-old-pdf-prints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to automatically remove pdf prints that were generated more than 1 week ago. Should be run weekly.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = Storage::files('prints');

        $lastWeekTimeStamp = Carbon::now()->subWeek()->timestamp;

        foreach($files as $file) {

            if(Storage::lastModified($file) < $lastWeekTimeStamp) {
                Storage::delete($file);
            }
        }

    }
}
