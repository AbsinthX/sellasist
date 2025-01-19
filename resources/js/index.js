import './bootstrap';
import { createApp } from 'vue';
import App from './Index.vue';
import '../css/styles.css';

const index = createApp(App);
index.mount('#index');
