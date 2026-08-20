<article class="article-card">
    <a class="article-card__media" href="{$article.url}">
        <img src="{$article.image_url}" alt="{$article.image_alt}" width="560" height="340" loading="lazy">
    </a>
    <div class="article-card__body">
        <div class="article-card__meta">
            <time datetime="{$article.published_at_iso}">{$article.published_at_formatted}</time>
            <span>{$article.views} просмотров</span>
        </div>
        <h3><a href="{$article.url}">{$article.title}</a></h3>
        <p>{$article.description}</p>
        <a class="text-link" href="{$article.url}">Читать статью <span aria-hidden="true">→</span></a>
    </div>
</article>
