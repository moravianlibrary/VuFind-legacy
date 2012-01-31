<tr>
  <td>
    {if !empty($summAuthor)}
        <a href="{$url}/Author/Home?author={$summAuthor|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
    {/if}
  </td>
  <td>
     {if $summFormats[0] == "WEB"}
     {foreach from=$summURLs key=recordurl item=urldesc}
         <a href="{$recordurl}" class="title">{$summTitle|truncate:180:"..."|escape}</a>
         {/foreach}
     {else}
     <a href="{$url}/Record/{$summId|escape:"url"}" class="title">{if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if}</a>
     {/if}
  </td>
  <td>{if $summDate}{$summDate[0]}{/if}</td>
  <td>
    <div class="status" id="status{$itemLink|escape}"> {*was {$summId|escape} *}
      {if $summFormats[0] != "WEB"}<span class="unknown" style="font-size: 8pt;">{translate text='Loading'}...</span>{/if}
    </div>
  </td>
  {if $summAjaxStatus}
  <script type="text/javascript">
    getStatuses('{$summId|escape:"javascript"}');
  </script>
  {/if}
</tr>
