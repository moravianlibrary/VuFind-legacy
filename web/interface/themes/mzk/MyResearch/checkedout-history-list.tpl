<ul class="filters">
	{foreach from=$transList item=resource name="recordLoop"} {if
	($smarty.foreach.recordLoop.iteration % 2) == 0}
	<li class="result alt">{else}
	
	<li class="result">{/if} <span class="order">{$smarty.foreach.recordLoop.iteration}.&nbsp;</span>
		{if $renewForm} {if $resource.ils_details.renewable &&
		$resource.ils_details.renew_details}
		<div class="hiddenLabel">
			<label
				for="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}">{translate
				text="Select this record"}</label>
		</div> <input type="checkbox" name="renewSelectedIDS[]"
		value="{$resource.ils_details.renew_details}" class="ui_checkboxes"
		id="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" />
		<input type="hidden" name="renewAllIDS[]"
		value="{$resource.ils_details.renew_details}" /> {/if} {/if}
		<div class="yui-ge">
			<div class="yui-u first" style="background-color: transparent">
				<img
					src="{$path}/bookcover.php?isn={$resource.isbn|@formatISBN}&amp;size=small"
					class="alignleft" alt="{$resource.title|escape}">

				<div class="resultitem">
					{* If $resource.id is set, we have the full Solr record loaded and
					should display a link... *} {if !empty($resource.id)}
					<p class="resultItemLine1">
						<a href="{$url}/Record/{$resource.id|escape:" url"}" class="title">{$resource.title|escape}</a>
					</p>
					{* If the record is not available in Solr, perhaps the ILS driver
					sent us a title we can show... *} {elseif
					!empty($resource.ils_details.title)}
					<p class="resultItemLine1">
						<a
							href="{$url}/Search/Barcode?barcode={$resource.ils_details.barcode|escape:"
							url"}" class="title">{$resource.ils_details.title|escape}</a>
					</p>
					{*<p class="resultItemLine1">{$resource.ils_details.title|escape}*}
					{* Last resort -- indicate that no title could be found. *} {else}
					{translate text='Title not available'} {/if} {if $resource.author}
					<p>
						{translate text='by'}: <a
							href="{$url}/Author/Home?author={$resource.author|escape:"url"}">{$resource.author|escape}</a>
					</p>
					{else}
					<p>
						{translate text='by'}: <a
							href="{$url}/Author/Home?author={$resource.ils_details.author|escape:"url"}">{$resource.ils_details.author|escape}</a>
					</p>
					{/if} {if $resource.tags} {translate text='Your Tags'}: {foreach
					from=$resource.tags item=tag name=tagLoop} <a
						href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape}</a>{if
					!$smarty.foreach.tagLoop.last},{/if} {/foreach} {/if} {if
					$resource.notes} {translate text='Notes'}: {$resource.notes|escape}<br>
					{/if} {if is_array($resource.format)} {foreach
					from=$resource.format item=format}
					<p class="resultItemLine2">
						<span class="iconlabel {$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate
							text=$format}</span>
					</p>
					{/foreach} {elseif isset($resource.format)}
					<p>
						<span class="iconlabel {$resource.format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate
							text=$resource.format}</span>
					</p>
					{/if} {if $resource.ils_details.volume}
					<p>
						<strong>{translate text='Volume'}:</strong>
						{$resource.ils_details.volume|escape}
					</p>
					{/if} {if $resource.ils_details.publication_year}
					<p>
						<strong>{translate text='Year of Publication'}:</strong>
						{$resource.ils_details.publication_year|escape}
					</p>
					{/if} {assign var="showStatus" value="show"} {if
					$renewResult[$resource.ils_details.item_id]} {if
					$renewResult[$resource.ils_details.item_id].success} {assign
					var="showStatus" value="hide"} <strong>{translate text='Due Date'}:
						{$renewResult[$resource.ils_details.item_id].new_date} {if
						$renewResult[$resource.ils_details.item_id].new_time}{$renewResult[$resource.ils_details.item_id].new_time|escape}{/if}</strong>
					<div class="userMsg">{translate text='renew_success'}</div>
					{else} <strong>{translate text='Due Date'}:
						{$resource.ils_details.duedate|escape} {if
						$resource.ils_details.dueTime}
						{$resource.ils_details.dueTime|escape}{/if}</strong>
					<div class="error">{translate text='renew_fail'}{if
						$renewResult[$resource.ils_details.item_id].sysMessage}:
						{$renewResult[$resource.ils_details.item_id].sysMessage|escape}{/if}</div>
					{/if} {else}
					<p>
						<strong> {translate text='Returned'}:
							{$resource.ils_details.returned|escape} | {translate text='Due
							Date'}: {$resource.ils_details.duedate|escape} </strong>
					</p>
					{if $showStatus == "show"} {if $resource.ils_details.dueStatus ==
					"overdue"}
					<div class="error">{translate text="renew_item_overdue"}</div>
					{elseif $resource.ils_details.dueStatus == "due"}
					<div class="userMsg">{translate text="renew_item_due"}</div>
					{/if} {/if} {/if} {if $showStatus == "show" &&
					$resource.ils_details.message}
					<div class="userMsg">{translate text=$resource.ils_details.message}</div>
					{/if} {if $resource.ils_details.renewable &&
					$resource.ils_details.renew_link} <a
						href="{$resource.ils_details.renew_link|escape}">{translate
						text='renew_item'}</a> {/if}
				</div>
			</div>
		</div>
	</li> {/foreach}
</ul>
{if $renewForm}
</form>
{/if}
