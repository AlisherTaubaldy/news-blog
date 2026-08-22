{extends file="layouts/base.tpl"}
{block name="title"}{$category.name} — Blogy{/block}
{block name="meta_description"}{$category.description}{/block}
{block name="content"}
    <section class="page-hero">
        <div class="container page-hero__inner">
            <p class="eyebrow">Категория</p>
            <h1>{$category.name}</h1>
            <p>{$category.description}</p>
        </div>
    </section>
    <section class="container listing-page">
        <div class="listing-page__toolbar">
            <p>Найдено {$totalCount} статей</p>
            {include file="partials/sorting.tpl" sorting=$sorting}
        </div>
        {include file="partials/article-grid.tpl" articles=$articles}
        {include file="partials/pagination.tpl" pagination=$pagination}
    </section>
{/block}
