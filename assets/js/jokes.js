/**
 * Classe JokeGenerator pour récupérer et afficher des blagues depuis l'API JokeAPI
 */
class JokeGenerator {
    /**
     * Constructeur de la classe
     */
    constructor() {
        // Éléments du DOM
        this.jokeContent = document.getElementById('joke-content');
        this.jokeLoader = document.getElementById('joke-loader');
        this.categorySelect = document.getElementById('joke-category');
        this.getJokeButton = document.getElementById('get-joke');
        this.languageOptions = document.getElementsByName('language');

        // URL de base de l'API
        this.apiBaseUrl = 'https://v2.jokeapi.dev/joke/';

        // Initialisation
        this.init();
    }

    /**
     * Initialise le générateur de blagues
     */
    init() {
        // Ajouter un écouteur d'événement au bouton
        this.getJokeButton.addEventListener('click', () => this.fetchJoke());
    }

    /**
     * Récupère la valeur de la langue sélectionnée
     * @returns {string} La langue sélectionnée
     */
    getSelectedLanguage() {
        for (const radio of this.languageOptions) {
            if (radio.checked) {
                return radio.value;
            }
        }
        return 'en'; // Par défaut en anglais
    }

    /**
     * Affiche le loader pendant le chargement
     */
    showLoader() {
        this.jokeContent.classList.add('hidden');
        this.jokeLoader.classList.remove('hidden');
    }

    /**
     * Cache le loader après le chargement
     */
    hideLoader() {
        this.jokeContent.classList.remove('hidden');
        this.jokeLoader.classList.add('hidden');
    }

    /**
     * Récupère une blague depuis l'API
     */
    // Modification de la méthode fetchJoke dans jokes.js
    fetchJoke() {
        // Afficher le loader
        this.showLoader();

        // Récupérer la catégorie et la langue sélectionnées
        const category = this.categorySelect.value;
        const language = this.getSelectedLanguage();

        // Construire l'URL de l'API - simplifions au maximum
        const apiUrl = `https://v2.jokeapi.dev/joke/Any?lang=${language}`;

        console.log("Fetching joke from:", apiUrl); // Debug

        // Faire la requête à l'API de la manière la plus simple
        fetch(apiUrl)
            .then(response => {
                console.log("Response status:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("API response:", data); // Debug
                // Cacher le loader
                this.hideLoader();

                // Afficher la blague
                this.displayJoke(data);
            })
            .catch(error => {
                console.error("Error fetching joke:", error); // Debug
                // Cacher le loader
                this.hideLoader();

                // Afficher l'erreur de manière plus détaillée
                this.jokeContent.innerHTML = `
            <div class="joke-error">
                <p>Une erreur est survenue lors de la récupération de la blague.</p>
                <p class="error-details">Détails: ${error.toString()}</p>
            </div>
        `;
            });
    }

    /**
     * Affiche la blague
     * @param {Object} jokeData Les données de la blague
     */
    displayJoke(jokeData) {
        if (jokeData.error) {
            // Afficher l'erreur retournée par l'API
            this.jokeContent.innerHTML = `
                <div class="joke-error">
                    <p>Une erreur est survenue: ${jokeData.message}</p>
                </div>
            `;
            return;
        }

        // Déterminer le type de blague (simple ou à deux parties)
        if (jokeData.type === 'single') {
            // Blague simple
            this.jokeContent.innerHTML = `
                <div class="joke-single">
                    <p>${jokeData.joke}</p>
                </div>
            `;
        } else if (jokeData.type === 'twopart') {
            // Blague à deux parties (question-réponse)
            this.jokeContent.innerHTML = `
                <div class="joke-twopart">
                    <p class="joke-setup">${jokeData.setup}</p>
                    <p class="joke-delivery">${jokeData.delivery}</p>
                </div>
            `;
        }

        // Ajouter des informations supplémentaires
        this.jokeContent.innerHTML += `
            <div class="joke-meta">
                <p>Catégorie: ${jokeData.category}</p>
            </div>
        `;
    }
}

// Initialiser le générateur de blagues lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    const jokeGenerator = new JokeGenerator();
});