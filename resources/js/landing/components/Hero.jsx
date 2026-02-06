import React from 'react';
import { Sparkles, QrCode, MessageCircle } from 'lucide-react';

const Hero = () => {
    return (
        <section className="relative mx-auto w-full max-w-6xl px-4 pb-20 pt-10">
            <div className="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between">
                <div className="max-w-2xl space-y-6">
                    <div className="inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-emerald-500">
                        <Sparkles className="h-4 w-4" />
                        Automação inteligente com WhatsApp
                    </div>

                    <h1 className="text-4xl font-black leading-tight text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        Transforme seu WhatsApp em uma{' '}
                        <span className="bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-300 bg-clip-text text-transparent">
                            máquina de vendas
                        </span>{' '}
                        com IA.
                    </h1>

                    <p className="text-base text-slate-600 dark:text-slate-300 sm:text-lg">
                        IAshopp organiza catálogo, pedidos e pagamentos Pix em um fluxo automatizado, com visual SaaS moderno e
                        experiência mobile-first.
                    </p>

                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a
                            href="/register"
                            className="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-glow transition hover:-translate-y-0.5 hover:bg-emerald-400"
                        >
                            Começar agora
                        </a>
                        <a
                            href="#dashboard"
                            className="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-soft transition hover:-translate-y-0.5 hover:border-emerald-300 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200"
                        >
                            Ver dashboard
                        </a>
                    </div>

                    <div className="grid grid-cols-1 gap-3 pt-2 sm:grid-cols-3">
                        {[
                            { icon: QrCode, label: 'Conexão por QR' },
                            { icon: MessageCircle, label: 'Fluxos inteligentes' },
                            { icon: Sparkles, label: 'IA sempre ativa' },
                        ].map((item) => (
                            <div
                                key={item.label}
                                className="flex items-center gap-3 rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-soft dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200"
                            >
                                <div className="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                                    <item.icon className="h-4 w-4" />
                                </div>
                                {item.label}
                            </div>
                        ))}
                    </div>
                </div>

                <div className="relative flex-1">
                    <div className="rounded-[32px] border border-emerald-500/20 bg-gradient-to-br from-white/80 via-white to-emerald-50 p-6 shadow-soft dark:border-emerald-500/20 dark:from-slate-900/80 dark:via-slate-900 dark:to-emerald-900/20">
                        <div className="flex items-center justify-between text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">
                            <span>IAshopp Automation</span>
                            <span className="text-emerald-500">Live</span>
                        </div>
                        <div className="mt-6 space-y-4">
                            <div className="rounded-3xl bg-slate-900 px-5 py-4 text-sm text-white shadow-soft dark:bg-slate-950">
                                Olá! Posso te ajudar a escolher um produto hoje?
                            </div>
                            <div className="ml-auto w-5/6 rounded-3xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-600 dark:text-emerald-300">
                                Quero ver o catálogo e pagar via Pix.
                            </div>
                            <div className="rounded-3xl bg-slate-900 px-5 py-4 text-sm text-white shadow-soft dark:bg-slate-950">
                                Perfeito! Já separei o link e o QR Code para você.
                            </div>
                        </div>
                    </div>
                    <div className="absolute -bottom-8 -left-8 hidden rounded-3xl border border-slate-200 bg-white p-4 text-xs text-slate-600 shadow-soft dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 md:block">
                        <div className="font-semibold text-slate-900 dark:text-white">Taxa de conversão</div>
                        <div className="text-emerald-500">+34% em 30 dias</div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default Hero;
