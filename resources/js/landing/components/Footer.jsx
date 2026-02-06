import React from 'react';

const Footer = () => {
    return (
        <footer className="border-t border-slate-200 bg-white/70 py-10 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-950/70 dark:text-slate-400">
            <div className="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 md:flex-row md:items-center md:justify-between">
                <div className="font-semibold text-slate-700 dark:text-slate-200">IAshopp</div>
                <div className="flex flex-wrap gap-6">
                    <a className="hover:text-slate-900 dark:hover:text-white" href="#features">
                        Recursos
                    </a>
                    <a className="hover:text-slate-900 dark:hover:text-white" href="#dashboard">
                        Dashboard
                    </a>
                    <a className="hover:text-slate-900 dark:hover:text-white" href="#integrations">
                        Integrações
                    </a>
                </div>
                <div>© 2026 IAshopp. Todos os direitos reservados.</div>
            </div>
        </footer>
    );
};

export default Footer;
