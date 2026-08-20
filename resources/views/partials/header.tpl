<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="/preview.php">Blogy<span>.</span></a>
        <nav class="preview-nav" aria-label="Предпросмотр страниц">
            <a class="{if $currentPage === 'home'}is-active{/if}" href="/preview.php">Главная</a>
            <a class="{if $currentPage === 'category'}is-active{/if}" href="/preview.php?page=category">Категория</a>
            <a class="{if $currentPage === 'article'}is-active{/if}" href="/preview.php?page=article">Статья</a>
        </nav>
    </div>
</header>
