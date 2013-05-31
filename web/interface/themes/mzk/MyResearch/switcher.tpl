{assign var='views' value=','|explode:"list,table"}
{foreach from=$views item=view}
  {if $currentView != $view}
    <a href="{$path}?view={$view|escape}&limit={$currentLimit|escape}" title='{"Switch view to `$view`"|translate}' style="text-decoration:none">
  {/if}
  {image src="view_`$view`.png"}
  {if $currentView != $view}
    </a>
  {/if}
  &nbsp;
{/foreach}

