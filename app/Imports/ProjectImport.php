<?php

namespace App\Imports;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProjectImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function collection(Collection $collection)
    {

        $project = Project::create([
            'code' => $collection['code'],
            'name' => $collection['name'],
            'description' => $collection['description'],
            'budget' => $collection['budget'],
            'start_date' => $collection['start_date'],
            'end_date' => $collection['end_date'],
            'organisation_id' => $this->organisation->id ?? null,
        ]);

        $collection->keys()->filter(function($key) {
            return Str::startsWith($key, 'country_');
        })->each(function($key) {
            $key
        })

        // country keys
        $project->countries()->sync



    }


}
