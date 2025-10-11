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
                'keywords' => ['hola', 'buenos dias', 'buenas tardes', 'saludos'],
                'response_template' => '¡Hola{{if name}} {{name}}{{endif}}! 👋 Son las {{time}}. ¿En qué puedo ayudarte hoy?',
                'priority' => 10,
                'fuzzy_match' => true,
                'trigger_type' => 'contains',
            ],
            [
                'keywords' => ['menu', 'carta', 'productos', 'oferta'],
                'response_template' => '📋 Aquí está nuestro menú completo: [enlace del menú]',
                'priority' => 8,
                'fuzzy_match' => true,
                'trigger_type' => 'contains',
            ],
            [
                'keywords' => ['precio', 'costo', 'cuanto', 'valor'],
                'response_template' => '💰 Nuestros precios van desde $15.000{{if name}}, {{name}}{{endif}}. ¿Te interesa algún producto específico?',
                'priority' => 7,
                'fuzzy_match' => true,
                'trigger_type' => 'contains',
            ],
            [
                'keywords' => ['horario', 'hora', 'abierto', 'cerrado'],
                'response_template' => '🕒 Nuestro horario: {{business_hours}}. Hoy es {{day}} y son las {{time}}.',
                'priority' => 6,
                'fuzzy_match' => true,
                'trigger_type' => 'contains',
            ],
            [
                'keywords' => ['ubicacion', 'direccion', 'donde', 'como llegar'],
                'response_template' => '📍 Estamos ubicados en [dirección]. ¿Necesitas indicaciones?',
                'priority' => 5,
                'fuzzy_match' => true,
                'trigger_type' => 'contains',
            ],
            [
                'keywords' => ['emergencia', 'urgente', 'ayuda urgente'],
                'response_template' => '🚨 Entendemos que es urgente{{if name}}, {{name}}{{endif}}. {{if is_business_open}}Te atendemos ahora{{else}}Estamos cerrados pero puedes escribir al WhatsApp de emergencias: +57300123456{{endif}}',
                'priority' => 15,
                'fuzzy_match' => true,
                'trigger_type' => 'contains',
            ],
        ];

        foreach ($rules as $ruleData) {
            KeywordRuleModel::create([
                'id' => Str::uuid(),
                'keywords' => $ruleData['keywords'],
                'response_template' => $ruleData['response_template'],
                'is_active' => true,
                'priority' => $ruleData['priority'],
                'fuzzy_match' => $ruleData['fuzzy_match'],
                'trigger_type' => $ruleData['trigger_type'],
            ]);
        }
    }
}
