import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Layout from './Layouts/Layout';

const appName = import.meta.env.VITE_APP_NAME || 'Jetty';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx', { eager: false })
        );

        // Only apply default layout if page doesn't have its own layout
        if (!page.default.layout) {
            page.default.layout = (page) => <Layout>{page}</Layout>;
        }

        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#6366f1', // Primary indigo to match theme
        showSpinner: true,
    },
});

