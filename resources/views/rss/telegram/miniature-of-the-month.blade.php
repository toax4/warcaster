@if ($article->data["warcaster_tag"] == "miniature-of-the-month" )
⚔️ <b>La miniature du mois est là !</b>
@elseif ($article->data["warcaster_tag"] == "coin-of-the-month" )
🪙 <b>La pièce du mois est là !</b>
@endif