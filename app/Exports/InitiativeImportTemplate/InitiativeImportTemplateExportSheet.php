<?php

namespace App\Exports\InitiativeImportTemplate;

use App\Models\Organisation;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InitiativeImportTemplateExportSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{

    use RegistersEventListeners;

    public function __construct(public Organisation $organisation)
    {
    }

    public function collection(): Collection
    {
        return collect([
            [
                'portfolio' => 'select the portfolio. The dropdown list includes the portfolios your institution has created on the web platform',
                'code' => 'enter a unique code for the initiative.',
                'name' => 'enter the name of the initiative',
                'category' => 'select the category of the initiative',
                'description' => 'enter a description of the initiative (optional)',
                'currency' => 'enter the 3-letter currency code of the initiative',
                'exchange_rate' => '1 of this initiative\'s currency = XXX of your institution\'s default currency',
                'budget' => 'enter the budget of the initiative in the initiative\'s currency',
                'uses_only_own_funds' => 'enter yes if the initiative uses only your institution\'s funds, no if the funds come from other sources.',
                'main_recipient' => 'the institution or entity t hat directly receives the majority of the funds for this initiative',
                'start_date' => 'enter the start date of the initiative in the format YYYY-MM-DD',
                'end_date' => 'enter the end date of the initiative in the format YYYY-MM-DD. (optional)',
                'geographic_reach' => 'select the geographic reach of the initiative',
                'continent_1' => 'select the continent(s)',
                'continent_2' => 'if there are more than 2 continents, you can add new columns. Please title them in the same format (continent_3, continent_4, etc.)',
                'region_1' => 'select the region(s)',
                'region_2' => 'if there are more than 2 regions, you can add new columns. Please title them in the same format (region_3, region_4, etc.)',
                'country_1' => 'select the country(ies)',
                'country_2' => 'if there are more than 4 countries, you can add new columns. Please title them in the same format (country_5, country_6, etc.)',
            ],
            [
                'Portfolio Name',
                'example',
                'initiative name',
                'Field projects',
                'description of the initiative',
                'EUR',
                '1',
                '100000',
                'yes',
                'Self',
                '2021-01-01',
                '2021-12-31',
                'Global',
                'Africa',
                '',
                'Eastern Africa',
                'Western Africa',
                'Burkina Faso',
                'Ghana',
                'Mali',
                'Niger',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'portfolio',
            'code',
            'name',
            'category',
            'description',
            'currency',
            'exchange_rate',
            'budget',
            'uses_only_own_funds',
            'main_recipient',
            'start_date',
            'end_date',
            'geographic_reach',
            'continent_1',
            'continent_2',
            'region_1',
            'region_2',
            'country_1',
            'country_2',
            'country_3',
            'country_4',
        ];
    }

    public function title(): string
    {
        return 'initiatives';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
            // Style the second row as small faded text.
            2 => [
                'fill' => [
                    'color' => [
                        'rgb' => 'F2F2F2',
                    ],
                    'fillType' => Fill::FILL_SOLID,
                ],
                'font' => [
                    'size' => 8,
                ],
                'alignment' => [
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 10,
            'C' => 20,
            'D' => 15,
            'E' => 20,
            'F' => 10,
            'G' => 15,
            'H' => 10,
            'I' => 19,
            'J' => 18,
            'K' => 14,
            'L' => 14,
            'M' => 15,
            'N' => 15,
            'O' => 15,
            'P' => 15,
            'Q' => 15,
            'R' => 15,
            'S' => 15,
            'T' => 15,
            'U' => 15,
        ];
    }

    // after the sheet generation, add custom validation to setup dropdown lists in the main worksheet
    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();

        $portfolioVal = self::createDefaultDropdownValidation();
        $portfolioVal->setError('The entered value is not in the list of portfolios for your institution.');
        $portfolioVal->setPromptTitle('Select Portfolio');
        $portfolioVal->setPrompt('Please select from the list of your institution\'s portfolios');
        $portfolioVal->setFormula1('\'portfolios\'!$A:$A');

        $categoryVal = self::createDefaultDropdownValidation();
        $categoryVal->setError('The entered value is not in the list of initiative categories.');
        $categoryVal->setPromptTitle('Select Category');
        $categoryVal->setPrompt('Please select from the list of available categories. If none fit, please select \'Other\'.');
        $categoryVal->setFormula1('\'initiative_categories\'!$A:$A');

        $ynVal = self::createDefaultDropdownValidation();
        $ynVal->setError('Please enter yes or no.');
        $ynVal->setPromptTitle('Yes or No');
        $ynVal->setPrompt('Please select yes or no.');
        $ynVal->setFormula1('\'yes_no\'!$A:$A');

        $geoVal = self::createDefaultDropdownValidation();
        $geoVal->setError('The entered value is not in the list of geographic reaches.');
        $geoVal->setPromptTitle('Select Geographic Reach');
        $geoVal->setPrompt('Please select from the list of available geographic reaches.');
        $geoVal->setFormula1('\'geographic_reaches\'!$A:$A');

        $continentVal = self::createDefaultDropdownValidation();
        $continentVal->setError('The entered value is not in the list of continents.');
        $continentVal->setPromptTitle('Select Continent');
        $continentVal->setPrompt('Please select from the list of continents.');
        $continentVal->setFormula1('\'countries\'!$A:$A');

        $regionVal = self::createDefaultDropdownValidation();
        $regionVal->setError('The entered value is not in the list of regions.');
        $regionVal->setPromptTitle('Select Region');
        $regionVal->setPrompt('Please select from the list of regions. You can start typing to filter the list.');
        $regionVal->setFormula1('\'countries\'!$B:$B');

        $countryVal = self::createDefaultDropdownValidation();
        $countryVal->setError('The entered value is not in the list of countries.');
        $countryVal->setPromptTitle('Select Country');
        $countryVal->setPrompt('Please select from the list of countries. You can start typing to filter the list');
        $countryVal->setFormula1('\'countries\'!$C:$C');

        for ($i = 3; $i <= 1000; $i++) {
            $sheet->setDataValidation("A$i", $portfolioVal);
            $sheet->setDataValidation("D$i", $categoryVal);
            $sheet->setDataValidation("I$i", $ynVal);
            $sheet->setDataValidation("M$i", $geoVal);
            $sheet->setDataValidation("N$i", $continentVal);
            $sheet->setDataValidation("O$i", $continentVal);
            $sheet->setDataValidation("P$i", $regionVal);
            $sheet->setDataValidation("Q$i", $regionVal);
            $sheet->setDataValidation("R$i", $countryVal);
            $sheet->setDataValidation("S$i", $countryVal);
            $sheet->setDataValidation("T$i", $countryVal);
            $sheet->setDataValidation("U$i", $countryVal);

            // update date columns to use sensible ISO format;
            $sheet->getCell("K$i")->setDataType(DataType::TYPE_ISO_DATE);
            $sheet->getStyle("K$i")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

            $sheet->getCell("L$i")->setDataType(DataType::TYPE_ISO_DATE);
            $sheet->getStyle("L$i")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        }


    }

    public static function createDefaultDropdownValidation(): DataValidation
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input Error');

        return $validation;
    }
}
