<div>More than 1 item found for: <h3>{$match}</h3></div>
<div>
{foreach from=$matches key=row item=v}
<li>{$row}: <a href="?q={$v.upc}">{$v.description}</a></li>
{/foreach}
</div>
