<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotRule;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class ChatbotRuleController extends Controller
{
    public function index()
    {
        $rules = ChatbotRule::query()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.chatbot-rules.index', compact('rules'));
    }

    public function create(Request $request)
    {
        $templates = MessageTemplate::query()->orderBy('name')->get();
        $rule = new ChatbotRule([
            'status' => 'active',
            'match_type' => 'contains',
            'response_type' => 'text',
            'applies_to_state' => 'welcome',
            'priority' => 0,
        ]);

        if ($request->query('preset') === 'menu') {
            $rule->name = 'Menu principal';
            $rule->response_type = 'buttons';
            $rule->response_text = 'Ola! Como posso ajudar?';
            $rule->triggers_text = "menu\ninicio\n0";
            $rule->buttons_text = "Catalogo|catalogo\nStatus|status\nAtendente|atendente";
        }

        return view('admin.chatbot-rules.create', compact('templates', 'rule'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRule($request);
        ChatbotRule::create($data);

        return redirect()->route('admin.chatbot-rules.index')
            ->with('status', 'Regra criada com sucesso.');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.chatbot-rules.edit', $id);
    }

    public function edit(string $id)
    {
        $rule = ChatbotRule::query()->findOrFail($id);
        $templates = MessageTemplate::query()->orderBy('name')->get();

        return view('admin.chatbot-rules.edit', compact('rule', 'templates'));
    }

    public function update(Request $request, string $id)
    {
        $rule = ChatbotRule::query()->findOrFail($id);
        $data = $this->validateRule($request);
        $rule->update($data);

        return redirect()->route('admin.chatbot-rules.index')
            ->with('status', 'Regra atualizada com sucesso.');
    }

    public function destroy(string $id)
    {
        $rule = ChatbotRule::query()->findOrFail($id);
        $rule->delete();

        return redirect()->route('admin.chatbot-rules.index')
            ->with('status', 'Regra removida com sucesso.');
    }

    protected function validateRule(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'match_type' => ['required', 'string', 'in:contains,exact,starts_with,regex'],
            'triggers_text' => ['nullable', 'string'],
            'response_type' => ['required', 'string', 'in:text,template,buttons,menu,catalog,status,handoff,ai'],
            'response_text' => ['nullable', 'string'],
            'template_id' => ['nullable', 'integer', 'exists:message_templates,id'],
            'buttons_text' => ['nullable', 'string'],
            'applies_to_state' => ['nullable', 'string', 'in:any,welcome,order_created'],
            'priority' => ['nullable', 'integer'],
        ]);

        if (($data['response_type'] ?? '') !== 'template') {
            $data['template_id'] = null;
        }

        if (($data['response_type'] ?? '') !== 'buttons') {
            $data['buttons_text'] = null;
        }

        if (!in_array($data['response_type'] ?? '', ['text', 'buttons'], true)) {
            $data['response_text'] = null;
        }

        return $data;
    }
}
