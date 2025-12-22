<?php

$source_name = $article->source->name;

$icon = "🖌️";
?>


<i>{{ $source_name }}</i>
{{ $icon }} <b>{{ $article->data["title"] }}</b>

<b>Prix :</b> {{ number_format($article->data["price"], 2, ",", "") }} €

{{ ($article->data["summary"]) }}

<a href="{{ $article->link }}">🛒 Voir dans la Boutique</a>