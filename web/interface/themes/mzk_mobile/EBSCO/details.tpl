{* Display Title *}
<div class="graytitle">{$record.Items.Title.Data}</div>
{* End Title *}

{* Display Main Details *}
<table class="details">
  {foreach from=$record.Items item=item key=key name=records}
  	{if !empty($item.Data) && $key != 'Title'}
    	<tr valign="top">
        	<th>{translate text=$key}: </th>
        	{if $key == 'TypeDocument'}
        		<td>{translate text=$item.Data}</td>
        	{else}
            	<td>{$item.Data|link_urls}</td>
            {/if}
        </tr>
    {/if}
  {/foreach}

  {if $physical}
    <tr valign="top">
      <th>{translate text='Physical Description'}: </th>
      <td>
        {foreach from=$physical item=field name=loop}
          {$field|escape}<br>
        {/foreach}
      </td>
    </tr>
  {/if}
  {if $record.FullText.Links.pdflink}
  	<tr>
  		<th>{translate text="PDF full text"}</th>
		<td>
			<a href="{$record.FullText.Links.pdflink}" class="icon pdf fulltext">{translate text="PDF full text"}</a>
		</td>
	</tr>
  {/if}
  {if $record.PLink}
  	<tr>
  		<th>{translate text='View in EDS'}</th>
  		<td>
			<a href="{$record.PLink}">
				{translate text='View in EDS'}
			</a>
		</td>
	</tr>
  {/if}
</table>
{* End Main Details *}
