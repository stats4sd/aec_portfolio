<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class PrinciplesTableSeeder extends Seeder
{

    public function run()
    {


        DB::table('principles')->delete();

        DB::table('principles')->insert([
            0 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 1,
                    'name' => 'Recycling',
                    'description' => 'Preferentially use local renewable resources and close as far as possible resource cycles of nutrients and biomass.',
                    'info_link' => null,
                    'number' => 1,
                    'rating_na' => 'Project cannot address any dimension of recycling',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Relies on natural processes and has mostly closed resource cycles (nutrients, water, biomass, ...) using predominantly local renewable resources, and/or encourages circular economy, especially in waste management ',
                    'rating_zero' => 'Makes no effort to close resource cycles or contribute to circular economy, and introduces non-recyclable materials',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            1 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 2,
                    'name' => 'Input reduction',
                    'description' => 'Reduce or eliminate dependency on purchased inputs.',
                    'info_link' => 'https://www.agroecology-europe.org/wp-content/uploads/2021/10/Factsheet_Input-reduction.pdf',
                    'number' => 2,
                    'rating_na' => 'Project does not address production system',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Increases self-sufficiency on farm, community or territorial levels and eliminates harmful inputs, particularly synthetic fertilizers, pesticides and preventive antibiotics',
                    'rating_zero' => 'Neutral regarding external inputs or increases dependency of producers on external inputs',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            2 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 3,
                    'name' => 'Soil Health',
                    'description' => 'Secure and enhance soil health and functioning for improved plant growth, particularly by managing organic matter and by enhancing soil biological activity',
                    'info_link' => 'https://youtu.be/3K1BKf7eYYk',
                    'number' => 3,
                    'rating_na' => 'Project does not address agricultural production system',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Deliberately and actively preserves and enhances soil health through explicit design for improving soil biological activity and structure and preserving soil erosion',
                    'rating_zero' => 'Does not focus on soil health and may use practices undermining soil health',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            3 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 4,
                    'name' => 'Animal Health',
                    'description' => 'Ensure animal health and welfare',
                    'info_link' => 'https://youtu.be/tz0GutJc6CI',
                    'number' => 4,
                    'rating_na' => 'Project does not involve animals',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Ensures highest standard of animal health and welfare, during entire life cycle with a focus on species-appropriate environment and locally adapted and resilient breeds. ',
                    'rating_zero' => 'Neutral regarding animal health and welfare or meets required animal health and welfare standards in intensive production',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            4 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 5,
                    'name' => 'Biodiversity',
                    'description' => 'Maintain and enhance diversity of species, functional diversity and genetic resources and maintain biodiversity in the agroecosystem over time and space at field, farm and landscape scales.',
                    'info_link' => 'https://youtu.be/Li-ftIaTdJc',
                    'number' => 5,
                    'rating_na' => 'Project does not address production system',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Deliberately and actively protects and enhances biological diversity within production systems – from domesticated diversity (crops and animal races, ...) and ‘wild’ diversity (soil microorganisms, plants, insects, birds, fish, ...) to multi-habitat approaches (land use diversity at landscape level)',
                    'rating_zero' => 'Neutral with respect to biodiversity or actively manages production system to limit diversity (e.g. monocultures for ease of mechanical harvesting)',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            5 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 6,
                    'name' => 'Synergy',
                    'description' => 'Enhance positive ecological interaction, synergy, integration, and complementarity amongst the elements of agroecosystems (plants, animals, trees, soil, water).',
                    'info_link' => 'https://www.agroecology-europe.org/wp-content/uploads/2021/10/factsheet_synergy2.pdf',
                    'number' => 6,
                    'rating_na' => 'Project does not work on biophysical aspects of landscape',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Enhances positive ecological interaction, integration and complementarity among the elements of agroecosystems (animals, crops, trees, soil and water), as well as between production and environmental objectives across field, farm and landscape scales (e.g. land sharing).',
                    'rating_zero' => 'Neutral with respect to integrating or segregating components within production systems or actively segregates components within production systems, including intensification of production on higher potential land, leaving other land for meeting conservation objectives (land sparing)',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            6 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 7,
                    'name' => 'Economic diversification ',
                    'description' => 'Diversify on-farm incomes by ensuring small-scale farmers have greater financial independence and value addition opportunities while enabling them to respond to demand from consumers.',
                    'info_link' => 'https://youtu.be/5UP2PC63se8',
                    'number' => 7,
                    'rating_na' => 'Project does not address livelihoods',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Actively strives for greater economic diversity of production systems, including to diversify livelihoods and enable financial independence and autonomy',
                    'rating_zero' => 'Neutral with respect to diversification or actively emphasises specialization in production systems ',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            7 =>
                [
                    'can_be_na' => 0,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 8,
                    'name' => 'Co-creation of knowledge',
                    'description' => 'Enhance co-creation and horizontal sharing of knowledge including local and scientific innovation, especially through farmer-to-farmer exchange.',
                    'info_link' => null,
                    'number' => 8,
                    'rating_na' => 'This principle should always apply',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Actively supports and emphasizes the importance of indigenous/traditional knowledge, local innovation, farmer-to-farmer knowledge exchange, and other horizontal knowledge exchanges within the food system',
                    'rating_zero' => 'Does not promote co-creation of knowledge and emphasizes dissemination of innovation from state and privately-funded formal research',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            8 =>
                [
                    'can_be_na' => 0,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 9,
                    'name' => 'Social values & diets',
                    'description' => 'Build food systems based on the culture, identity, tradition, social and gender equity of local communities that provide healthy, diversified, seasonally and culturally appropriate diets.',
                    'info_link' => 'https://www.agroecology-europe.org/wp-content/uploads/2021/10/Month-of-Agroecology-Article-on-Social-Values-Les-Levidow-.pdf',
                    'number' => 9,
                    'rating_na' => 'This principle should always apply',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Build food systems based on equity and the cultural identity and tradition of local communities that provide healthy, diversified, culturally appropriate diets.',
                    'rating_zero' => 'Does not address social inequalities and disregards cultural identities and values related to food and diets',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            9 =>
                [
                    'can_be_na' => 0,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 10,
                    'name' => 'Fairness',
                    'description' => 'Support dignified and robust livelihoods for all actors engaged in food systems, especially small-scale food producers, based on fair trade, fair employment and fair treatment of intellectual property rights.',
                    'info_link' => null,
                    'number' => 10,
                    'rating_na' => 'This principle should always apply',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Emphasizes fairness as well as decent work, and actively supports dignified and robust livelihoods for all actors engaged in food systems, especially small-scale food producers',
                    'rating_zero' => 'Neutral to or disregarding labour conditions as well as injustices in trade and legal arrangements ',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            10 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 11,
                    'name' => 'Connectivity',
                    'description' => 'Ensure proximity and confidence between producers and consumers through promotion of fair and short distribution networks and by re-embedding food systems into local economies.',
                    'info_link' => null,
                    'number' => 11,
                    'rating_na' => 'Project does not address commercialisation and exchange of produce',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Emphasizes proximity and relationships between producers, consumers and other food system actors through promotion of fair and short distribution networks, circular economy, workers’ cooperatives and solidarity networks.',
                    'rating_zero' => 'Project does not promote connectivity between food system actors and/or emphasizes global value chains',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            11 =>
                [
                    'can_be_na' => 1,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 12,
                    'name' => 'Land & Natural resources governance',
                    'description' => 'Recognize and support the needs and interests of family farmers, smallholders and peasant food producers as sustainable managers and guardians of natural and genetic resources.',
                    'info_link' => null,
                    'number' => 12,
                    'rating_na' => 'Land and natural resource governance and institutional arrangements fall outside of the scope of the project',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Asserts basic rights (especially the right to food and water, land rights) and strengthens institutional arrangements to support agroecological production and smallholder food producers as sustainable managers of natural and genetic resources',
                    'rating_zero' => 'Neutral to rights-based approaches and/or ignores role of local communities in natural resource management ',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
            12 =>
                [
                    'can_be_na' => 0,
                    'created_at' => '2022-09-13 17:07:35',
                    'id' => 13,
                    'name' => 'Participation',
                    'description' => 'Encourage social organization and greater participation in decision-making by food producers and consumers to support decentralized governance and local adaptive management of agricultural and food systems.',
                    'info_link' => null,
                    'number' => 13,
                    'rating_na' => 'This principle should always apply',
                    'rating_one' => 'Partially',
                    'rating_two' => 'Places smallholder food producers at the centre of decision-making, encourages decentralized governance, strengthens organisational capacity for self-determination and autonomy and actively strives for greater food actor agency – i.e. participation of all food actors and wider civil society in decision-making about how food is produced, processed, stored, transported and consumed. ',
                    'rating_zero' => 'Does not actively encourage inclusive participation and/or centralises decision-making',
                    'updated_at' => '2022-09-13 17:07:35',
                ],
        ]);


    }
}
