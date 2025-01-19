<template>
    <div>
        <h1>Pet Store Sellasist - Konrad Ptak</h1>

        <!-- Licznik zwierząt -->
        <div class="counter" style="text-align: center; font-size: 2em;">
            <p>Łączna liczba zwierząt: <strong>{{ pets.length }}</strong></p>
        </div>

        <!-- Radio buttony do wyboru statusów -->
        <div class="status-filter" style="text-align: center; font-size: 1.5em; margin-top: 20px;">
            <label style="margin: 0 10px;">
                <input
                    type="radio"
                    value="available"
                    v-model="selectedStatus"
                    @change="fetchPets"
                />
                Available
            </label>
            <label style="margin: 0 10px;">
                <input
                    type="radio"
                    value="pending"
                    v-model="selectedStatus"
                    @change="fetchPets"
                />
                Pending
            </label>
            <label style="margin: 0 10px;">
                <input
                    type="radio"
                    value="sold"
                    v-model="selectedStatus"
                    @change="fetchPets"
                />
                Sold
            </label>
        </div>

        <!-- Wyświetlanie błędów -->
        <div v-if="error" class="error">
            Nie udało się pobrać listy zwierząt.
        </div>

        <!-- Lista zwierząt -->
        <div v-else>
            <div v-if="pets.length === 0" class="no-data">
                Brak zwierząt do wyświetlenia.
            </div>
            <div v-else class="pets-list">
                <div
                    v-for="pet in pets"
                    :key="pet.id"
                    class="pet-card"
                >
                    <img
                        :src="pet.photoUrls[0] || 'https://via.placeholder.com/150'"
                        alt="Zdjęcie zwierzęcia"
                        class="pet-photo"
                    />
                    <div class="pet-info">
                        <h2>{{ pet.name }}</h2>
                        <p><strong>ID:</strong> {{ pet.id }}</p>
                        <p><strong>Status:</strong> {{ pet.status }}</p>
                        <button style="background-color: blue; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;" @click="editPet(pet.id)">Edytuj</button>
                        <button style="background-color: red; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;" @click="deletePet(pet.id)">Usuń</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const pets = ref([]);
const error = ref(false);
const selectedStatus = ref('available'); // Domyślnie zaznaczony status "available"

// Funkcja do pobierania zwierząt na podstawie wybranego statusu
const fetchPets = async () => {
    try {
        error.value = false;

        if (!selectedStatus.value) {
            pets.value = [];
            return;
        }

        // Tworzenie parametrów zapytania
        const params = new URLSearchParams();
        params.append('status', selectedStatus.value);

        // Wysyłanie żądania do API
        const response = await axios.get(`/api/pets?${params.toString()}`);
        pets.value = response.data;
    } catch (err) {
        console.error('Błąd podczas pobierania danych:', err);
        error.value = true;
    }
};

// Funkcja do edycji zwierzęcia
const editPet = (id) => {
    console.log(`Edytuj zwierzę o ID: ${id}`);
    // Tutaj dodaj logikę edycji zwierzęcia
};

// Funkcja do usuwania zwierzęcia
const deletePet = async (id) => {
    try {
        console.log(`Usuń zwierzę o ID: ${id}`);
        // Tutaj dodaj logikę usuwania zwierzęcia
        await axios.delete(`/api/pets/${id}`);
        fetchPets(); // Odśwież listę po usunięciu
    } catch (err) {
        console.error('Błąd podczas usuwania zwierzęcia:', err);
    }
};

// Pobranie początkowych danych
onMounted(fetchPets);
</script>
