import React from 'react';
import { Bot, Moon, Sun } from 'lucide-react';

const Navbar = ({ theme, onToggleTheme }) => {
    return (
        <header className="fixed top-0 inset-x-0 z-50">
            <nav className="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4">
                <div className="flex items-center gap-3 rounded-full bg-white/70 px-4 py-2 shadow-soft ring-1 ring-slate-200/60 backdrop-blur-xl dark:bg-slate-900/70 dark:ring-slate-800/60">
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                        <Bot className="h-5 w-5" />
                    </div>
                    <div className="leading-tight">
                        <div className="text-sm font-semibold">IAshopp</div>
                        <div className="text-xs text-slate-500 dark:text-slate-400">SaaS de vendas inteligentes</div>
                    </div>
                </div>

                <div className="hidden items-center gap-6 rounded-full bg-white/70 px-6 py-3 text-sm font-medium shadow-soft ring-1 ring-slate-200/60 backdrop-blur-xl dark:bg-slate-900/70 dark:ring-slate-800/60 md:flex">
                    <a className="text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white" href="#features">
                        Recursos
                    </a>
                    <a className="text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white" href="#dashboard">
                        Dashboard
                    </a>
                    <a className="text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white" href="#integrations">
                        Integrações
                    </a>
                </div>

                <div className="flex items-center gap-3">
                    <button
                        type="button"
                        onClick={onToggleTheme}
                        className="flex h-11 w-11 items-center justify-center rounded-full bg-white/80 text-slate-700 shadow-soft ring-1 ring-slate-200/60 transition hover:-translate-y-0.5 hover:shadow-glow dark:bg-slate-900/70 dark:text-slate-200 dark:ring-slate-800/60"
                        aria-label="Alternar tema"
                    >
                        {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
                    </button>
                    <a
                        href="/login"
                        className="hidden rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-glow transition hover:-translate-y-0.5 hover:bg-emerald-400 md:inline-flex"
                    >
                        Entrar
                    </a>
                </div>
            </nav>
        </header>
    );
};

export default Navbar;
