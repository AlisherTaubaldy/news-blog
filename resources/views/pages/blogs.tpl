{extends file="layouts/base.tpl"}
{block name="title"}Все статьи — Blogy{/block}
{block name="content"}
    <section class="page-hero"><div class="container page-hero__inner"><p class="eyebrow">Библиотека</p><h1>Все статьи</h1><p>Свежие материалы о PHP, архитектуре, базах данных и интерфейсах.</p></div></section>
    <section class="container listing-page">
        <div class="listing-page__toolbar"><p>Найдено {$totalCount} статей</p>{include file="partials/sorting.tpl" sorting=$sorting}</div>
        {include file="partials/article-grid.tpl" articles=$articles}
        {include file="partials/pagination.tpl" pagination=$pagination}
    </section>
{/block}
