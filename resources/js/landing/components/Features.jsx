import React from 'react';
import { Bot, ShoppingBag, Sparkles, ShieldCheck, QrCode, BarChart3 } from 'lucide-react';

const features = [
    {
        title: 'Atendimento IA 24/7',
        description: 'Fluxos inteligentes que respondem dúvidas, confirmam pedidos e elevam a conversão.',
        icon: Bot,
    },
    {
        title: 'Catálogo vivo',
        description: 'Produtos e estoque sempre atualizados para evitar vendas perdidas.',
        icon: ShoppingBag,
    },
    {
        title: 'QR Pix instantâneo',
        description: 'Envie QR Code estático e confirme pagamentos com controle total.',
        icon: QrCode,
    },
    {
        title: 'Métricas em tempo real',
        description: 'Acompanhe vendas, tickets e taxas de conversão com clareza.',
        icon: BarChart3,
    },
    {
        title: 'Integração segura',
        description: 'Camadas de autenticação e auditoria para proteger sua operação.',
        icon: ShieldCheck,
    },
    {
        title: 'Automação modular',
        description: 'Arquitetura SaaS pronta para evoluir com novos fluxos e canais.',
        icon: Sparkles,
    },
];

const Features = () => {
    return (
        <section id="features" className="mx-auto w-full max-w-6xl px-4 py-16">
            <div className="flex flex-col gap-4 pb-10">
                <p className="text-sm font-semibold uppercase tracking-widest text-emerald-500">Recursos</p>
                <h2 className="text-3xl font-black text-slate-900 dark:text-white sm:text-4xl">
                    Tudo o que sua operação precisa para vender mais pelo WhatsApp
                </h2>
                <p className="max-w-2xl text-base text-slate-600 dark:text-slate-300">
                    Uma plataforma SaaS feita para negócios digitais. Minimalista, rápida e focada em resultados.
                </p>
            </div>

            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {features.map((feature) => (
                    <div
                        key={feature.title}
                        className="group rounded-[32px] border border-slate-200 bg-white p-6 shadow-soft transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-glow dark:border-slate-800 dark:bg-slate-900"
                    >
                        <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-3xl bg-emerald-500/10 text-emerald-500 transition group-hover:scale-105">
                            <feature.icon className="h-5 w-5" />
                        </div>
                        <h3 className="text-lg font-semibold text-slate-900 dark:text-white">{feature.title}</h3>
                        <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">{feature.description}</p>
                    </div>
                ))}
            </div>
        </section>
    );
};

export default Features;
