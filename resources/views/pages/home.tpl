{extends file="layouts/base.tpl"}
{block name="title"}Blogy — свежие статьи{/block}
{block name="content"}
    <section class="hero container">
        <p class="eyebrow">Блог о технологиях</p>
        <h1>Разбираем сложное<br>простыми словами.</h1>
        <p class="hero__lead">Практические материалы о PHP, архитектуре, базах данных и интерфейсах.</p>
    </section>
    <div class="container home-sections">
        {foreach $categories as $category}
            {include file="partials/category-section.tpl" category=$category}
        {/foreach}
    </div>
{/block}
