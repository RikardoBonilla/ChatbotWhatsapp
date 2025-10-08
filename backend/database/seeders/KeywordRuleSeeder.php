<?php

namespace Database\Seeders;

use App\Infrastructure\WhatsApp\Models\KeywordRuleModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KeywordRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'keyword' => 'hola',
                'response_template' => '¡Hola! 👋 ¿En qué puedo ayudarte hoy?',
                'priority' => 10,
            ],
            [
                'keyword' => 'menu',
                'response_template' => '📋 Aquí está nuestro menú completo: [enlace del menú]',
                'priority' => 8,
            ],
            [
                'keyword' => 'precio',
                'response_template' => '💰 Nuestros precios van desde $15.000. ¿Te interesa algún producto específico?',
                'priority' => 7,
            ],
            [
                'keyword' => 'horario',
                'response_template' => '🕒 Nuestro horario de atención es de Lunes a Viernes 9am - 6pm',
                'priority' => 6,
            ],
            [
                'keyword' => 'ubicacion',
                'response_template' => '📍 Estamos ubicados en [dirección]. ¿Necesitas indicaciones?',
                'priority' => 5,
            ],
        ];

        foreach ($rules as $ruleData) {
            KeywordRuleModel::create([
                'id' => Str::uuid(),
                'keyword' => $ruleData['keyword'],
                'response_template' => $ruleData['response_template'],
                'is_active' => true,
                'priority' => $ruleData['priority'],
            ]);
        }
    }
}
