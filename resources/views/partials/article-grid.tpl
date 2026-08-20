<div class="article-grid">
    {foreach $articles as $article}
        {include file="partials/article-card.tpl" article=$article}
    {/foreach}
</div>
