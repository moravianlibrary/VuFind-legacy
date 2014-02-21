{if $error}
    <div class="error">{translate text=$error}</div>
{else}
  <form class="std" method="post" action="{$url}{$formTargetPath|escape}" name="popupForm" id="puthold">
    <input type="hidden" name="item" value="{$item|escape}" />
    <input type="hidden" name="type" value="short" />
    <table>
      <tr>
        <td>{translate text="Delivery location"}: </td>
        <td>
          <select name="slot">
            {foreach from=$slots key=id item=desc}
              <option value="{$id|escape}">{$desc|escape}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td></td>
        <td><input class="form-submit" type="submit" name="submit" value='{translate text="PutHold"}'></td>
      </tr>
    </table>
  </form>
{/if}