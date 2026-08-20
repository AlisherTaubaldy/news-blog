<section class="category-section">
    <header class="section-heading">
        <div>
            <p class="eyebrow">Категория</p>
            <h2>{$category.name}</h2>
        </div>
        <a class="text-link" href="{$category.url}">Все статьи <span aria-hidden="true">→</span></a>
    </header>
    {include file="partials/article-grid.tpl" articles=$category.articles}
</section>
