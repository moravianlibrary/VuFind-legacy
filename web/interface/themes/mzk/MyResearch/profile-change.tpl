{if count($pickup) > 1}
  {assign var='showHomeLibForm' value=true}
{else}
  {assign var='showHomeLibForm' value=false}
{/if}
<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
    {if $user->cat_username}
      <div class="resulthead"><h3>{$pageTitle|escape|translate}</h3></div>
      <div class="page">
      {if $userErrorMsg}
        <p class="error">{translate text=$userErrorMsg}</p>
      {/if}
      {if $userMsg}
        <p class="info">{translate text=$userMsg}</p>
      {/if}
      {if $operation == 'password' && $showPasswordChangeForm}
      <form class="std" method="post" action="{$url}/MyResearch/ProfileChange?op=password">
        <table>
          <tbody>
            <tr>
              <td><label for="old_password">{translate text='Old password'}</label></td>
              <td><input type="password" name="old_password" /></td>
            </tr>
            <tr>
              <td><label for="new_password">{translate text='New password'}</label></td>
              <td><input type="password" name="new_password" /></td>
            </tr>
            <tr>
              <td><label for="new_password">{translate text='New password - repeat'}</label></td>
              <td><input type="password" name="new_password_repeat" /></td>
            </tr>
            <tr>
              <td><input class="form-submit" type="submit" name="submit" value='{translate text="Change password"}'></td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" name="hmac" value="{$hmac|escape:html}"></input>
      </form>
      {elseif $operation == 'nickname' && $showNicknameChangeForm}
      <form class="std" method="post" action="{$url}/MyResearch/ProfileChange?op=nickname">
        <table>
          <tbody>
            <tr>
              <td><label for="nickname">{translate text='Nickname'}</label></td>
              <td><input type="text" name="nickname" value="{$nickname|escape:html}"/></td>
            </tr>
            <tr>
              <td><input class="form-submit" type="submit" name="submit" value='{translate text="Change nickname"}'></td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" name="hmac" value="{$hmac|escape:html}"></input>
      </form>
      {elseif $operation == 'email' && $showEmailChangeForm}
      <form class="std" method="post" action="{$url}/MyResearch/ProfileChange?op=email">
        <table>
          <tbody>
            <tr>
              <td><label for="email">{translate text='New email address'}</label></td>
              <td><input type="text" name="email" value="{$email|escape:html}"/></td>
            </tr>
            <tr>
              <td><input class="form-submit" type="submit" name="submit" value='{translate text="Change email"}'></td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" name="hmac" value="{$hmac|escape:html}"></input>
      </form>
      {/if}
      </div>
    {else}
      <div class="page">
      {include file="MyResearch/catalog-login.tpl"}
      </div>
    {/if}
    <b class="bbot"><b></b></b>
    </div>
  
</div>
  {include file="MyResearch/menu.tpl"}

</div>