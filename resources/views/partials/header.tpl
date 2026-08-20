<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="/">Blogy<span>.</span></a>
        <nav class="preview-nav" aria-label="Основная навигация">
            <a class="{if $currentPage === 'home'}is-active{/if}" href="/">Главная</a>
            <a class="{if $currentPage === 'blogs' || $currentPage === 'article'}is-active{/if}" href="/blogs">Статьи</a>
            <a class="{if $currentPage === 'categories' || $currentPage === 'category'}is-active{/if}" href="/categories">Категории</a>
        </nav>
    </div>
</header>
