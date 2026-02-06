import React from 'react';
import { ArrowRight } from 'lucide-react';

const CTA = () => {
    return (
        <section className="mx-auto w-full max-w-6xl px-4 pb-20 pt-10">
            <div className="rounded-[32px] border border-emerald-500/30 bg-gradient-to-br from-emerald-500/15 via-white to-emerald-500/5 p-10 shadow-soft dark:from-emerald-500/10 dark:via-slate-900 dark:to-emerald-500/5">
                <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div className="space-y-3">
                        <h3 className="text-3xl font-black text-slate-900 dark:text-white">
                            Pronto para automatizar suas vendas?
                        </h3>
                        <p className="text-base text-slate-600 dark:text-slate-300">
                            Conecte seu WhatsApp e deixe a IAshopp converter atendimentos em pedidos.
                        </p>
                    </div>
                    <a
                        href="/register"
                        className="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-glow transition hover:-translate-y-0.5 hover:bg-emerald-400"
                    >
                        Criar minha conta
                        <ArrowRight className="h-4 w-4" />
                    </a>
                </div>
            </div>
        </section>
    );
};

export default CTA;
