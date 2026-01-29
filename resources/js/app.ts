import { createApp } from 'vue';
import '../css/app.css';
import AppShell from './components/AppShell.vue';
import { initializeTheme } from './composables/useAppearance';

const app = createApp(AppShell);

app.mount('#app');

// This will set light / dark mode on page load...
initializeTheme();
