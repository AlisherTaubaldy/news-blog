{extends file="layouts/base.tpl"}
{block name="title"}Категории — Blogy{/block}
{block name="content"}
    <section class="page-hero"><div class="container page-hero__inner"><p class="eyebrow">Навигация</p><h1>Категории</h1><p>Выберите направление и перейдите к тематической подборке статей.</p></div></section>
    <section class="container category-directory">
        {foreach $categories as $category}
            <a class="category-tile" href="{$category.url}"><span class="eyebrow">{$category.articleCount} статьи</span><h2>{$category.name}</h2><p>{$category.description}</p><span class="text-link">Смотреть статьи →</span></a>
        {/foreach}
    </section>
{/block}
