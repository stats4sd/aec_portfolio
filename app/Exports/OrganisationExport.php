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
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'geographic_reach' => $project->geographic_reach,
                'continents' => $project->continents->pluck('name')->join(', '),
                'regions' => $project->regions->pluck('name')->join(', '),
                'countries' => $project->countries->pluck('name')->join(', '),
                'sub_regions' => $project->sub_regions,
                'status' => $project->assessment_status->value,
            ]);

            foreach (RedLine::select('name', 'id')->get() as $redline) {
                $value = $project->redLines->firstWhere('id', $redline->id)->pivot->value;
                if ($value === -99) {
                    $value = "na";
                }
                $projectExport[$redline->name] = $value;
            }

            foreach (Principle::all() as $principle) {
                $principleProject = $project->principleProjects->firstWhere('principle_id', $principle->id);
                $projectExport[] = $principleProject?->rating == -99 ? 'na' : $principleProject?->rating;
                $projectExport[] = $principleProject?->rating_comment ?? '-';
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
            'start_date',
            'end_date',
            'geographic_reach',
            'continents',
            'regions',
            'countries',
            'sub_regions',
            'status'
        ];

        foreach (RedLine::select('name', 'id')->get() as $redline) {
            $headings[] = "Redline\n" . $redline->name;
        }

        foreach (Principle::all() as $principle) {
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
            'AG' => $wrap,
            'AH' => $wrap,
            'AI' => $wrap,
            'AJ' => $wrap,
            'AK' => $wrap,
            'AL' => $wrap,
            'AM' => $wrap,
            'AN' => $wrap,
            'AO' => $wrap,
            'AP' => $wrap,
            'AQ' => $wrap,
            'AR' => $wrap,
            'AS' => $wrap,
            'AT' => $wrap,
            'AU' => $wrap,
            'AV' => $wrap,
            'AW' => $wrap,
            'AX' => $wrap,
            'AY' => $wrap,
            'AZ' => $wrap,
        ];
    }

}
