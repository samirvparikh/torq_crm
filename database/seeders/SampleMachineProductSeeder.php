<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SampleMachineProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'filling-machines'],
            ['name' => 'Filling Machines', 'is_active' => true, 'sort_order' => 1]
        );

        Product::query()->updateOrCreate(
            ['sku' => 'FILL-4H-SERVO'],
            [
                'category_id' => $category->id,
                'name' => 'Automatic 4 Head Servo Filling Machine',
                'description' => 'The Electronic PLC Based Filling Machine is compact and highly efficient machine with elegant look. This multi functional multi featured machine meets the GMP requirements of filling for glass, plastic or Aluminum bottles. The flow of liquid is measured and converted into electronic signals being controlled by micro computer base circuitry. Minimum adjustment required to set different capacities from 500ml to 5 Ltr with varying containers.',
                'capacity' => '100 ML TO 5000 ML',
                'operation' => 'Nozzle goes upwards slowly from the bottom level of bottle towards neck during filling to minimize foaming. Adjustable nozzle is reciprocating according to filling dose. The filling synchronized with conveyor drive and conveyor drive controlled by AC frequency drive.',
                'technical_specifications' => [
                    'Filling Range' => '100 ML TO 5000 ML',
                    'Output/Min*' => '30 TO 40 BPM @ 500 ML',
                    'Direction of Movement' => 'Left to Right',
                    'Number Of Head' => '4 Nos.',
                    'Electric Supply' => '230 V / 50HZ',
                    'Conveyor Motor' => '1 HP / 415 V / 50 HZ',
                    'Servo' => 'Delta Make 400 W',
                    'PLC' => 'Delta Make',
                    'HMI' => 'Delta color display',
                    'Dimensions' => '2400 L x 1300 W x 1750 H',
                    'Net Weight' => '500 Kgs. Approx.',
                ],
                'input_specifications' => [
                    'Note' => 'Depends on Container size, fill size, Neck diameter of container, and nature of liquid etc.',
                ],
                'salient_features' => [
                    'No Bottle No Filling System',
                    'All Contact Parts made of S.S.316',
                    '4 Filling Stations, space saving design',
                    'Easy to clean',
                    'Pneumatically controlled nozzles, with No Container No Fill arrangement',
                    'Drip free nozzle arrangement',
                    'Reciprocating filling nozzle with self-centering device',
                    'Pneumatically operated bottle stopper',
                ],
                'utility_requirements' => [
                    'Electrical supply' => '1 Phase + Neutral + Earthing',
                    'Electrical load' => '2 KW',
                    'Air Pressure' => '6 bar pressure 10 CFM',
                ],
                'unit' => 'Pcs',
                'price' => 465000,
                'tax_rate' => 18,
                'hsn_code' => '8422',
                'is_active' => true,
            ]
        );

        Product::query()->updateOrCreate(
            ['sku' => 'CAP-1H-SCREW'],
            [
                'category_id' => $category->id,
                'name' => 'Automatic Single Head Screw Capping Machine',
                'description' => 'Automatic Screw cap sealing machine is versatile, self-supported on stainless steel leg with height adjustable adjustment system. The machine is precision built on sturdy welded steel frame completely enclose in stainless steel sheet and doors are provided to facilitate the servicing of machine.',
                'capacity' => 'Up to 2 Ltr',
                'operation' => 'The feed container moving on conveyor belt are fed into star wheel, bringing the container below the sealing head in the subsequent indexing part, meanwhile the bottle pick up a cap from the delivery chute of cap feeding bowl.',
                'technical_specifications' => [
                    'Output/Hour*' => '1000 to 3000 bottles',
                    'Direction of Movement' => 'Left to Right',
                    'No. of Sealing Head' => '1 Nos.',
                    'Main Motor' => '1 HP / 415 Volts / 50 Hz',
                    'Machine Dimensions' => '2000 L x 1100 W x 1700 H',
                    'Net Weight' => '350 Kgs. Approx.',
                ],
                'salient_features' => [
                    'No container No cap arrangement',
                    'SS elegantly matt finished body',
                    'Low noise level, low power consumptions',
                    'Adjustable bottle height gauge for easy and quick setting',
                ],
                'utility_requirements' => [
                    'Electrical supply' => '1 Phase + Neutral + Earthing',
                    'Electrical load' => '1.5 KW',
                ],
                'unit' => 'Pcs',
                'price' => 220000,
                'tax_rate' => 18,
                'is_active' => true,
            ]
        );

        $this->command?->info('Sample machine products seeded.');
    }
}
