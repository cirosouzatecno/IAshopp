<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('admin.chatbot.index') }}" class="inline-flex items-center rounded-full px-4 py-2 text-xs font-semibold {{ request()->routeIs('admin.chatbot.index') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
        Visao geral
    </a>
    <a href="{{ route('admin.chatbot-rules.index') }}" class="inline-flex items-center rounded-full px-4 py-2 text-xs font-semibold {{ request()->routeIs('admin.chatbot-rules.*') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
        Regras
    </a>
    <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center rounded-full px-4 py-2 text-xs font-semibold {{ request()->routeIs('admin.templates.*') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
        Templates
    </a>
    <a href="{{ route('admin.settings.edit') }}" class="inline-flex items-center rounded-full px-4 py-2 text-xs font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
        IA e Config
    </a>
    <a href="{{ route('admin.whatsapp-web.index') }}" class="inline-flex items-center rounded-full px-4 py-2 text-xs font-semibold {{ request()->routeIs('admin.whatsapp-web.*') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
        WhatsApp Web
    </a>
</div>
