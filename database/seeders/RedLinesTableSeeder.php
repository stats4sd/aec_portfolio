<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RedLinesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('red_lines')->delete();

        \DB::table('red_lines')->insert(array (
            0 =>
            array (
                'created_at' => '2022-09-13 16:54:21',
                'description' => 'Project focuses on introducing GMOs and associated genome-editing technologies',
                'id' => 1,
                'name' => 'GMOs',
                'updated_at' => '2022-09-13 16:54:21',
            ),
            1 =>
            array (
                'created_at' => '2022-09-13 16:54:35',
                'description' => 'Project focuses on the promotion of synthetic fertilizers and pesticides',
                'id' => 2,
                'name' => 'Synthetics',
                'updated_at' => '2022-09-13 16:54:35',
            ),
            2 =>
            array (
                'created_at' => '2022-09-13 16:54:42',
                'description' => 'Project focuses exclusively on promoting extensive single cash crop production at the expense of diversified strategies',
                'id' => 3,
                'name' => 'Monoculture',
                'updated_at' => '2022-09-13 16:54:42',
            ),
            3 =>
            array (
                'created_at' => '2022-09-13 16:54:50',
                'description' => 'Project focuses exclusively on productivity resulting in avoidable destruction of vital ecosystems and their services',
                'id' => 4,
                'name' => 'Productivity',
                'updated_at' => '2022-09-13 16:54:50',
            ),
            4 =>
            array (
                'created_at' => '2022-09-13 16:55:00',
                'description' => 'Project actively promotes regulations/actions that hamper/destroy local and farmer-managed seed systems',
                'id' => 5,
                'name' => 'Seed Systems',
                'updated_at' => '2022-09-13 16:55:00',
            ),
            5 =>
            array (
                'created_at' => '2022-09-13 16:55:18',
            'description' => 'Project focuses on the large-scale intensification of animal production (factory farming)',
                'id' => 6,
                'name' => 'Factory Farming',
                'updated_at' => '2022-09-13 16:55:18',
            ),
            6 =>
            array (
                'created_at' => '2022-09-13 16:55:27',
                'description' => 'Project excludes or discriminate against women and other marginalised groups',
                'id' => 7,
                'name' => 'Discrimination',
                'updated_at' => '2022-09-13 16:55:27',
            ),
            7 =>
            array (
                'created_at' => '2022-09-13 16:55:37',
            'description' => 'Project focuses exclusively on promoting highly processed, industrially produced foods   (with low nutrient value)',
                'id' => 8,
                'name' => 'Processed food',
                'updated_at' => '2022-09-13 16:55:37',
            ),
            8 =>
            array (
                'created_at' => '2022-09-13 16:55:48',
                'description' => 'Project promotes extractive raw material production without some local value addition',
                'id' => 9,
                'name' => 'Extraction',
                'updated_at' => '2022-09-13 16:55:48',
            ),
            9 =>
            array (
                'created_at' => '2022-09-13 16:56:05',
                'description' => 'Project promotes approaches that violate rights, including customary rights',
                'id' => 10,
                'name' => 'Violation of Human Rights',
                'updated_at' => '2022-09-13 16:56:05',
            ),
            10 =>
            array (
                'created_at' => '2022-09-13 16:56:13',
                'description' => 'Project results in the displacement of local populations and/or land and resource grabbing',
                'id' => 11,
                'name' => 'Displacement',
                'updated_at' => '2022-09-13 16:56:13',
            ),
            11 =>
            array (
                'created_at' => '2022-09-13 16:56:26',
                'description' => 'Project ignores free prior and informed consent of affected communities',
                'id' => 12,
                'name' => 'Consent Ignored',
                'updated_at' => '2022-09-13 16:57:01',
            ),
            12 =>
            array (
                'created_at' => '2022-09-13 16:56:51',
                'description' => 'Project blocks participation of affected communities',
                'id' => 14,
                'name' => 'Participation Blocked',
                'updated_at' => '2022-09-13 16:56:51',
            ),
        ));


    }
}
