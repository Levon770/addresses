<div style="width:33%; float: left;">
    <h2>{$title}</h2>
    {foreach from=$model item=$item}
        <div>{$item['addresses_address']} {$item['addresses_street_name']} ( {$item['distance']|round:2} Km )</div>
    {/foreach}
</div>