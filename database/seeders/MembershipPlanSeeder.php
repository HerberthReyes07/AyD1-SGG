<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            // Plan Básico
            [
                'name' => 'Basic - Monthly',
                'description' => 'Acceso a las instalaciones y equipos del gimnasio durante el horario general, registro de entrada/salida y acceso al módulo de nutrición.',
                'price' => 150.00,
                'duration_months' => 1,
                'includes_group_classes' => false,
                'weekly_class_limit' => null,
                'includes_trainer' => false,
                'has_waitlist_priority' => false,
            ],
            [
                'name' => 'Basic - Quarterly',
                'description' => 'Acceso a las instalaciones y equipos del gimnasio durante el horario general, registro de entrada/salida y acceso al módulo de nutrición.',
                'price' => 405.00, // 10% descuento vs. 3 meses individuales
                'duration_months' => 3,
                'includes_group_classes' => false,
                'weekly_class_limit' => null,
                'includes_trainer' => false,
                'has_waitlist_priority' => false,
            ],
            [
                'name' => 'Basic - Annual',
                'description' => 'Acceso a las instalaciones y equipos del gimnasio durante el horario general, registro de entrada/salida y acceso al módulo de nutrición.',
                'price' => 1440.00, // 20% descuento vs. 12 meses individuales
                'duration_months' => 12,
                'includes_group_classes' => false,
                'weekly_class_limit' => null,
                'includes_trainer' => false,
                'has_waitlist_priority' => false,
            ],

            // Plan Premium
            [
                'name' => 'Premium - Monthly',
                'description' => 'Todos los beneficios del plan Básico, además de inscripción en hasta 3 clases grupales por semana (según disponibilidad) y acceso al historial y valoraciones de las clases.',
                'price' => 250.00,
                'duration_months' => 1,
                'includes_group_classes' => true,
                'weekly_class_limit' => 3,
                'includes_trainer' => false,
                'has_waitlist_priority' => false,
            ],
            [
                'name' => 'Premium - Quarterly',
                'description' => 'Todos los beneficios del plan Básico, además de inscripción en hasta 3 clases grupales por semana (según disponibilidad) y acceso al historial y valoraciones de las clases.',
                'price' => 675.00,
                'duration_months' => 3,
                'includes_group_classes' => true,
                'weekly_class_limit' => 3,
                'includes_trainer' => false,
                'has_waitlist_priority' => false,
            ],
            [
                'name' => 'Premium - Annual',
                'description' => 'Todos los beneficios del plan Básico, además de inscripción en hasta 3 clases grupales por semana (según disponibilidad) y acceso al historial y valoraciones de las clases.',
                'price' => 2400.00,
                'duration_months' => 12,
                'includes_group_classes' => true,
                'weekly_class_limit' => 3,
                'includes_trainer' => false,
                'has_waitlist_priority' => false,
            ],

            // Plan Élite
            [
                'name' => 'Elite - Monthly',
                'description' => 'Todos los beneficios del plan Premium, con clases grupales ilimitadas (según disponibilidad), entrenador personal para rutinas y seguimiento del progreso, y prioridad en la lista de espera.',
                'price' => 400.00,
                'duration_months' => 1,
                'includes_group_classes' => true,
                'weekly_class_limit' => null, // null = ilimitado, diferenciado de Básico por includes_group_classes = true
                'includes_trainer' => true,
                'has_waitlist_priority' => true,
            ],
            [
                'name' => 'Elite - Quarterly',
                'description' => 'Todos los beneficios del plan Premium, con clases grupales ilimitadas (según disponibilidad), entrenador personal para rutinas y seguimiento del progreso, y prioridad en la lista de espera.',
                'price' => 1080.00,
                'duration_months' => 3,
                'includes_group_classes' => true,
                'weekly_class_limit' => null,
                'includes_trainer' => true,
                'has_waitlist_priority' => true,
            ],
            [
                'name' => 'Elite - Annual',
                'description' => 'Todos los beneficios del plan Premium, con clases grupales ilimitadas (según disponibilidad), entrenador personal para rutinas y seguimiento del progreso, y prioridad en la lista de espera.',
                'price' => 3840.00,
                'duration_months' => 12,
                'includes_group_classes' => true,
                'weekly_class_limit' => null,
                'includes_trainer' => true,
                'has_waitlist_priority' => true,
            ],
        ];

        DB::table('membership_plans')->upsert(
            $plans,
            ['name'],
            ['description', 'price', 'duration_months', 'includes_group_classes', 'weekly_class_limit', 'includes_trainer', 'has_waitlist_priority']
        );
    }
}
