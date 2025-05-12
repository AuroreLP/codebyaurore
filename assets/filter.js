document.addEventListener("DOMContentLoaded", () => {
    const categoryFilter    = document.getElementById("category-filter");
    const tagFilter         = document.getElementById("tag-filter");
    const articlesContainer = document.getElementById("articles-container");
  
    function fetchArticles() {
      const params = new URLSearchParams();
      if (categoryFilter.value) params.append("category", categoryFilter.value);
      if (tagFilter.value)      params.append("tag", tagFilter.value);
  
      fetch(`/api/articles?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
          console.log("API /api/articles response:", data);
          articlesContainer.innerHTML = "";
  
          if (!data.articles.length) {
            articlesContainer.innerHTML = "<p>Aucun article trouvé.</p>";
            return;
          }
  
          data.articles.forEach(article => {
            const el = document.createElement("article");
            el.classList.add("blog__article");
            el.innerHTML = `
              <div class="blog__article-header">
                <h2 class="title">
                  <a href="/blog/${article.slug}">${article.title}</a>
                </h2>
                <span class="category">${article.category}</span>
              </div>
              <div class="blog__article-meta-container">
                <p class="blog__article-meta">${article.meta}</p>
                <a href="/blog/${article.slug}" class="read-more-link">Lire la suite</a>
              </div>
              <div class="blog__article-footer">
                <span class="date">Publié le ${article.publishedAt}</span>
                <div class="tags">
                  ${article.tags.map(t => `<span>#${t}</span>`).join(' ')}
                </div>
              </div>
            `;
            articlesContainer.appendChild(el);
          });
        })
        .catch(err => console.error("Erreur fetch /api/articles :", err));
    }
  
    categoryFilter.addEventListener("change", fetchArticles);
    tagFilter.addEventListener("change", fetchArticles);
  });