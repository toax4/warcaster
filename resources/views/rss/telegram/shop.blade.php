<?php

$source_name = $article->source->name;

if($article->data["isPreOrder"]) {
    $source_name = str_replace("Shop", "Précommandes", $source_name);
    // $source_name .= " / Précommandes";
} elseif($article->data["isNewRelease"]) {
    $source_name = str_replace("Shop", "Nouveautés", $source_name);
    // $source_name .= " / Nouveautés";
}

// dd($article->data["productType"]);
if($article->data["productType"] == "book") {
    $icon = "📖";
    $source_name .= " - Black Library";
} elseif($article->data["productType"] == "miniatureKit") {
    $icon = "⚔️";
    $source_name .= " - " . implode(" / ", $article->data["games"]);
} elseif($article->data["productType"] == "rulebookCards") {
    $icon = "🪪";
    $source_name .= " - " . implode(" / ", $article->data["games"]);
} elseif($article->data["productType"] == "licensedProduct") {
    $icon = "🧸";
} elseif($article->data["productType"] == "gamingAccessory") {
    $icon = "🎲";
} elseif($article->data["productType"] == "magazine") {
    $icon = "🗞️";
    $source_name .= " - White Dwarf";
} else {
    $icon = "🛡️";
}



?>


<i>{{ $source_name }}</i> 
{{ $icon }} <b>{{ $article->data["title"] }}</b>

<b>Prix :</b> {{ number_format($article->data["price"], 2, ",", "") }} €

@if ($article->data["productType"] == "book")
    {!! $article->data["summary"] !!}
@else
    {{ $article->data["summary"] }}
@endif

<a href="{{ $article->link }}">🛒 Voir dans la Boutique</a>