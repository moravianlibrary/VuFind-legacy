{if $lastModule == "MyResearch"}
<div id="leftnavbody"><a href="{$url}/{$lastModule}/{$lastAction}" class="backtosearch">{translate text="Back To "|cat:$lastAction}</a></div><br /><br />
{elseif $lastsearch}
<div id="leftnavbody"><a href="{$lastsearch|escape}" class="backtosearch">{translate text="back_to_results_short"}</a></div><br /><br />
{/if}

<ul class="pageitem">
  <li>{include file=$coreMetadata}</li>
  <li>{include file="Record/$subTemplate"}</li>
</ul>