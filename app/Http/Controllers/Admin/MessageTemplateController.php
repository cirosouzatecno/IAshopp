<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = MessageTemplate::query()->orderByDesc('id')->paginate(20);

        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:20'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        MessageTemplate::create($data);

        return redirect()->route('admin.templates.index')
            ->with('status', 'Template criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.templates.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $template = MessageTemplate::query()->findOrFail($id);

        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $template = MessageTemplate::query()->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:20'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $template->update($data);

        return redirect()->route('admin.templates.index')
            ->with('status', 'Template atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $template = MessageTemplate::query()->findOrFail($id);
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('status', 'Template removido com sucesso.');
    }
}
