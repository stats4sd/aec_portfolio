<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScoreTagsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('score_tags')->delete();

        \DB::table('score_tags')->insert(array (
            0 =>
            array (
                'id' => 1,
                'principle_id' => 1,
            'name' => 'Closing nutrient cycles through biomass recycling - at farm or landscape level depending on context (e.g. produce and use own compost, manure incl humanure, biofertiliser)',
            'description' => 'Closing nutrient cycles through biomass recycling - at farm or landscape level depending on context (e.g. produce and use own compost, manure incl humanure, biofertiliser)',
                'created_at' => '2022-09-13 17:08:31',
                'updated_at' => '2022-09-13 17:08:31',
            ),
            1 =>
            array (
                'id' => 2,
                'principle_id' => 1,
                'name' => 'Wastewater recycling',
                'description' => 'Wastewater recycling',
                'created_at' => '2022-09-13 17:08:44',
                'updated_at' => '2022-09-13 17:08:44',
            ),
            2 =>
            array (
                'id' => 3,
                'principle_id' => 1,
                'name' => 'Rainwater harvesting',
                'description' => 'Rainwater harvesting',
                'created_at' => '2022-09-13 17:08:55',
                'updated_at' => '2022-09-13 17:08:55',
            ),
            3 =>
            array (
                'id' => 4,
                'principle_id' => 1,
                'name' => 'Reusable or recyclable packaging',
                'description' => 'Reusable or recyclable packaging',
                'created_at' => '2022-09-13 17:09:01',
                'updated_at' => '2022-09-13 17:09:11',
            ),
            4 =>
            array (
                'id' => 5,
                'principle_id' => 2,
            'name' => 'Water harvesting, storage and efficient water management (e.g. drip irrigation, rainwater harvester ponds )',
            'description' => 'Water harvesting, storage and efficient water management (e.g. drip irrigation, rainwater harvester ponds )',
                'created_at' => '2022-09-13 17:09:20',
                'updated_at' => '2022-09-13 17:09:20',
            ),
            5 =>
            array (
                'id' => 6,
                'principle_id' => 2,
            'name' => 'Eliminate or actively reduce use of plastic (e.g. packaging, mulch)',
            'description' => 'Eliminate or actively reduce use of plastic (e.g. packaging, mulch)',
                'created_at' => '2022-09-13 17:09:26',
                'updated_at' => '2022-09-13 17:09:26',
            ),
            6 =>
            array (
                'id' => 7,
                'principle_id' => 2,
                'name' => 'Reduce energy consumption and/or produce renewable energy for domestic use on farm',
            'description' => 'Reduce energy consumption and/or produce renewable energy for domestic use on farm (i.e. not for export), incl producing wood and other for fuel, reducing vehicle use, reducing digital implements & use of renewable energies (e.g. solar electricity, biogas from animal manure)',
                'created_at' => '2022-09-13 17:09:43',
                'updated_at' => '2022-09-13 17:09:43',
            ),
            7 =>
            array (
                'id' => 8,
                'principle_id' => 2,
                'name' => 'Eliminate or actively /significantly reduce synthetic fertilisers',
                'description' => 'Eliminate or actively /significantly reduce synthetic fertilisers',
                'created_at' => '2022-09-13 17:09:54',
                'updated_at' => '2022-09-13 17:09:54',
            ),
            8 =>
            array (
                'id' => 9,
                'principle_id' => 2,
                'name' => 'Eliminate or actively/significantly reduce synthetic pesticides and veterinary drugs',
                'description' => 'Eliminate or actively/significantly reduce synthetic pesticides and veterinary drugs',
                'created_at' => '2022-09-13 17:09:57',
                'updated_at' => '2022-09-13 17:09:57',
            ),
            9 =>
            array (
                'id' => 10,
                'principle_id' => 2,
            'name' => 'Eliminate or actively/significantly reduce industrial/imported feed (e.g. from outside the territory, highly processed, with additives)',
            'description' => 'Eliminate or actively/significantly reduce industrial/imported feed (e.g. from outside the territory, highly processed, with additives)',
                'created_at' => '2022-09-13 17:10:02',
                'updated_at' => '2022-09-13 17:10:02',
            ),
            10 =>
            array (
                'id' => 11,
                'principle_id' => 2,
                'name' => 'Use farm-saved seeds or community seed banks or seed saver networks',
                'description' => 'Use farm-saved seeds or community seed banks or seed saver networks',
                'created_at' => '2022-09-13 17:10:09',
                'updated_at' => '2022-09-13 17:10:09',
            ),
            11 =>
            array (
                'id' => 12,
                'principle_id' => 2,
            'name' => 'Deliberately use preventative methods (e.g. nitrogen fixing plants, biological pest management, production of natural remedies)',
            'description' => 'Deliberately use preventative methods (e.g. nitrogen fixing plants, biological pest management, production of natural remedies)',
                'created_at' => '2022-09-13 17:10:16',
                'updated_at' => '2022-09-13 17:10:16',
            ),
            12 =>
            array (
                'id' => 13,
                'principle_id' => 2,
                'name' => 'Produce fibre and building materials on-farm for own use',
                'description' => 'Produce fibre and building materials on-farm for own use',
                'created_at' => '2022-09-13 17:10:24',
                'updated_at' => '2022-09-13 17:10:24',
            ),
            13 =>
            array (
                'id' => 14,
                'principle_id' => 2,
            'name' => 'Elimination of heavy, soil (structure) damaging machinery',
            'description' => 'Elimination of heavy, soil (structure) damaging machinery',
                'created_at' => '2022-09-13 17:10:29',
                'updated_at' => '2022-09-13 17:10:29',
            ),
            14 =>
            array (
                'id' => 15,
                'principle_id' => 3,
                'name' => 'Holistic approach using multiple practices to deliberately enhance soil health incl carbon sequestration',
            'description' => 'Holistic approach using multiple practices to deliberately enhance soil health incl carbon sequestration ( e.g. cover crops, permaculture, integrated diversified farming, organic agriculture, animal integration for manure, ...)',
                'created_at' => '2022-09-13 17:10:47',
                'updated_at' => '2022-09-13 17:10:47',
            ),
            15 =>
            array (
                'id' => 16,
                'principle_id' => 3,
            'name' => 'Land use management & prevention of soil erosion (ex. terracing, zai pits, minimum tillage…)',
            'description' => 'Land use management & prevention of soil erosion (ex. terracing, zai pits, minimum tillage…)',
                'created_at' => '2022-09-13 17:10:53',
                'updated_at' => '2022-09-13 17:10:53',
            ),
            16 =>
            array (
                'id' => 17,
                'principle_id' => 3,
                'name' => 'Deliberate fallow periods',
                'description' => 'Deliberate fallow periods',
                'created_at' => '2022-09-13 17:11:00',
                'updated_at' => '2022-09-13 17:11:00',
            ),
            17 =>
            array (
                'id' => 18,
                'principle_id' => 3,
                'name' => 'Monitor/assess soil health and biological activity to evaluate practices',
                'description' => 'Monitor/assess soil health and biological activity to evaluate practices',
                'created_at' => '2022-09-13 17:11:05',
                'updated_at' => '2022-09-13 17:11:05',
            ),
            18 =>
            array (
                'id' => 19,
                'principle_id' => 4,
                'name' => 'Work with resilient, locally adapted and naturally healthful breeds & promote responsible research on these',
                'description' => 'Work with resilient, locally adapted and naturally healthful breeds & promote responsible research on these',
                'created_at' => '2022-09-13 17:11:12',
                'updated_at' => '2022-09-13 17:11:12',
            ),
            19 =>
            array (
                'id' => 20,
                'principle_id' => 4,
                'name' => 'Align number of animals to carrying capacity of the land',
                'description' => 'Align number of animals to carrying capacity of the land',
                'created_at' => '2022-09-13 17:11:17',
                'updated_at' => '2022-09-13 17:11:17',
            ),
            20 =>
            array (
                'id' => 21,
                'principle_id' => 4,
            'name' => 'Species-appropriate environment (free range, grass-fed ruminants, foraging fowl, outdoors ideally all year round)',
            'description' => 'Species-appropriate environment (free range, grass-fed ruminants, foraging fowl, outdoors ideally all year round)',
                'created_at' => '2022-09-13 17:11:22',
                'updated_at' => '2022-09-13 17:11:34',
            ),
            21 =>
            array (
                'id' => 22,
                'principle_id' => 4,
                'name' => 'High standards of welfare: free from stress, hunger, thirst',
                'description' => 'High standards of welfare: free from stress, hunger, thirst',
                'created_at' => '2022-09-13 17:11:27',
                'updated_at' => '2022-09-13 17:11:27',
            ),
            22 =>
            array (
                'id' => 23,
                'principle_id' => 4,
            'name' => 'No breeding-related handicaps (e.g. brittle bones, hip problems, inability to birth naturally, or proclivities to particular diseases)',
            'description' => 'No breeding-related handicaps (e.g. brittle bones, hip problems, inability to birth naturally, or proclivities to particular diseases)',
                'created_at' => '2022-09-13 17:11:40',
                'updated_at' => '2022-09-13 17:11:40',
            ),
            23 =>
            array (
                'id' => 24,
                'principle_id' => 4,
            'name' => 'Preventative approach to disease, preferably with natural remedies/approaches; castration or other medical interventions only when necessary (not routine)',
            'description' => 'Preventative approach to disease, preferably with natural remedies/approaches; castration or other medical interventions only when necessary (not routine)',
                'created_at' => '2022-09-13 17:11:46',
                'updated_at' => '2022-09-13 17:11:46',
            ),
            24 =>
            array (
                'id' => 25,
                'principle_id' => 4,
                'name' => 'Ethical killing / slaughter, incl. in fishing',
                'description' => 'Ethical killing / slaughter, incl. in fishing',
                'created_at' => '2022-09-13 17:11:54',
                'updated_at' => '2022-09-13 17:11:54',
            ),
            25 =>
            array (
                'id' => 26,
                'principle_id' => 4,
                'name' => 'No separation of mother from young; no routine slaughter of baby males',
                'description' => 'No separation of mother from young; no routine slaughter of baby males',
                'created_at' => '2022-09-13 17:12:05',
                'updated_at' => '2022-09-13 17:12:05',
            ),
            26 =>
            array (
                'id' => 27,
                'principle_id' => 4,
                'name' => 'Eliminate/ reduce actively/significantly use of synthetic feeds and hormones - increase use of organic feeds',
                'description' => 'Eliminate/ reduce actively/significantly use of synthetic feeds and hormones - increase use of organic feeds',
                'created_at' => '2022-09-13 17:12:10',
                'updated_at' => '2022-09-13 17:12:10',
            ),
            27 =>
            array (
                'id' => 28,
                'principle_id' => 5,
            'name' => 'Use a diversity of crops and varieties including  local, traditional, indigenous or \'orphan\' crops, locally adapted breeds and varieties (animals, trees, crops, fish)',
            'description' => 'Use a diversity of crops and varieties including  local, traditional, indigenous or \'orphan\' crops, locally adapted breeds and varieties (animals, trees, crops, fish)',
                'created_at' => '2022-09-13 17:12:15',
                'updated_at' => '2022-09-13 17:12:15',
            ),
            28 =>
            array (
                'id' => 29,
                'principle_id' => 5,
            'name' => 'Encouraging of particular species (e.g. pollinators, pest predators, wild companion plants) through habitat management',
            'description' => 'Encouraging of particular species (e.g. pollinators, pest predators, wild companion plants) through habitat management',
                'created_at' => '2022-09-13 17:12:22',
                'updated_at' => '2022-09-13 17:12:22',
            ),
            29 =>
            array (
                'id' => 30,
                'principle_id' => 5,
                'name' => 'Conservation of forest fragments around farms, conversion of field edges into woodlands',
                'description' => 'Conservation of forest fragments around farms, conversion of field edges into woodlands',
                'created_at' => '2022-09-13 17:12:28',
                'updated_at' => '2022-09-13 17:12:28',
            ),
            30 =>
            array (
                'id' => 31,
                'principle_id' => 5,
                'name' => 'Multi-year crop rotation',
                'description' => 'Multi-year crop rotation',
                'created_at' => '2022-09-13 17:12:33',
                'updated_at' => '2022-09-13 17:12:33',
            ),
            31 =>
            array (
                'id' => 32,
                'principle_id' => 5,
            'name' => 'Multi-habitat approaches (land use diversity at landscape level)',
            'description' => 'Multi-habitat approaches (land use diversity at landscape level)',
                'created_at' => '2022-09-13 17:12:38',
                'updated_at' => '2022-09-13 17:12:38',
            ),
            32 =>
            array (
                'id' => 33,
                'principle_id' => 5,
                'name' => 'Biological soil fertility/health measures',
                'description' => 'Biological soil fertility/health measures',
                'created_at' => '2022-09-13 17:12:44',
                'updated_at' => '2022-09-13 17:12:44',
            ),
            33 =>
            array (
                'id' => 34,
                'principle_id' => 5,
                'name' => 'Measures to enhance local and natural pollinators',
                'description' => 'Measures to enhance local and natural pollinators',
                'created_at' => '2022-09-13 17:12:51',
                'updated_at' => '2022-09-13 17:12:51',
            ),
            34 =>
            array (
                'id' => 35,
                'principle_id' => 6,
                'name' => 'Agroecological redesign & diversification increasing synergies at farm / fishery level,',
            'description' => 'Agroecological redesign & diversification increasing synergies at farm / fishery level, such as : Companion planting; Non-crop plants for ecological functions; Polycultures and mixed farming, cover cropping, green manures or permanent ground cover; Intercropping, agroforestry, silvopasture; Crop-tree-livestock-fish integration; Legumes for nitrogen fixation; Fodder trees and crops (mangroves for fisheries?); Soil-plants management system',
                'created_at' => '2022-09-13 17:13:07',
                'updated_at' => '2022-09-13 17:13:07',
            ),
            35 =>
            array (
                'id' => 36,
                'principle_id' => 6,
                'name' => 'Connectivity between elements of the agroecosystem and the landscape',
                'description' => 'Connectivity between elements of the agroecosystem and the landscape',
                'created_at' => '2022-09-13 17:13:12',
                'updated_at' => '2022-09-13 17:13:12',
            ),
            36 =>
            array (
                'id' => 37,
                'principle_id' => 6,
                'name' => 'Rotational/regenerative grazing',
                'description' => 'Rotational/regenerative grazing',
                'created_at' => '2022-09-13 17:13:18',
                'updated_at' => '2022-09-13 17:13:18',
            ),
            37 =>
            array (
                'id' => 38,
                'principle_id' => 6,
            'name' => 'Integrated pest management by habitat manipulation (planting flowers to attract bees, push-pull approach...)',
            'description' => 'Integrated pest management by habitat manipulation (planting flowers to attract bees, push-pull approach...)',
                'created_at' => '2022-09-13 17:13:23',
                'updated_at' => '2022-09-13 17:13:23',
            ),
            38 =>
            array (
                'id' => 39,
                'principle_id' => 6,
                'name' => 'Landscape planning and synchronised landscape activity/ territorial approach leading to improved  ecosystem services',
                'description' => 'Landscape planning and synchronised landscape activity/ territorial approach leading to improved  ecosystem services',
                'created_at' => '2022-09-13 17:13:30',
                'updated_at' => '2022-09-13 17:13:30',
            ),
            39 =>
            array (
                'id' => 40,
                'principle_id' => 6,
                'name' => 'Tackling climate change through redesigned system',
                'description' => 'Tackling climate change through redesigned system',
                'created_at' => '2022-09-13 17:13:40',
                'updated_at' => '2022-09-13 17:13:40',
            ),
            40 =>
            array (
                'id' => 41,
                'principle_id' => 7,
                'name' => 'Diversification of production – e.g. honey, wild/foraged foods and herbs, non-timber forest products, native local fish species',
                'description' => 'Diversification of production – e.g. honey, wild/foraged foods and herbs, non-timber forest products, native local fish species',
                'created_at' => '2022-09-13 17:13:46',
                'updated_at' => '2022-09-13 17:13:46',
            ),
            41 =>
            array (
                'id' => 42,
                'principle_id' => 7,
                'name' => 'On-farm storage, agroprocessing / transformation',
                'description' => 'On-farm storage, agroprocessing / transformation',
                'created_at' => '2022-09-13 17:13:53',
                'updated_at' => '2022-09-13 17:13:53',
            ),
            42 =>
            array (
                'id' => 43,
                'principle_id' => 7,
            'name' => 'Farm-based or local input production for distribution (seed, seedlings, trees, biofertilisers, biopesticides)',
            'description' => 'Farm-based or local input production for distribution (seed, seedlings, trees, biofertilisers, biopesticides)',
                'created_at' => '2022-09-13 17:13:59',
                'updated_at' => '2022-09-13 17:13:59',
            ),
            43 =>
            array (
                'id' => 44,
                'principle_id' => 7,
                'name' => 'Support short/regional value chains/circuits, local food system',
                'description' => 'Support short/regional value chains/circuits, local food system',
                'created_at' => '2022-09-13 17:14:14',
                'updated_at' => '2022-09-13 17:14:14',
            ),
            44 =>
            array (
                'id' => 45,
                'principle_id' => 7,
                'name' => 'Small enterprise development and support in agro-food value chains',
                'description' => 'Small enterprise development and support in agro-food value chains',
                'created_at' => '2022-09-13 17:14:21',
                'updated_at' => '2022-09-13 17:14:21',
            ),
            45 =>
            array (
                'id' => 46,
                'principle_id' => 7,
                'name' => 'Support youth and women entrepreneurship',
                'description' => 'Support youth and women entrepreneurship',
                'created_at' => '2022-09-13 17:14:26',
                'updated_at' => '2022-09-13 17:14:26',
            ),
            46 =>
            array (
                'id' => 47,
                'principle_id' => 7,
            'name' => 'Farm-based non-agricultural activities (e.g. crafts, agri-tourism, eco-tourism, services)',
            'description' => 'Farm-based non-agricultural activities (e.g. crafts, agri-tourism, eco-tourism, services)',
                'created_at' => '2022-09-13 17:14:33',
                'updated_at' => '2022-09-13 17:14:33',
            ),
            47 =>
            array (
                'id' => 48,
                'principle_id' => 7,
                'name' => 'Providing ecosystem services',
                'description' => 'Providing ecosystem services',
                'created_at' => '2022-09-13 17:14:39',
                'updated_at' => '2022-09-13 17:14:39',
            ),
            48 =>
            array (
                'id' => 49,
                'principle_id' => 8,
                'name' => 'Platform for the horizontal creation and transfer of knowledge and good practices, such as : Farmer to farmer learning and exchanges including farmer field schools, farmers’ climate field schools; Community of practices on agroecology',
                'description' => 'Platform for the horizontal creation and transfer of knowledge and good practices, such as : Farmer to farmer learning and exchanges including farmer field schools, farmers’ climate field schools; Community of practices on agroecology',
                'created_at' => '2022-09-13 17:14:52',
                'updated_at' => '2022-09-13 17:14:52',
            ),
            49 =>
            array (
                'id' => 50,
                'principle_id' => 8,
                'name' => 'Farmer research and experimentation groups',
                'description' => 'Farmer research and experimentation groups',
                'created_at' => '2022-09-13 17:21:02',
                'updated_at' => '2022-09-13 17:21:02',
            ),
            50 =>
            array (
                'id' => 51,
                'principle_id' => 8,
                'name' => 'Recovery, valorisation and dissemination of traditional and indigenous knowledge',
                'description' => 'Recovery, valorisation and dissemination of traditional and indigenous knowledge',
                'created_at' => '2022-09-13 17:21:10',
                'updated_at' => '2022-09-13 17:21:10',
            ),
            51 =>
            array (
                'id' => 52,
                'principle_id' => 8,
                'name' => 'Co innovation between farmers and researchers / Participatory research',
                'description' => 'Co innovation between farmers and researchers / Participatory research',
                'created_at' => '2022-09-13 17:21:17',
                'updated_at' => '2022-09-13 17:21:17',
            ),
            52 =>
            array (
                'id' => 53,
                'principle_id' => 8,
            'name' => 'Transdisciplinary research (design, implementation, analysis, evaluation).',
            'description' => 'Transdisciplinary research (design, implementation, analysis, evaluation).',
                'created_at' => '2022-09-13 17:21:23',
                'updated_at' => '2022-09-13 17:21:23',
            ),
            53 =>
            array (
                'id' => 54,
                'principle_id' => 8,
                'name' => 'Improve access to agroecological knowledge, notably through: Capacity building / /strengthen agroecological extension; Improvement and development of agroecology curricula',
                'description' => 'Improve access to agroecological knowledge, notably through: Capacity building / /strengthen agroecological extension; Improvement and development of agroecology curricula',
                'created_at' => '2022-09-13 17:21:33',
                'updated_at' => '2022-09-13 17:21:33',
            ),
            54 =>
            array (
                'id' => 55,
                'principle_id' => 8,
                'name' => 'Engagement and participation of the producers in the local community and grassroot organizations',
                'description' => 'Engagement and participation of the producers in the local community and grassroot organizations',
                'created_at' => '2022-09-13 17:21:36',
                'updated_at' => '2022-09-13 17:21:36',
            ),
            55 =>
            array (
                'id' => 56,
                'principle_id' => 9,
                'name' => 'Social values : Cultural identity and tradition',
                'description' => 'Social values : Cultural identity and tradition',
                'created_at' => '2022-09-13 17:21:46',
                'updated_at' => '2022-09-13 17:21:46',
            ),
            56 =>
            array (
                'id' => 57,
                'principle_id' => 9,
                'name' => 'Social values :  Gender equity',
                'description' => 'Social values :  Gender equity',
                'created_at' => '2022-09-13 17:21:51',
                'updated_at' => '2022-09-13 17:21:51',
            ),
            57 =>
            array (
                'id' => 58,
                'principle_id' => 9,
                'name' => 'Social values : Youth empowerment',
                'description' => 'Social values : Youth empowerment',
                'created_at' => '2022-09-13 17:21:57',
                'updated_at' => '2022-09-13 17:21:57',
            ),
            58 =>
            array (
                'id' => 59,
                'principle_id' => 9,
            'name' => 'Social values : Inclusion (IPLC’s, PWD and other marginalised groups)',
            'description' => 'Social values : Inclusion (IPLC’s, PWD and other marginalised groups)',
                'created_at' => '2022-09-13 17:22:03',
                'updated_at' => '2022-09-13 17:22:03',
            ),
            59 =>
            array (
                'id' => 60,
                'principle_id' => 9,
                'name' => 'Social values : Agriculture based on family farmers which have full access to capital and decision making processes',
                'description' => 'Social values : Agriculture based on family farmers which have full access to capital and decision making processes',
                'created_at' => '2022-09-13 17:22:09',
                'updated_at' => '2022-09-13 17:22:09',
            ),
            60 =>
            array (
                'id' => 61,
                'principle_id' => 9,
                'name' => 'Diets : Promotion of diversified locally produced healthy diets through a diversified food production system',
                'description' => 'Diets : Promotion of diversified locally produced healthy diets through a diversified food production system',
                'created_at' => '2022-09-13 17:22:14',
                'updated_at' => '2022-09-13 17:22:14',
            ),
            61 =>
            array (
                'id' => 62,
                'principle_id' => 9,
                'name' => 'Diets : Access to culturally and seasonally appropriate food',
                'description' => 'Diets : Access to culturally and seasonally appropriate food',
                'created_at' => '2022-09-13 17:22:22',
                'updated_at' => '2022-09-13 17:22:22',
            ),
            62 =>
            array (
                'id' => 63,
                'principle_id' => 10,
                'name' => 'Fair trade and fair prices',
                'description' => 'Fair trade and fair prices',
                'created_at' => '2022-09-13 17:22:28',
                'updated_at' => '2022-09-13 17:22:28',
            ),
            63 =>
            array (
                'id' => 64,
                'principle_id' => 10,
                'name' => 'Decent jobs and working conditions for all actors in agri-food system',
                'description' => 'Decent jobs and working conditions for all actors in agri-food system',
                'created_at' => '2022-09-13 17:22:34',
                'updated_at' => '2022-09-13 17:22:34',
            ),
            64 =>
            array (
                'id' => 65,
                'principle_id' => 10,
                'name' => 'Social mechanisms to reduce vulnerability',
                'description' => 'Social mechanisms to reduce vulnerability',
                'created_at' => '2022-09-13 17:22:39',
                'updated_at' => '2022-09-13 17:22:39',
            ),
            65 =>
            array (
                'id' => 66,
                'principle_id' => 10,
                'name' => 'Producers and consumers organisations',
                'description' => 'Producers and consumers organisations',
                'created_at' => '2022-09-13 17:22:48',
                'updated_at' => '2022-09-13 17:22:48',
            ),
            66 =>
            array (
                'id' => 67,
                'principle_id' => 10,
                'name' => 'Dignified livelihoods especially for smallholders',
                'description' => 'Dignified livelihoods especially for smallholders',
                'created_at' => '2022-09-13 17:22:53',
                'updated_at' => '2022-09-13 17:22:53',
            ),
            67 =>
            array (
                'id' => 68,
                'principle_id' => 10,
                'name' => 'Protection and promotion of intellectual property rights, specially with respect to traditional knowledge',
                'description' => 'Protection and promotion of intellectual property rights, specially with respect to traditional knowledge',
                'created_at' => '2022-09-13 17:22:59',
                'updated_at' => '2022-09-13 17:22:59',
            ),
            68 =>
            array (
                'id' => 69,
                'principle_id' => 10,
                'name' => 'Equitable and collective ownership models',
            'description' => 'NOTE: To be confirmed / checked (had ??? in the Excel Sheet)',
                'created_at' => '2022-09-13 17:23:27',
                'updated_at' => '2022-09-13 17:23:27',
            ),
            69 =>
            array (
                'id' => 70,
                'principle_id' => 11,
                'name' => 'Re-establishing connection between consumers and producers emphasising connectivity and trust',
                'description' => 'Re-establishing connection between consumers and producers emphasising connectivity and trust',
                'created_at' => '2022-09-13 17:23:34',
                'updated_at' => '2022-09-13 17:23:34',
            ),
            70 =>
            array (
                'id' => 71,
                'principle_id' => 11,
                'name' => 'Promote highly interconnected, supportive systems, promoting women and youth involvement',
                'description' => 'Promote highly interconnected, supportive systems, promoting women and youth involvement',
                'created_at' => '2022-09-13 17:23:42',
                'updated_at' => '2022-09-13 17:23:42',
            ),
            71 =>
            array (
                'id' => 72,
                'principle_id' => 11,
            'name' => 'Re-establishing and reinforcing the connection between communities and territories (including spiritual and ancestral connections)',
            'description' => 'Re-establishing and reinforcing the connection between communities and territories (including spiritual and ancestral connections)',
                'created_at' => '2022-09-13 17:23:51',
                'updated_at' => '2022-09-13 17:23:51',
            ),
            72 =>
            array (
                'id' => 73,
                'principle_id' => 11,
                'name' => 'Access to markets emphasising short food chains and local food webs',
                'description' => 'Access to markets emphasising short food chains and local food webs',
                'created_at' => '2022-09-13 17:23:58',
                'updated_at' => '2022-09-13 17:23:58',
            ),
            73 =>
            array (
                'id' => 74,
                'principle_id' => 11,
                'name' => 'Encourage and sensitise for seasonal and regional demand',
                'description' => 'Encourage and sensitise for seasonal and regional demand',
                'created_at' => '2022-09-13 17:24:05',
                'updated_at' => '2022-09-13 17:24:05',
            ),
            74 =>
            array (
                'id' => 75,
                'principle_id' => 11,
                'name' => 'Public procurement schemes for agroecological produce especially favouring small holder food producers',
                'description' => 'Public procurement schemes for agroecological produce especially favouring small holder food producers',
                'created_at' => '2022-09-13 17:24:10',
                'updated_at' => '2022-09-13 17:24:10',
            ),
            75 =>
            array (
                'id' => 76,
                'principle_id' => 11,
                'name' => 'Organisation and support of local farmer markets, workers cooperatives, CSAs and/or PGS',
                'description' => 'Organisation and support of local farmer markets, workers cooperatives, CSAs and/or PGS',
                'created_at' => '2022-09-13 17:24:17',
                'updated_at' => '2022-09-13 17:24:17',
            ),
            76 =>
            array (
                'id' => 77,
                'principle_id' => 11,
                'name' => 'Community restaurants, soup kitchens',
                'description' => 'Community restaurants, soup kitchens',
                'created_at' => '2022-09-13 17:24:24',
                'updated_at' => '2022-09-13 17:24:24',
            ),
            77 =>
            array (
                'id' => 78,
                'principle_id' => 12,
                'name' => 'Recognition of smallholder rights & conflict resolution in their support',
                'description' => 'Recognition of smallholder rights & conflict resolution in their support',
                'created_at' => '2022-09-13 17:24:31',
                'updated_at' => '2022-09-13 17:24:31',
            ),
            78 =>
            array (
                'id' => 79,
                'principle_id' => 12,
                'name' => 'Promoting and respecting right to food',
                'description' => 'Promoting and respecting right to food',
                'created_at' => '2022-09-13 17:24:35',
                'updated_at' => '2022-09-13 17:24:35',
            ),
            79 =>
            array (
                'id' => 80,
                'principle_id' => 12,
                'name' => 'Promoting and respecting rights of traditional knowledge protection',
                'description' => 'Promoting and respecting rights of traditional knowledge protection',
                'created_at' => '2022-09-13 17:24:40',
                'updated_at' => '2022-09-13 17:24:40',
            ),
            80 =>
            array (
                'id' => 81,
                'principle_id' => 12,
                'name' => 'Promotion of food sovereignty',
                'description' => 'Promotion of food sovereignty',
                'created_at' => '2022-09-13 17:24:47',
                'updated_at' => '2022-09-13 17:24:47',
            ),
            81 =>
            array (
                'id' => 82,
                'principle_id' => 12,
                'name' => 'Integrated seed systems and governance emphasising farmer managed seed systems; Promoting and respecting right to farm-saved seed',
                'description' => 'Integrated seed systems and governance emphasising farmer managed seed systems; Promoting and respecting right to farm-saved seed',
                'created_at' => '2022-09-13 17:24:54',
                'updated_at' => '2022-09-13 17:24:54',
            ),
            82 =>
            array (
                'id' => 83,
                'principle_id' => 12,
                'name' => 'Land tenure that respects traditional and customary land rights and ensure equitable and secure access to land for smallholders/ family farmers and peasant food producers.',
            'description' => 'Land tenure that respects traditional and customary land rights and ensure equitable and secure access to land for smallholders/ family farmers and peasant food producers. (e.g. social forestry, community-based forest management, protected area management by local communities)',
                'created_at' => '2022-09-13 17:25:10',
                'updated_at' => '2022-09-13 17:25:10',
            ),
            83 =>
            array (
                'id' => 84,
                'principle_id' => 12,
                'name' => 'Control of inland and marine water resources  by coastal/fishing communities; governance of water resources include their representatives',
                'description' => 'Control of inland and marine water resources  by coastal/fishing communities; governance of water resources include their representatives',
                'created_at' => '2022-09-13 17:25:16',
                'updated_at' => '2022-09-13 17:25:16',
            ),
            84 =>
            array (
                'id' => 85,
                'principle_id' => 12,
                'name' => 'Equitable ownership and access to natural resources recognising the crucial role of small holders and IPLCs as stewards of the environment',
                'description' => 'Equitable ownership and access to natural resources recognising the crucial role of small holders and IPLCs as stewards of the environment',
                'created_at' => '2022-09-13 17:25:22',
                'updated_at' => '2022-09-13 17:25:22',
            ),
            85 =>
            array (
                'id' => 86,
                'principle_id' => 12,
                'name' => 'Improving the enabling environment for agroecology, sustainable land use and natural resource management',
            'description' => 'Improving the enabling environment for agroecology, sustainable land use and natural resource management (e.g. Public and private incentives for provision of ecosystem services through agriculture and land management, national land use policies that protect agricultural land from conversion)',
                'created_at' => '2022-09-13 17:25:30',
                'updated_at' => '2022-09-13 17:25:30',
            ),
            86 =>
            array (
                'id' => 87,
                'principle_id' => 13,
                'name' => 'Rights awareness and capacity to claim for rights holders and accountability for duty bearers',
                'description' => 'Rights awareness and capacity to claim for rights holders and accountability for duty bearers',
                'created_at' => '2022-09-13 17:25:41',
                'updated_at' => '2022-09-13 17:25:41',
            ),
            87 =>
            array (
                'id' => 88,
                'principle_id' => 13,
            'name' => 'Participatory food system governance (including policy development, food councils)',
            'description' => 'Participatory food system governance (including policy development, food councils)',
                'created_at' => '2022-09-13 17:25:46',
                'updated_at' => '2022-09-13 17:25:46',
            ),
            88 =>
            array (
                'id' => 89,
                'principle_id' => 13,
                'name' => 'Multi-actor food system processes, communities of practice',
                'description' => 'Multi-actor food system processes, communities of practice',
                'created_at' => '2022-09-13 17:25:52',
                'updated_at' => '2022-09-13 17:25:52',
            ),
            89 =>
            array (
                'id' => 90,
                'principle_id' => 13,
                'name' => 'Citizen’s juries',
                'description' => 'Citizen’s juries',
                'created_at' => '2022-09-13 17:25:58',
                'updated_at' => '2022-09-13 17:25:58',
            ),
            90 =>
            array (
                'id' => 91,
                'principle_id' => 13,
                'name' => 'Devolved decision-making',
                'description' => 'Devolved decision-making',
                'created_at' => '2022-09-13 17:26:03',
                'updated_at' => '2022-09-13 17:26:03',
            ),
            91 =>
            array (
                'id' => 92,
                'principle_id' => 13,
                'name' => 'Community-based natural resource management',
                'description' => 'Community-based natural resource management',
                'created_at' => '2022-09-13 17:26:09',
                'updated_at' => '2022-09-13 17:26:09',
            ),
            92 =>
            array (
                'id' => 93,
                'principle_id' => 13,
                'name' => 'Participatory land use planning, landscape design; Participatory biosphere conservation and restoration, catchment management',
                'description' => 'Participatory land use planning, landscape design; Participatory biosphere conservation and restoration, catchment management',
                'created_at' => '2022-09-13 17:26:16',
                'updated_at' => '2022-09-13 17:26:16',
            ),
            93 =>
            array (
                'id' => 94,
                'principle_id' => 13,
                'name' => 'Inclusive and meaningful participation of women, youth, IPLCs and other marginalised groups in policy and decision',
            'description' => 'Inclusive and meaningful participation of women, youth, IPLCs and other marginalised groups in policy and decision (e.g. making Increased agency of all actors in the food systems, their legitimate and self-selected representatives sit in relevant governance and implementation bodies)',
                'created_at' => '2022-09-13 17:26:26',
                'updated_at' => '2022-09-13 17:26:26',
            ),
            94 =>
            array (
                'id' => 95,
                'principle_id' => 13,
            'name' => 'Strengthened organisational capacity for self-determination and autonomy (e.g. food sovereignty)',
            'description' => 'Strengthened organisational capacity for self-determination and autonomy (e.g. food sovereignty)',
                'created_at' => '2022-09-13 17:26:34',
                'updated_at' => '2022-09-13 17:26:34',
            ),
        ));


    }
}
