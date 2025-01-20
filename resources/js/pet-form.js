import './bootstrap';
import { createApp } from 'vue';
import App from './PetForm.vue';
import '../css/styles.css';

const element = document.getElementById('pet-form');
const petId = element?.dataset.id || null;


const petForm = createApp(App, {
    id: petId,
});

petForm.mount('#pet-form');
