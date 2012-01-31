<div class="span-18">
  <h3>{$pageTitle|translate}</h3>
  
  {if !$isEmpty}
  <div class="toolbar">
    <ul>
      <li><a href="{$url}/Cart/Mail" class="mail" title="{translate text='Email Items'}">{translate text='Email Items'}</a></li>
      {if is_array($exportFormats) && count($exportFormats) > 0}
      <li>
        <a title="{translate text='Export Items'}" href="{$url}/Cart/Export?style={$exportFormats.0|escape:'url'}" class="export exportMenu">{translate text='Export Items'}</a>
        <ul class="menu offscreen" id="exportMenu">
        {foreach from=$exportFormats item=exportFormat}
          <li><a href="{$url}/Cart/Export?style={$exportFormat|escape:'url'}">{$exportFormat|escape}</a></li>
        {/foreach}
        </ul>
      </li>
      {/if}
      <li><a href="{$url}/Cart/Save" class="fav" title="{translate text='Save Items'}">{translate text='Save Items'}</a></li>
      <li><a href="{$url}/Cart/Print" class="print" title="{translate text='Print Items'}">{translate text='Print Items'}</a></li>
      <li><a href="{$url}/Cart/Home?empty=1" class="cartEmpty" title="{translate text='Empty Cart'}">{translate text='Empty Cart'}</a></li>
    </ul>
    <div class="clear"></div>
  </div>
  {/if}  

  {* This is raw HTML -- do not escape it: *}
  {$cart}
</div>

<div class="span-5 last">
</div>

<div class="clear"></div>
