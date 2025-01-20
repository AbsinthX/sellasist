<template>
    <div>
        <h1>Pet Store Sellasist - Konrad Ptak</h1>

        <!-- Counter -->
        <div class="counter" style="text-align: center; font-size: 2em;">
            <p>Animals: <strong>{{ pets.length }}</strong></p>
            <button
                class="add-pet-button"
                @click="goToAddPet"
            >
                Add animal
            </button>
        </div>

        <!-- Statuses -->
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

        <!-- Errors -->
        <div v-if="error" class="error">
            Failed to fetch the list of animals.
        </div>

        <!-- Animals -->
        <div v-else class="pets-container">
            <div v-if="pets.length === 0" class="no-data">
                No animals to display.
            </div>
            <div v-else class="pets-list">
                <div
                    v-for="pet in pets"
                    :key="pet.id"
                    class="pet-card"
                >
                    <img
                        :src="pet.photoUrls[0] || 'https://via.placeholder.com/150'"
                        alt="Image of the pet"
                        class="pet-photo"
                    />
                    <div class="pet-info">
                        <h2>{{ pet.name }}</h2>
                        <p><strong>ID:</strong> {{ pet.id }}</p>
                        <p><strong>Status:</strong> {{ pet.status }}</p>
                        <button
                            style="background-color: blue; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;"
                            @click="goToEditPet(pet.id)"
                        >
                            Edit
                        </button>
                        <button
                            style="background-color: red; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;"
                            @click="confirmDeletePet(pet.id)"
                        >
                            Delete
                        </button>
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
const selectedStatus = ref('available');

const fetchPets = async () => {
    try {
        error.value = false;

        if (!selectedStatus.value) {
            pets.value = [];
            return;
        }

        const params = new URLSearchParams();
        params.append('status', selectedStatus.value);

        const response = await axios.get(`/api/pets?${params.toString()}`);
        console.log("Raw API data:", response.data);

        pets.value = response.data
            .filter((pet, index, self) =>
                index === self.findIndex((p) => p.id === pet.id)
            )
            .filter((pet) => pet.status === selectedStatus.value);
    } catch (err) {
        console.error('Error while fetching data:', err);
        error.value = true;
    }
};

const goToEditPet = (id) => {
    window.location.href = `/pet/${id}`;
};

const confirmDeletePet = async (id) => {
    if (confirm('Are you sure you want to delete this animal?')) {
        await deletePet(id);
    }
};

const deletePet = async (id) => {
    try {
        await axios.delete(`/api/pets/${id}`);
        fetchPets();
    } catch (err) {
        console.error('Error while deleting the animal:', err);
        alert('There was a problem while deleting the animal. Please try again.');
    }
};

const goToAddPet = () => {
    window.location.href = '/pet';
};

onMounted(fetchPets);
</script>
