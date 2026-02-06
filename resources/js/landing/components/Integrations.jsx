import React from 'react';
import { Cpu, QrCode, MessageSquare, Zap } from 'lucide-react';

const Integrations = () => {
    return (
        <section id="integrations" className="mx-auto w-full max-w-6xl px-4 py-16">
            <div className="rounded-[32px] border border-slate-200 bg-white p-8 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <div className="grid gap-8 lg:grid-cols-[1.1fr_1fr] lg:items-center">
                    <div className="space-y-5">
                        <p className="text-sm font-semibold uppercase tracking-widest text-emerald-500">WhatsApp + IA</p>
                        <h2 className="text-3xl font-black text-slate-900 dark:text-white sm:text-4xl">
                            Integração inteligente com QR Code e automações
                        </h2>
                        <p className="text-base text-slate-600 dark:text-slate-300">
                            Conecte seu número via QR, automatize respostas e tenha total controle do catálogo, pagamentos e
                            status de pedidos em uma experiência unificada.
                        </p>
                        <div className="flex flex-wrap gap-3">
                            <span className="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-xs font-semibold text-emerald-500">
                                QR Code dinâmico
                            </span>
                            <span className="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                                Fluxos inteligentes
                            </span>
                            <span className="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                                Pix automatizado
                            </span>
                        </div>
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2">
                        {[
                            {
                                icon: QrCode,
                                title: 'Conexão QR',
                                text: 'Pareamento rápido e monitorado.',
                            },
                            {
                                icon: MessageSquare,
                                title: 'Mensagens IA',
                                text: 'Respostas inteligentes e humanas.',
                            },
                            {
                                icon: Cpu,
                                title: 'Automação SaaS',
                                text: 'Orquestrações com dados ao vivo.',
                            },
                            {
                                icon: Zap,
                                title: 'Tempo real',
                                text: 'Atualizações instantâneas.',
                            },
                        ].map((item) => (
                            <div
                                key={item.title}
                                className="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm shadow-soft transition hover:-translate-y-1 hover:border-emerald-300 dark:border-slate-800 dark:bg-slate-950"
                            >
                                <div className="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                                    <item.icon className="h-4 w-4" />
                                </div>
                                <div className="font-semibold text-slate-900 dark:text-white">{item.title}</div>
                                <div className="text-xs text-slate-600 dark:text-slate-300">{item.text}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default Integrations;
