{* author, title, ... *}
{translate text='Main Author'}: {if $coreMainAuthor} {$coreMainAuthor}{/if}{$rn}
{translate text='Title'}: {$coreShortTitle}{if $coreSubtitle} {$coreSubtitle}{/if} {if $coreTitleSection}{$coreTitleSection}{/if}{$rn}
{if !$full && $coreLocations}
{translate text='Location'}:{$rn}
{foreach from=$coreLocations item=coreLocation}
  {$coreLocation}{$rn}
{/foreach}
{/if}
{if $full}
{translate text='Published'}: {foreach from=$corePublications item=field}{$field}{/foreach}{$rn}
{/if}
{* ISBN *}
{if $isbn and $full}
{translate text='ISBN / ISSN'}: {$isbn}{$rn}
{/if}
{* Keywords *}
{if $coreSubjects and $full}
{translate text='Subjects'|translate}:{$rn}
{foreach from=$coreSubjects item=coreSubject}
  {foreach from=$coreSubject item=coreSubjectPart}{$coreSubjectPart} {/foreach}{$rn}
{/foreach}
{/if}
{* Location *}
{* Callnumbers *}
{if $full}
{translate text='Callnumber'}:{$rn}
{foreach from=$callNumber item=item}
  {$item}{$rn}
{/foreach}
{/if}
{* Series *}
{if !empty($coreSeries) and $full}
{translate text='Series'}:{$rn}
{foreach from=$coreSeries item=field}
{if is_array($field)}
{if !empty($field.name)}
  {$field.name} {if !empty($field.number)} {$field.number} {/if}{$rn}
{/if}
{else}
{$field}
{/if}
{/foreach}
{/if}
{* Physical description *}
{if $physical and $full}
{translate text='Physical Description'}: {foreach from=$physical item=field}{$field}{/foreach}{$rn}
{/if}
____________________________________ 
