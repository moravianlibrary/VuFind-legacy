{if $offlineMode == "ils-offline"}
  <div class="sysInfo">
    <h2>{translate text="ils_offline_title"}</h2>
    <p><strong>{translate text="ils_offline_status"}</strong></p>
    <p>{translate text="ils_offline_login_message"}</p>
    <p><a href="mailto:{$supportEmail}">{$supportEmail}</a></p>
  </div>
{else}
  <span class="greytitle">{translate text='Library Catalog Profile'}</span>
  {if $loginError}
  <ul class="pageitem"><li>{translate text=$loginError}</li></ul>
  {/if}

  <form method="post">
  <ul class="pageitem">
    <li class="textbox">{translate text='cat_establish_account'}</li>
    <li class="form"><input type="text" name="cat_username" placeholder="{translate text='cat_username_abbrev'}"></li>
    <li class="form"><input type="text" name="cat_password" placeholder="{translate text='cat_password_abbrev'}"></li>
    <li class="form"><input type="submit" name="submit" value="{translate text='Save'}"></li>
  </ul>
  </form>
{/if}