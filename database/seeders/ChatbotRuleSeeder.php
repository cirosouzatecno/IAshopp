<?php

namespace Database\Seeders;

use App\Models\ChatbotRule;
use Illuminate\Database\Seeder;

class ChatbotRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Saudacao',
                'status' => 'active',
                'match_type' => 'contains',
                'triggers_text' => "oi\nola\nbom dia\nboa tarde\nboa noite\nmenu\ninicio",
                'response_type' => 'menu',
                'applies_to_state' => 'welcome',
                'priority' => 10,
            ],
            [
                'name' => 'Catalogo',
                'status' => 'active',
                'match_type' => 'contains',
                'triggers_text' => "catalogo\ncatalog",
                'response_type' => 'catalog',
                'applies_to_state' => 'welcome',
                'priority' => 9,
            ],
            [
                'name' => 'Status pedido',
                'status' => 'active',
                'match_type' => 'contains',
                'triggers_text' => "status\npedido",
                'response_type' => 'status',
                'applies_to_state' => 'welcome',
                'priority' => 8,
            ],
            [
                'name' => 'Handoff',
                'status' => 'active',
                'match_type' => 'contains',
                'triggers_text' => "atendente\nhumano\nfalar com atendente",
                'response_type' => 'handoff',
                'applies_to_state' => 'welcome',
                'priority' => 7,
            ],
        ];

        foreach ($rules as $rule) {
            ChatbotRule::query()->firstOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }
    }
}
