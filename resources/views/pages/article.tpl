{extends file="layouts/base.tpl"}
{block name="title"}{$article.title} — Blogy{/block}
{block name="meta_description"}{$article.description}{/block}
{block name="content"}
    <article class="article-page">
        <header class="article-hero container">
            <div class="article-hero__categories">
                {foreach $article.categories as $category}<a href="{$category.url}">{$category.name}</a>{/foreach}
            </div>
            <h1>{$article.title}</h1>
            <p class="article-hero__lead">{$article.description}</p>
            <div class="article-hero__meta">
                <time datetime="{$article.published_at_iso}">{$article.published_at_formatted}</time>
                <span>{$article.views} просмотров</span>
            </div>
        </header>
        <div class="container article-page__media"><img src="{$article.image_url}" alt="{$article.image_alt}"></div>
        <div class="article-content container">
            {foreach $article.paragraphs as $paragraph}<p>{$paragraph}</p>{/foreach}
            <blockquote>Хорошая архитектура делает направление зависимостей очевидным и сохраняет код понятным.</blockquote>
        </div>
    </article>
    <section class="related container">
        <header class="section-heading"><div><p class="eyebrow">Продолжить чтение</p><h2>Похожие статьи</h2></div></header>
        {include file="partials/article-grid.tpl" articles=$relatedArticles}
    </section>
{/block}
