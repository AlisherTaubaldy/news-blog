<nav class="pagination" aria-label="Страницы">
    <a class="pagination__arrow" href="#" aria-label="Предыдущая страница">←</a>
    {foreach $pagination as $page}
        <a class="pagination__page{if $page.current} is-current{/if}" href="{$page.url}" {if $page.current}aria-current="page"{/if}>{$page.number}</a>
    {/foreach}
    <a class="pagination__arrow" href="#" aria-label="Следующая страница">→</a>
</nav>
