<tr>
  <td>
    {if !empty($summAuthor)}
        <a href="{$url}/Author/Home?author={$summAuthor|escape:"url"}&filter[]={$filter|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
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
  {foreach from=$summURLs key=recordurl item=urldesc}
     <a href="{$recordurl|escape}" class="fulltext" target="new">{if $recordurl == $urldesc}{translate text='Online'}{else}{$urldesc|escape}{/if}</a>
  {/foreach}
  </td>
</tr>
