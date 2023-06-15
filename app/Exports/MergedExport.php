<?php

namespace App\Exports;

use App\Imports\GenericCollectionImporter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MergedExport implements FromCollection
{
    public Collection $files;

    public function __construct()
    {
        $this->files = collect(['AEC - Data Export - Auer-Green-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Bartoletti PLC-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Bosco-Wiegand-2023-06-15 21-23-09.xlsx',
            "AEC - Data Export - Cummerata, O'Connell and Rowe-2023-06-15 21-23-09.xlsx",
            'AEC - Data Export - Erdman-Collier-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Glover Inc-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Gorczany Inc-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Gusikowski PLC-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Jacobson LLC-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Kreiger, Hammes and Hessel-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Leffler and Sons-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Lockman Group-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Murazik, Klocko and Armstrong-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Olson-Goodwin-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Pacocha, Cartwright and Rutherford-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Strosin, Kshlerin and Schuster-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Swift, Keeling and Auer-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Test Institution 1-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Weber-Hilpert-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Weissnat Inc-2023-06-15 21-23-09.xlsx',
            'AEC - Data Export - Wuckert LLC-2023-06-15 21-23-09.xlsx',
        ]);

    }

    public function collection()
    {
        $data = collect([]);

        foreach ($this->files as $file) {
            // open file

            $uploadedFile = new UploadedFile(Storage::path($file), $file);

            $data[] = Excel::toCollection(new GenericCollectionImporter(), $uploadedFile);
        }

        ddd($data);

        $inits = [];
        $redlines = [];
        $principles = [];

    }
}
