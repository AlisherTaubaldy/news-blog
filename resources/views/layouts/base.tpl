<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}Blogy{/block}</title>
    <meta name="description" content="{block name="meta_description"}Статьи о разработке и технологиях{/block}">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="site-shell">
        {include file="partials/header.tpl"}
        <main class="site-main">{block name="content"}{/block}</main>
        {include file="partials/footer.tpl"}
    </div>
</body>
</html>
