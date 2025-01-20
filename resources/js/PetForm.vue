<template>
    <div class="add-pet-form">
        <h1>{{ isEditing ? 'Edit animal' : 'Add animal' }}</h1>

        <form @submit.prevent="submitForm">
            <div>
                <label for="name">Name:</label>
                <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    placeholder="Enter the name of the animal"
                    required
                />
            </div>

            <div>
                <label for="category">Category:</label>
                <select id="category" v-model="form.category.name" required>
                    <option v-for="category in categories" :key="category" :value="category">
                        {{ category }}
                    </option>
                </select>
            </div>

            <div>
                <label for="photoUrls">Image URL:</label>
                <div class="url-input-wrapper">
                    <input
                        type="url"
                        id="photoUrls"
                        v-model="photoUrlInput"
                        placeholder="Add image URL"
                    />
                    <button type="button" class="blue-button" @click="addPhotoUrl">Add URL</button>
                </div>
            </div>
            <ul>
                <li v-for="(url, index) in form.photoUrls" :key="index" class="list-item">
                    <span>{{ url }}</span>
                    <button type="button" class="red-button" @click="removePhotoUrl(index)">Delete</button>
                </li>
            </ul>

            <div>
                <label for="tags">Tags:</label>
                <div class="tag-input-wrapper">
                    <input
                        type="text"
                        id="tags"
                        v-model="tagInput"
                        placeholder="Add tag"
                    />
                    <button type="button" class="blue-button" @click="addTag">Add tag</button>
                </div>
            </div>
            <ul>
                <li v-for="(tag, index) in form.tags" :key="index" class="list-item">
                    <span>{{ tag.name }}</span>
                    <button type="button" class="red-button" @click="removeTag(index)">Delete</button>
                </li>
            </ul>

            <div>
                <label for="status">Status:</label>
                <select id="status" v-model="form.status" required>
                    <option value="available">Available</option>
                    <option value="pending">Pending</option>
                    <option value="sold">Sold</option>
                </select>
            </div>

            <button type="submit" class="submit-button">{{ isEditing ? 'Save' : 'Add animal' }}</button>
        </form>

        <div class="navigation-buttons">
            <button class="blue-button" @click="goToMainPage">Back</button>
        </div>

        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
        <p v-if="successMessage" class="success">{{ successMessage }}</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "PetForm",
    props: {
        id: {
            type: [String, Number],
            default: null,
        },
    },
    data() {
        return {
            form: {
                name: "",
                category: {
                    name: "",
                },
                photoUrls: [],
                tags: [],
                status: "available",
            },
            categories: ["Dog", "Cat", "Bird"],
            photoUrlInput: "",
            tagInput: "",
            errorMessage: "",
            successMessage: "",
            isEditing: false,
        };
    },
    methods: {
        async fetchPet() {
            if (this.id) {
                try {
                    const response = await axios.get(`/api/pets/${this.id}`);
                    this.form = response.data;
                    this.isEditing = true;
                } catch (error) {
                    this.errorMessage = "`Failed to load animal data.`";
                }
            }
        },
        async submitForm() {
            try {
                this.errorMessage = "";
                this.successMessage = "";

                const payload = {
                    ...this.form,
                    tags: this.form.tags.map((tag) => ({
                        name: tag.name,
                    })),
                };

                if (this.isEditing) {
                    await axios.put(`/api/pets/${this.id}`, payload);
                    this.successMessage = `The animal "${this.form.name}" has been updated.`;
                } else {
                    await axios.post("/api/pets", payload);
                    this.successMessage = `The animal "${this.form.name}" has been added successfully.`;
                }
            } catch (error) {
                console.error("Error response:", error.response);

                if (error.response?.status === 422) {
                    const details = error.response.data.details || {};
                    const validationErrors = Object.entries(details)
                        .map(([field, messages]) => `${field}: ${messages.join(", ")}`)
                        .join("\n");
                    this.errorMessage = `Validation failed:\n${validationErrors}`;
                } else {
                    this.errorMessage =
                        error.response?.data?.message || "An error occurred during processing.";
                }
            }
        },
        addPhotoUrl() {
            if (this.photoUrlInput) {
                this.form.photoUrls.push(this.photoUrlInput);
                this.photoUrlInput = "";
            }
        },
        removePhotoUrl(index) {
            this.form.photoUrls.splice(index, 1);
        },
        addTag() {
            if (this.tagInput) {
                this.form.tags.push({ name: this.tagInput });
                this.tagInput = "";
            }
        },
        removeTag(index) {
            this.form.tags.splice(index, 1);
        },
        goToMainPage() {
            window.location.href = "/";
        },
    },
    mounted() {
        if (this.id) {
            this.fetchPet();
        }
    },
};
</script>
