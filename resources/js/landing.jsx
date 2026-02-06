import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './landing/App';

const rootEl = document.getElementById('app');

if (rootEl) {
    createRoot(rootEl).render(<App />);
}
