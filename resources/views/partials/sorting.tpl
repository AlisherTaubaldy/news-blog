<nav class="sorting" aria-label="Сортировка статей">
    <span>Сортировать:</span>
    {foreach $sorting as $option}
        <a class="sorting__option{if $option.active} is-active{/if}" href="{$option.url}" {if $option.active}aria-current="page"{/if}>{$option.label}</a>
    {/foreach}
</nav>
