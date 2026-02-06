import React from 'react';
import { TrendingUp, Users, ShoppingCart, Wallet } from 'lucide-react';

const metrics = [
    { label: 'Vendas do mês', value: 'R$ 128.4k', change: '+18%', icon: TrendingUp },
    { label: 'Clientes ativos', value: '4.382', change: '+9%', icon: Users },
    { label: 'Pedidos confirmados', value: '1.284', change: '+12%', icon: ShoppingCart },
    { label: 'Pagamentos Pix', value: '86%', change: '+4%', icon: Wallet },
];

const DashboardPreview = () => {
    return (
        <section id="dashboard" className="mx-auto w-full max-w-6xl px-4 py-16">
            <div className="grid gap-10 lg:grid-cols-[1fr_1.1fr] lg:items-center">
                <div className="space-y-4">
                    <p className="text-sm font-semibold uppercase tracking-widest text-emerald-500">Dashboard</p>
                    <h2 className="text-3xl font-black text-slate-900 dark:text-white sm:text-4xl">
                        Visual moderno para decisões rápidas
                    </h2>
                    <p className="text-base text-slate-600 dark:text-slate-300">
                        Indicadores essenciais em um layout claro, pronto para operações multi-canal. Tudo pensado para
                        performance e ação.
                    </p>
                    <ul className="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                        <li>• Insights de conversão e ticket médio</li>
                        <li>• Status de pedidos e pagamentos Pix</li>
                        <li>• Monitoramento em tempo real</li>
                    </ul>
                </div>

                <div className="rounded-[32px] border border-slate-200 bg-white p-6 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-xs uppercase tracking-widest text-slate-400">Resumo</p>
                            <h3 className="text-lg font-semibold text-slate-900 dark:text-white">Performance</h3>
                        </div>
                        <div className="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-500">
                            Últimos 30 dias
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        {metrics.map((metric) => (
                            <div
                                key={metric.label}
                                className="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm shadow-soft dark:border-slate-800 dark:bg-slate-950"
                            >
                                <div className="flex items-center justify-between">
                                    <p className="text-slate-500 dark:text-slate-400">{metric.label}</p>
                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                                        <metric.icon className="h-4 w-4" />
                                    </div>
                                </div>
                                <div className="mt-2 flex items-end justify-between">
                                    <span className="text-lg font-semibold text-slate-900 dark:text-white">{metric.value}</span>
                                    <span className="text-xs font-semibold text-emerald-500">{metric.change}</span>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="mt-6 rounded-3xl border border-slate-200 bg-gradient-to-br from-emerald-500/10 via-transparent to-emerald-500/20 p-5 dark:border-slate-800">
                        <div className="flex items-center justify-between text-xs uppercase tracking-widest text-slate-400">
                            <span>Receita diária</span>
                            <span>+22%</span>
                        </div>
                        <div className="mt-4 flex h-24 items-end gap-2">
                            {[30, 55, 40, 70, 45, 80, 60].map((height, index) => (
                                <div
                                    key={index}
                                    className="flex-1 rounded-2xl bg-emerald-500/70 shadow-soft"
                                    style={{ height: `${height}%` }}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default DashboardPreview;
