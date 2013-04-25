{* author, title, ... *}
{translate text='Main Author'}: {$coreMainAuthor|escape} 
{translate text='Title'}: {$coreShortTitle|escape} {if $coreSubtitle}{$coreSubtitle|escape}{/if} {if $coreTitleSection}{$coreTitleSection|escape}{/if} 
{if !$full && $coreLocations}
{translate text='Location'}: 
{foreach from=$coreLocations item=coreLocation}
  {$coreLocation|escape}
{/foreach}
{/if}
{if $full} 
{translate text='Published'}: {foreach from=$corePublications item=field}{$field|escape} {/foreach} 
{/if}
{* ISBN *}
{if $isbn and $full}
{translate text='ISBN / ISSN'}: {$isbn|escape} 
{/if}
{* Keywords *}
{if $coreSubjects and $full}
{translate text='Subjects'|translate}:
{foreach from=$coreSubjects item=coreSubject}
  {foreach from=$coreSubject item=coreSubjectPart}{$coreSubjectPart|escape} {/foreach}  
{/foreach}
{/if}
{* Location *}
{* Callnumbers *}
{if $full}
{translate text='Callnumber'}:
{foreach from=$callNumber item=item}
  {$item|escape} 
{/foreach}
{/if}
{* Series *}
{if !empty($coreSeries) and $full}
{translate text='Series'}:
{foreach from=$coreSeries item=field}
{if is_array($field)}
{if !empty($field.name)}
  {$field.name|escape} {if !empty($field.number)} {$field.number|escape} {/if}  
{/if}
{else}
{$field|escape} 
{/if}
{/foreach}
{/if}
{* Physical description *}
{if $physical and $full}
{translate text='Physical Description'}: {foreach from=$physical item=field}{$field|escape}{/foreach}
{/if}

____________________________________ 
