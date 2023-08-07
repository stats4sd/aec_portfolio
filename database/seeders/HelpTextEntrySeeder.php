<?php

namespace Database\Seeders;

use App\Models\HelpTextEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HelpTextEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HelpTextEntry::destroy(HelpTextEntry::all()->pluck('id')->toArray());

        $helpTextEntries = [
            [
                'location' => 'My Institution - page title',
                'text' => 'This is the default landing page for users linked to your institution. Below, you can see the key information related to your institution, including the list of users, the portfolios of initiatives to be assessed, and a page of settings. See the information in the tabs below for more details. <br/><br/> If you are a member of multiple institutions, you can change institution using the link in the top-right of the page.',
            ],
            [
                'location' => 'My Institution - Institution members',
                'text' => "
                       There are 3 types of user within an institution.
                       <ul>
                        <li><b>Institutional Admin:</b> Admins have full control over the institution. They can invite new users, change the institution's settings and export or delete all data belonging to the institution from the platform.</li>
                        <li><b>Institutional Assessor:</b> Assessors can create, edit and delete initiatives and portfolios. They have full authority to perform assessments of initiatives for the institution.</li>
                        <li><b>Institutional Member:</b> A regular member has read-only access to the platform. They can view all the initiatives and results of assessments, and they can access the Dashboard to review the overall institutional performance compared with other institutions within the system.</li>
                        </ul>

                        Institutional Admins may use this page to invite new users and manage existing users, which includes the ability to change the role of a user.


                ",
            ],
            [
                'location' => 'My Institution - Portfolios',
                'text' => "Every institution needs at least one portfolio. A portfolio is a set of initiatives, but beyond that we have deliberately left the definition of a portfolio broad, so it can fit your institution's existing method of grouping initiatives. For smaller institutions with only a few initiatives, you can choose to have 1 single portfolio if you wish.",
            ],
            [
                'location' => 'My Institution - Details',
                'text' => "
                       The only required information for your institution is the name (for identification), and the default currency. The currency is used to convert initiative budgets into a single currency for display on the dashboard.<br/><br/>
                       Optionally, an institutional admin may add extra information below. The 'institution type', geographic reach and HQ country will be useful for future analysis of anonymized analysis results by the Agroecology Coalition. ",
            ],
            [
                'location' => 'Initiatives - List page',
                'text' => "
                    This page presents the initiatives entered into the platform by your institution, and provides the links to conduct the assessment. You can use the filters and search features to find a given initiative. The page presents 1 initiative per card in the list below. You can see more options by clicking the blue arrow button on the right of any card.<br/><br/>
                    Each initiative shows the current status of the assessment, and presents the 'next' action as the main green button on the card. In the expanded section, you may also edit any previously completed section of the assessment, or even choose to 'reassess' the entire initiative, which will reset the entire assessment for that initiative and allow you to start from the beginning.",
            ],
            [
                'location' => 'Initiatives - statuses',
                'text' => "
                    The main initiative status is based on the overall assessment progress. It shows the latest 'step' in the process, and the status of that step.<br/><br/>
                    In the expanded view, you will see the status for each individual step. You can also filter initiatives by the status of a specific step, for example you may wish to show all initiatives with 'Red flags - complete' and 'Principles - Not Started'.
                    ",
            ],
            [
                'location' => 'Initiatives - score',
                'text' => "
                    The overall score for the initiative is calculated as follows: <ul>
                        <li>The sum of the rating given for each principle, divided by the maximum possible score.</li>
                        <li>The maximum score is 2 per principle. It takes into account any principles marked as NA. For example, an initiative where all 13 principles are relevant will be marked out of 13 x 2 = 26. If 2 principles are marked as NA, the initiative will be marked out of 11 x 2 = 22.</li>
</ul>
                    ",
            ],
            [
                'location' => 'Dashboard - page title',
                'text' => "
                    This page presents an overall summary of your institution's initiatives and compares your assessment results against the results from other institutions that use this tool. You can navigate the tabs below to review a summary of your overall portfolio ('Summary of Initaitives'), and the results of each step of the main assessment ('Summary of Red Flags' and 'Summary of Principles'). See the help text on each tab for more information.<br/><br/>
                    You may filter the results shown in 2 ways:
                    <ul>
                    <li>You may restrict the results to a single portfolio. In this case, 'your' results will be filtered to only the selected portfolio. The comparision against other institutions will remain unchanged./li>
                    <li>You may add one or more filters based on the initiative properties, including location, category, date and budget. These filters will be applied to both your initiatives and initiatives from other institutions. For example, you can compare all your initiatives that started between 2020 and 2023 with all other institutions' initiatives from the same time period.</li>
                    </ul>
                ",
            ],
            [
                'location' => 'Dashboard - initiatives added',
                'text' => "The number of initiatives your institution has added that match the current set of filters.",
            ],
            [
                'location' => 'Dashboard - Passed all red flags',
                'text' => "The number of initiatives that have completed and passed the red flag assessment.",
            ],
            [
                'location' => 'Dashboard - Fully assessed',
                'text' => "The number of initiatives that have completed the full assessment. This means the initiative has either failed the red flags assessment, or passed the red flags and completed the principle assessment step.",
            ],
            [
                'location' => 'Dashboard - overall score',
                'text' => "
                    The overall score is calculated as the average score of all fully assessed initiatives. Initiatives that failed the red flag assessment have a score of 0%. Other initiatives have a score calculated as follows:
                    <ul><li>The sum of ratings for each principle, divided by the total possible score and expressed as a percentage.</li></ul>",
            ],
            [
                'location' => 'Dashboard - AE focused budget',
                'text' => "The Agroecology-focused budget is calculated as the total budget multipled by the overall score as a percentage. So if your overall score is 50%, the Agroecology-focused budget will be 1/2 of the total budget.",
            ],
            [
                'location' => 'Dashboard - Redflags summary',
                'text' => "
                    The table below shows the summary of results from the red flags portion of the assessment. For each red flag, the table shows the % of your initiatives that passed, and the % of initiatives from other institutions that passed. The colours of the rows highlight the performance of your initiatives - green means 100% of your initiatives passed, and yellow shows where some % of your initiatives failed that red flag.
                ",
            ],
            [
                'location' => 'Dashboard - Summary of Principles',
                'text' => "
                    This tab presents a visual summary of your initiatives' performance in each of the 13 Principles of Agroecology. Each colour shows the % of initiatives that scored in a certain range for that principle.
                    <ul>
                    <li>The green bar is the % of initiatives that scored 1.5 or higher</li>
                    <li>The yellow bar is the % of initiatives that scored between 0.5 and 1.5 or higher</li>
                    <li>The red bar is the % of initiatives that scored lower than 0.5</li>
                    </ul>
                   The 3 coloured segments add up to 100% of the initiatives for which that principle applied.   <br/><br/>
                   There is also a grey line under some of the principle names - this line represents the % of initiatives that marked the principle as 'not applicable' in the assessment. <br/><br/>
                   You can hover over the graph to view the actual %s for each principle. You may also hide any of the categories by clicking on that category in the legend above the graph. For example, to view the graph without the grey 'na' line, you can click the grey square and 'NA' label. Click again to display it.
                ",
            ],
        ];

        foreach ($helpTextEntries as $helpTextEntry) {
            HelpTextEntry::create($helpTextEntry);
        }
    }
}
