<div data-role="page" id="MyResearch-login">
  {include file="header.tpl"}
  <div data-role="content">
    {if $offlineMode == "ils-offline"}
      <div class="sysInfo">
        <h2>{translate text="ils_offline_title"}</h2>
        <p><strong>{translate text="ils_offline_status"}</strong></p>
        <p>{translate text="ils_offline_login_message"}</p>
        <p><a href="mailto:{$supportEmail}">{$supportEmail}</a></p>
      </div>
    {elseif $hideLogin}
      <div class="error">{translate text='login_disabled'}</div>
    {/if}

    {if !$hideLogin}
      <h3>{$pageTitle|escape}</h3>
      {if $message}<div class="error">{$message|translate}</div>{/if}
      {if $authMethod != 'Shibboleth'}
      <form method="post" action="{$path}/MyResearch/Home" name="loginForm" data-ajax="false">
        <div data-role="fieldcontain">
          <label for="login_username">{translate text='Username'}:</label>
          <input id="login_username" type="text" name="username" value="{$username|escape}"/>
          <label for="login_password">{translate text='Password'}:</label>
          <input id="login_password" type="password" name="password"/>
        </div>
        <div data-role="fieldcontain">
          <input type="submit" name="submit" value="{translate text='Login'}"/>
        </div>
          {if $followup}<input type="hidden" name="followup" value="{$followup}"/>{/if}
          {if $followupModule}<input type="hidden" name="followupModule" value="{$followupModule}"/>{/if}
          {if $followupAction}<input type="hidden" name="followupAction" value="{$followupAction}"/>{/if}
          {if $recordId}<input type="hidden" name="recordId" value="{$recordId|escape:"html"}"/>{/if}
          {if $comment}<input type="hidden" name="comment" name="comment" value="{$comment|escape:"html"}"/>{/if}
        </form>
        {if $authMethod == 'DB'}<a rel="external" data-role="button" href="{$path}/MyResearch/Account">{translate text='Create New Account'}</a>{/if}
      {/if}
    {/if}
  </div>
  {include file="footer.tpl"}
</div>

