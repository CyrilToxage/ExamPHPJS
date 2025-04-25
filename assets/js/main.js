// Classe pour gérer le menu responsive (menu hamburger)
class ResponsiveMenu {
    constructor() {
        this.header = document.querySelector('header');
        this.nav = document.querySelector('nav');
        this.menuBtn = null;
        
        // Initialisation seulement si le header et la nav existent
        if (this.header && this.nav) {
            this.init();
        }
    }
    
    init() {
        // Créer le bouton hamburger
        this.createMenuButton();
        
        // Vérifier la taille de l'écran et appliquer le menu approprié
        this.checkScreenSize();
        
        // Ajouter un écouteur d'événement pour redimensionner
        window.addEventListener('resize', () => {
            this.checkScreenSize();
        });
    }
    
    createMenuButton() {
        // Créer le bouton du menu
        this.menuBtn = document.createElement('button');
        this.menuBtn.className = 'menu-toggle';
        this.menuBtn.innerHTML = '☰';
        this.menuBtn.setAttribute('aria-label', 'Toggle Menu');
        
        // Ajouter le bouton au header
        this.header.insertBefore(this.menuBtn, this.nav);
        
        // Ajouter l'écouteur d'événement au bouton
        this.menuBtn.addEventListener('click', () => {
            this.toggleMenu();
        });
    }
    
    checkScreenSize() {
        // Vérifier si l'écran est petit (mobile)
        if (window.innerWidth <= 768) {
            this.nav.classList.add('mobile-nav');
            this.nav.classList.add('hidden');
            this.menuBtn.style.display = 'block';
        } else {
            this.nav.classList.remove('mobile-nav');
            this.nav.classList.remove('hidden');
            this.menuBtn.style.display = 'none';
        }
    }
    
    toggleMenu() {
        // Basculer l'affichage du menu mobile
        this.nav.classList.toggle('hidden');
    }
}

// Classe pour gérer la validation du formulaire
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        this.form.addEventListener('submit', (e) => {
            // Empêcher l'envoi du formulaire si la validation échoue
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });
    }
    
    validateForm() {
        let isValid = true;
        
        // Récupérer tous les champs requis
        const requiredFields = this.form.querySelectorAll('[required]');
        
        // Vérifier chaque champ requis
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showError(field, 'Ce champ est obligatoire.');
                isValid = false;
            } else {
                this.removeError(field);
                
                // Validation supplémentaire pour le champ email
                if (field.type === 'email') {
                    if (!this.validateEmail(field.value)) {
                        this.showError(field, 'Veuillez entrer une adresse email valide.');
                        isValid = false;
                    }
                }
                
                // Validation de la longueur min/max pour les champs texte
                if (field.minLength && field.value.length < field.minLength) {
                    this.showError(field, `Ce champ doit contenir au moins ${field.minLength} caractères.`);
                    isValid = false;
                }
                
                if (field.maxLength && field.value.length > field.maxLength) {
                    this.showError(field, `Ce champ ne peut pas dépasser ${field.maxLength} caractères.`);
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
    
    validateEmail(email) {
        // Expression régulière pour valider un email
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    showError(field, message) {
        // Supprimer d'abord toute erreur existante
        this.removeError(field);
        
        // Créer un élément d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        // Ajouter le message d'erreur après le champ
        field.parentNode.appendChild(errorDiv);
        
        // Ajouter une classe d'erreur au champ
        field.classList.add('error');
    }
    
    removeError(field) {
        // Supprimer le message d'erreur s'il existe
        const parent = field.parentNode;
        const errorDiv = parent.querySelector('.error-message');
        
        if (errorDiv) {
            parent.removeChild(errorDiv);
        }
        
        // Supprimer la classe d'erreur du champ
        field.classList.remove('error');
    }
}

// Initialiser le menu responsive et la validation du formulaire lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser le menu responsive
    const responsiveMenu = new ResponsiveMenu();
    
    // Initialiser la validation du formulaire de contact
    const contactForm = new FormValidator('contactForm');
});