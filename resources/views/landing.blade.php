<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>IAshopp — Automação Inteligente no WhatsApp</title>

        @vite(['resources/css/app.css', 'resources/js/landing.jsx'])

        <script>
            (function () {
                try {
                    var theme = localStorage.getItem('iashopp-theme');
                    if (!theme) {
                        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    }
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {
                    // ignore
                }
            })();
        </script>
    </head>
    <body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        <div id="app"></div>
    </body>
</html>
