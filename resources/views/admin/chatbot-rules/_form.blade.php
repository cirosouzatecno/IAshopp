@php
    $responseType = old('response_type', $rule->response_type ?? 'text');
@endphp

<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Nome" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $rule->name) }}" required />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300">
                @foreach (['active' => 'Ativo', 'inactive' => 'Inativo'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $rule->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="priority" value="Prioridade" />
            <x-text-input id="priority" name="priority" type="number" class="mt-1 block w-full" value="{{ old('priority', $rule->priority ?? 0) }}" />
        </div>
        <div>
            <x-input-label for="applies_to_state" value="Aplicar no Estado" />
            <select id="applies_to_state" name="applies_to_state" class="mt-1 block w-full rounded-md border-gray-300">
                @foreach (['any' => 'Qualquer', 'welcome' => 'Boas-vindas', 'order_created' => 'Pedido criado'] as $value => $label)
                    <option value="{{ $value }}" {{ old('applies_to_state', $rule->applies_to_state ?? 'welcome') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <x-input-label for="match_type" value="Tipo de Match" />
        <select id="match_type" name="match_type" class="mt-1 block w-full rounded-md border-gray-300">
            @foreach (['contains' => 'Contem', 'exact' => 'Exato', 'starts_with' => 'Comeca com', 'regex' => 'Regex'] as $value => $label)
                <option value="{{ $value }}" {{ old('match_type', $rule->match_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <x-input-label for="triggers_text" value="Triggers (1 por linha)" />
        <textarea id="triggers_text" name="triggers_text" class="mt-1 block w-full rounded-md border-gray-300" rows="4" placeholder="ex: oi&#10;ola&#10;bom dia">{{ old('triggers_text', $rule->triggers_text) }}</textarea>
        <div class="text-xs text-gray-500 dark:text-slate-300 mt-1">Use textos simples. Para regex, insira o padrao direto (ex: ^pedido\\s+\\d+).</div>
    </div>

    <div>
        <x-input-label for="response_type" value="Tipo de Resposta" />
        <select id="response_type" name="response_type" class="mt-1 block w-full rounded-md border-gray-300">
            @foreach (['text' => 'Texto', 'template' => 'Template', 'buttons' => 'Botoes', 'menu' => 'Menu', 'catalog' => 'Catalogo', 'status' => 'Status pedido', 'handoff' => 'Handoff', 'ai' => 'IA'] as $value => $label)
                <option value="{{ $value }}" {{ old('response_type', $responseType) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div data-response="text,buttons" class="{{ in_array($responseType, ['text', 'buttons'], true) ? '' : 'hidden' }}">
        <x-input-label for="response_text" value="Mensagem" />
        <textarea id="response_text" name="response_text" class="mt-1 block w-full rounded-md border-gray-300" rows="4">{{ old('response_text', $rule->response_text) }}</textarea>
    </div>

    <div data-response="template" class="{{ $responseType === 'template' ? '' : 'hidden' }}">
        <x-input-label for="template_id" value="Template" />
        <select id="template_id" name="template_id" class="mt-1 block w-full rounded-md border-gray-300">
            <option value="">Selecione...</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" {{ (int) old('template_id', $rule->template_id) === $template->id ? 'selected' : '' }}>
                    {{ $template->name }} ({{ $template->language }})
                </option>
            @endforeach
        </select>
    </div>

    <div data-response="buttons" class="{{ $responseType === 'buttons' ? '' : 'hidden' }}">
        <x-input-label value="Botoes" />
        <input type="hidden" id="buttons_text" name="buttons_text" value="{{ old('buttons_text', $rule->buttons_text) }}" />
        <div id="buttons-builder" class="mt-2 space-y-2"></div>
        <div class="flex items-center gap-3 mt-3">
            <button type="button" id="add-button-row" class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-700">Adicionar botao</button>
            <span class="text-xs text-gray-500 dark:text-slate-300">Maximo de 3 botoes.</span>
        </div>
        <div class="mt-3 text-xs text-gray-500 dark:text-slate-300">
            Os IDs sao usados para reconhecer cliques. Se vazio, sera gerado automaticamente.
        </div>
        <div class="mt-3 text-xs text-gray-400" id="buttons-preview"></div>
    </div>
</div>

<script>
    (function () {
        const responseType = document.getElementById('response_type');
        if (!responseType) return;

        function toggleFields() {
            const value = responseType.value;
            document.querySelectorAll('[data-response]').forEach((el) => {
                const allowed = el.getAttribute('data-response').split(',').map((v) => v.trim());
                el.classList.toggle('hidden', !allowed.includes(value));
            });
        }

        responseType.addEventListener('change', toggleFields);
        toggleFields();
    })();

    (function () {
        const hidden = document.getElementById('buttons_text');
        const builder = document.getElementById('buttons-builder');
        const addButton = document.getElementById('add-button-row');
        const preview = document.getElementById('buttons-preview');
        const maxButtons = 3;

        if (!hidden || !builder || !addButton) return;

        function parseHidden() {
            const lines = hidden.value.split(/\r?\n/).map((line) => line.trim()).filter(Boolean);
            return lines.map((line) => {
                const parts = line.split('|');
                return {
                    title: (parts[0] || '').trim(),
                    id: (parts[1] || '').trim(),
                };
            });
        }

        let rows = parseHidden();
        if (!rows.length) {
            rows = [{ title: '', id: '' }];
        }

        function syncHidden() {
            const lines = rows
                .filter((row) => row.title)
                .map((row) => row.id ? `${row.title}|${row.id}` : row.title);
            hidden.value = lines.join("\n");
            renderPreview(lines);
        }

        function renderPreview(lines) {
            if (!preview) return;
            if (!lines.length) {
                preview.textContent = '';
                return;
            }
            preview.textContent = 'Preview: ' + lines.join(' | ');
        }

        function render() {
            builder.innerHTML = '';
            rows.forEach((row, index) => {
                const wrap = document.createElement('div');
                wrap.className = 'flex flex-col md:flex-row md:items-center gap-2';
                wrap.dataset.index = String(index);

                const title = document.createElement('input');
                title.type = 'text';
                title.value = row.title;
                title.placeholder = 'Titulo do botao';
                title.className = 'w-full rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100';
                title.dataset.field = 'title';

                const id = document.createElement('input');
                id.type = 'text';
                id.value = row.id;
                id.placeholder = 'ID (opcional)';
                id.className = 'w-full rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100';
                id.dataset.field = 'id';

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = 'Remover';
                remove.className = 'text-red-600 text-xs md:ml-2';
                remove.dataset.action = 'remove';

                wrap.appendChild(title);
                wrap.appendChild(id);
                wrap.appendChild(remove);
                builder.appendChild(wrap);
            });

            addButton.disabled = rows.length >= maxButtons;
            addButton.classList.toggle('opacity-50', addButton.disabled);
        }

        builder.addEventListener('input', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement)) return;
            const row = target.closest('[data-index]');
            if (!row) return;
            const index = parseInt(row.dataset.index || '0', 10);
            const field = target.dataset.field;
            if (!rows[index]) return;
            if (field === 'title' || field === 'id') {
                rows[index][field] = target.value;
                syncHidden();
            }
        });

        builder.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            if (target.dataset.action !== 'remove') return;
            const row = target.closest('[data-index]');
            if (!row) return;
            const index = parseInt(row.dataset.index || '0', 10);
            rows.splice(index, 1);
            if (!rows.length) {
                rows.push({ title: '', id: '' });
            }
            render();
            syncHidden();
        });

        addButton.addEventListener('click', () => {
            if (rows.length >= maxButtons) return;
            rows.push({ title: '', id: '' });
            render();
            syncHidden();
        });

        render();
        syncHidden();
    })();
</script>
