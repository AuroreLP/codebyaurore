import { Octokit } from "https://esm.sh/octokit"

class About {
    constructor() {
        this.descriptionHTML = document.querySelector('.js-home-description')
        this.profilHTML = document.querySelector('.js-home-profil-url')
        this.avatarHTML = document.querySelector('.js-home-avatar')

        this.projectsTitle = document.querySelectorAll('.js-home-project-title')
        this.projectsDescription = document.querySelectorAll('.js-home-project-description')
        this.projectsTagsContainer = document.querySelectorAll('.js-home-project-tags-container')
        this.projectsLinks = document.querySelectorAll('.js-home-project-link'); 

        console.log("projectsTitle:", this.projectsTitle); // Debugging
        console.log("projectsDescription:", this.projectsDescription);
        console.log("projectsTagsContainer:", this.projectsTagsContainer);
        console.log("projectsLinks:", this.projectsLinks);

        this.init()
    }

    init() {
        this.getUserInformations();
        this.getReposInformations(); // Appel de la récupération des repos
    }

    getUserInformations() {
        // API façon #1: récupérer le contenu avec un fetch
        fetch("https://api.github.com/users/AuroreLP")
            .then((response) => response.json())
            .then((data) => {
                this.updateHTMLUser(data)
            })
            .catch((error) => {
                console.log("ERREUR lors de l'appel api getUserInformations", error)
            })
    }

    async getReposInformations() {
        // API façon n*2 : récupérer le contenu avec l'octokit JS et avec "await / async"
        const octokit = new Octokit()
        // URL de l'API classique: https://api.github.com/users/AuroreLP/repos
        try {
            const response = await octokit
            .request("GET /users/AuroreLP/repos", {
                sort: "created", // Trie par date de création (du plus récent au plus ancien)
                per_page: 3      // Limite à 3 dépôts
            })
            this.updateHTMLProjects(response.data)
        } catch(error) {
                console.log("ERREUR lors de l'appel api getReposInformations", error)
            }
    }

    async getRepoLanguages(repoName) {
        const octokit = new Octokit();
        try {
            const response = await octokit.request(`GET /repos/AuroreLP/${repoName}/languages`);
            return Object.keys(response.data); // Récupère uniquement les noms des langages
        } catch (error) {
            console.log(`ERREUR lors de l'appel API pour les langages du repo ${repoName}`, error);
            return ["Non spécifié"]; // Si erreur, retourne "Non spécifié"
        }
    }

    async updateHTMLProjects(repos) {
        for (let i = 0; i < repos.length; i++) {
            const repo = repos[i]
            console.log(`Project URL: ${repo.html_url}`); 

            if (this.projectsTitle[i]) this.projectsTitle[i].textContent = repos[i].name;
            if (this.projectsDescription[i]) this.projectsDescription[i].textContent = repos[i].description || "Pas de description";
             // Link
             if (this.projectsLinks[i]) {
                console.log(`Setting link: ${repo.html_url}`); // Debugging
                this.projectsLinks[i].setAttribute('href', repo.html_url)
                this.projectsLinks[i].setAttribute('target', '_blank')
            }

            const languages = await this.getRepoLanguages(repos[i].name);
            if (this.projectsTagsContainer[i]) {
                this.projectsTagsContainer[i].innerHTML = ""; // On vide le conteneur
              
                languages.forEach((lang) => {
                    const tag = document.createElement("span");
                    tag.textContent = lang;
                    this.projectsTagsContainer[i].appendChild(tag);
                });
            }
        }
    }

    updateHTMLUser(APIdata) {
        if (this.descriptionHTML) this.descriptionHTML.textContent = APIdata.bio;
        if (this.profilHTML) this.profilHTML.setAttribute("href", APIdata.html_url);
        if (this.avatarHTML) this.avatarHTML.setAttribute("src", APIdata.avatar_url);
    }

}

// ✅ Instanciation de la classe après la définition
document.addEventListener('DOMContentLoaded', () => {
    new About();
});
