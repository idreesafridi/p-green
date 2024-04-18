<?php

namespace Database\Seeders;

use App\Models\MaterialOption;
use App\Models\MaterialType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cappotto
        $m1 = new MaterialOption();
        $m1->name = 'Cappotto';
        $m1->save();

        $m_type1 = new MaterialType();
        $m_type1->material_option_id = $m1->id;
        $m_type1->name = 'Cappotto verticale';
        $m_type1->save();

        $m_type2 = new MaterialType();
        $m_type2->material_option_id = $m1->id;
        $m_type2->name = 'Cappotto orizzontale';
        $m_type2->save();

        // Termico
        $m2 = new MaterialOption();
        $m2->name = 'Termico';
        $m2->save();

        $m_type3 = new MaterialType();
        $m_type3->material_option_id = $m2->id;
        $m_type3->name = 'Pompa di Calore o Caldaia';
        $m_type3->save();

        $m_type4 = new MaterialType();
        $m_type4->material_option_id = $m2->id;
        $m_type4->name = 'Water split';
        $m_type4->save();

        $m_type5 = new MaterialType();
        $m_type5->material_option_id = $m2->id;
        $m_type5->name = 'Fan coil';
        $m_type5->save();

        $m_type6 = new MaterialType();
        $m_type6->material_option_id = $m2->id;
        $m_type6->name = 'Alta prevalenza';
        $m_type6->save();

        $m_type7 = new MaterialType();
        $m_type7->material_option_id = $m2->id;
        $m_type7->name = 'Bassa prevalenza';
        $m_type7->save();

        $m_type8 = new MaterialType();
        $m_type8->material_option_id = $m2->id;
        $m_type8->name = 'Termostati';
        $m_type8->save();

        $m_type9 = new MaterialType();
        $m_type9->material_option_id = $m2->id;
        $m_type9->name = 'Solare termico';
        $m_type9->save();

        $m_type10 = new MaterialType();
        $m_type10->material_option_id = $m2->id;
        $m_type10->name = 'Impianto a pavimento';
        $m_type10->save();

        $m_type11 = new MaterialType();
        $m_type11->material_option_id = $m2->id;
        $m_type11->name = 'Scalda Acqua';
        $m_type11->save();

        // Serramenti
        $m3 = new MaterialOption();
        $m3->name = 'Infissi';
        $m3->save();

        $m_type11 = new MaterialType();
        $m_type11->material_option_id = $m3->id;
        $m_type11->name = 'Infissi';
        $m_type11->save();

        $m_type12 = new MaterialType();
        $m_type12->material_option_id = $m3->id;
        $m_type12->name = 'Oscuranti';
        $m_type12->save();

        // Fotovoltaico
        $m4 = new MaterialOption();
        $m4->name = 'Fotovoltaico';
        $m4->save();

        $m_type13 = new MaterialType();
        $m_type13->material_option_id = $m4->id;
        $m_type13->name = 'Pannelli Fotovoltaici';
        $m_type13->save();

        $m_type14 = new MaterialType();
        $m_type14->material_option_id = $m4->id;
        $m_type14->name = 'Batterie';
        $m_type14->save();

        $m_type15 = new MaterialType();
        $m_type15->material_option_id = $m4->id;
        $m_type15->name = 'Inverter';
        $m_type15->save();

        $m_type16 = new MaterialType();
        $m_type16->material_option_id = $m4->id;
        $m_type16->name = 'Colonnina';
        $m_type16->save();

        $m_type17 = new MaterialType();
        $m_type17->material_option_id = $m4->id;
        $m_type17->name = 'Termosifoni';
        $m_type17->save();

        $m_type18 = new MaterialType();
        $m_type18->material_option_id = $m4->id;
        $m_type18->name = 'Pompa di calore o Caldaia';
        $m_type18->save();

        $m_type19 = new MaterialType();
        $m_type19->material_option_id = $m4->id;
        $m_type19->name = 'Solare termico';
        $m_type19->save();

        $m_type20 = new MaterialType();
        $m_type20->material_option_id = $m4->id;
        $m_type20->name = 'Solare termic';
        $m_type20->save();
    }
}
