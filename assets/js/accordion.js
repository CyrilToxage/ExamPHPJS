/**
 * Classe Accordion pour créer un système d'accordéon
 */
class Accordion {
    /**
     * Constructeur de la classe
     * @param {string} selector - Sélecteur CSS de l'accordéon
     */
    constructor(selector) {
        // Sélectionner l'élément accordéon
        this.accordion = document.querySelector(selector);
        
        // Vérifier si l'accordéon existe
        if (!this.accordion) {
            console.error(`Aucun élément trouvé avec le sélecteur ${selector}`);
            return;
        }
        
        // Sélectionner tous les éléments de titre
        this.items = this.accordion.querySelectorAll('.accordion-item');
        
        // Initialiser l'accordéon
        this.init();
    }
    
    /**
     * Initialise l'accordéon en ajoutant les écouteurs d'événements
     */
    init() {
        // Pour chaque élément de l'accordéon
        this.items.forEach(item => {
            // Sélectionner le header et le contenu
            const header = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-content');
            
            // Définir la hauteur initiale à 0
            content.style.maxHeight = '0px';
            
            // Ajouter un écouteur d'événement au clic sur le header
            header.addEventListener('click', () => this.toggleItem(item));
        });
    }
    
    /**
     * Bascule l'état d'un élément (ouvert/fermé)
     * @param {Element} item - L'élément à basculer
     */
    toggleItem(item) {
        // Sélectionner le contenu de l'élément
        const content = item.querySelector('.accordion-content');
        
        // Si l'élément est actif (ouvert)
        if (item.classList.contains('active')) {
            // Fermer l'élément
            content.style.maxHeight = '0px';
            item.classList.remove('active');
        } else {
            // Ouvrir l'élément
            content.style.maxHeight = content.scrollHeight + 'px';
            item.classList.add('active');
        }
    }
    
    /**
     * Ouvre un élément spécifique par son index
     * @param {number} index - Index de l'élément à ouvrir
     */
    openItem(index) {
        if (index >= 0 && index < this.items.length) {
            const item = this.items[index];
            const content = item.querySelector('.accordion-content');
            
            content.style.maxHeight = content.scrollHeight + 'px';
            item.classList.add('active');
        }
    }
    
    /**
     * Ferme un élément spécifique par son index
     * @param {number} index - Index de l'élément à fermer
     */
    closeItem(index) {
        if (index >= 0 && index < this.items.length) {
            const item = this.items[index];
            const content = item.querySelector('.accordion-content');
            
            content.style.maxHeight = '0px';
            item.classList.remove('active');
        }
    }
    
    /**
     * Ouvre tous les éléments
     */
    openAll() {
        this.items.forEach((item, index) => this.openItem(index));
    }
    
    /**
     * Ferme tous les éléments
     */
    closeAll() {
        this.items.forEach((item, index) => this.closeItem(index));
    }
}

// Initialisation de l'accordéon lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    const accordionElement = document.getElementById('faq-accordion');
    if (accordionElement) {
        const accordion = new Accordion('#faq-accordion');
    } else {
        console.log("L'élément #faq-accordion n'existe pas sur cette page");
    }
});