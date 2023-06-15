<?php

namespace App\Jobs;

use App\Exports\Assessment\AssessmentExportWorkbook;
use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportOrgData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Organisation $organisation, public string $timestamp)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Excel::store(new AssessmentExportWorkbook($this->organisation), "AEC - Data Export - " . $this->organisation->name . "-" .
            $this->timestamp . ".xlsx");
    }
}
