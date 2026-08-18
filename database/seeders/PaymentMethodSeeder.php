<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Efectivo', 'description' => 'Pago en efectivo al momento de la transacción.'],
            ['name' => 'Tarjeta de crédito', 'description' => 'Pago mediante tarjeta de crédito.'],
            ['name' => 'Tarjeta de débito', 'description' => 'Pago mediante tarjeta de débito.'],
            ['name' => 'Transferencia bancaria', 'description' => 'Pago mediante transferencia desde una cuenta bancaria.'],
            ['name' => 'PayPal', 'description' => 'Pago a través de la plataforma PayPal.'],
        ];

        DB::table('payment_methods')->upsert($methods, ['name'], ['description']);
    }
}
