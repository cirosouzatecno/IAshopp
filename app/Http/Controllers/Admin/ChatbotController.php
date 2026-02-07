<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotRule;
use App\Models\MessageTemplate;
use App\Models\Setting;

class ChatbotController extends Controller
{
    public function index()
    {
        $rulesCount = ChatbotRule::query()->count();
        $templatesCount = MessageTemplate::query()->count();
        $aiEnabled = Setting::getValue('ai_enabled', '0') === '1';
        $menuRule = ChatbotRule::query()->where('name', 'Menu principal')->first();

        $latestRules = ChatbotRule::query()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin.chatbot.index', compact('rulesCount', 'templatesCount', 'aiEnabled', 'latestRules', 'menuRule'));
    }
}
