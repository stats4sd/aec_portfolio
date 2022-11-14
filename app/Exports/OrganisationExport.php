<?php

namespace App\Exports;

use App\Models\Organisation;
use App\Models\Principle;
use App\Models\RedLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SebastianBergmann\CodeCoverage\Report\Xml\Tests;

class OrganisationExport implements FromCollection, WithHeadings, WithStrictNullComparison, ShouldAutoSize, WithStyles
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function collection()
    {
        $projects = $this->organisation->projects;

        $projects->load('redLines', 'principleProjects');

        $export = [];

        foreach ($projects as $project) {
            $projectExport = collect([
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'description' => $project->description,
                'budget' => $project->budget,
                'status' => $project->assessment_status->value,
            ]);

            foreach (RedLine::select('name', 'id')->get() as $redline) {
                $value = $project->redLines->firstWhere('id', $redline->id)->pivot->value;
                if($value === -99) {
                    $value = "na";
                }
                $projectExport[$redline->name] = $value;
            }

            foreach(Principle::all() as $principle) {
                $principleProject = $project->principleProjects->firstWhere('id', $principle->id);
                $export[] = $principleProject->rating ?? 'na';
                $export[] = $principleProject->rating_comment ?? '-';
            }

            $export[] = $projectExport;
        }
        return collect($export);
    }

    public function headings(): array
    {
        $headings = [
            'id',
            'code',
            'name',
            'description',
            'budget',
            'status'
        ];

        foreach (RedLine::select('name', 'id')->get() as $redline) {
            $headings[] = "Redline\n" . $redline->name;
        }

        foreach(Principle::all() as $principle) {
            $headings[] = "Principle\n" . $principle->name . ' ( Rating )';
            $headings[] = "Principle\n" . $principle->name . ' ( Comment )';
        }

        return $headings;

    }

    public function styles(Worksheet $sheet)
    {
        $wrap = ['alignment' => ['wrapText' => true]];
        $bold = ['font' => ['bold' => true]];
        return [
            1 => $bold,
            'G' => $wrap,
            'H' => $wrap,
            'I' => $wrap,
            'J' => $wrap,
            'K' => $wrap,
            'L' => $wrap,
            'M' => $wrap,
            'N' => $wrap,
            'O' => $wrap,
            'P' => $wrap,
            'Q' => $wrap,
            'R' => $wrap,
            'S' => $wrap,
            'T' => $wrap,
            'U' => $wrap,
            'V' => $wrap,
            'W' => $wrap,
            'X' => $wrap,
            'Y' => $wrap,
            'Z' => $wrap,
            'AA' => $wrap,
            'AB' => $wrap,
            'AC' => $wrap,
            'AD' => $wrap,
            'AE' => $wrap,
            'AF' => $wrap,
        ];
    }

}
