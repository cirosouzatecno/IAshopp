import React, { useEffect, useState } from 'react';
import Navbar from './components/Navbar';
import Hero from './components/Hero';
import Features from './components/Features';
import DashboardPreview from './components/DashboardPreview';
import Integrations from './components/Integrations';
import CTA from './components/CTA';
import Footer from './components/Footer';

const THEME_KEY = 'iashopp-theme';

const App = () => {
    const [theme, setTheme] = useState('light');

    useEffect(() => {
        // Persisted theme preference (fallback to system).
        const stored = localStorage.getItem(THEME_KEY);
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const initial = stored || (systemPrefersDark ? 'dark' : 'light');
        setTheme(initial);
        document.documentElement.classList.toggle('dark', initial === 'dark');
    }, []);

    const toggleTheme = () => {
        const next = theme === 'dark' ? 'light' : 'dark';
        setTheme(next);
        document.documentElement.classList.toggle('dark', next === 'dark');
        localStorage.setItem(THEME_KEY, next);
    };

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
            <Navbar theme={theme} onToggleTheme={toggleTheme} />

            <main className="relative overflow-hidden pt-24">
                <div className="absolute inset-0 bg-glow pointer-events-none" />
                <Hero />
                <Features />
                <DashboardPreview />
                <Integrations />
                <CTA />
            </main>

            <Footer />
        </div>
    );
};

export default App;
