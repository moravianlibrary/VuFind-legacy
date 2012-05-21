  <script type="text/javascript">
  vufindString.bulk_noitems_advice = "{translate text="bulk_noitems_advice"}";
  vufindString.confirmEmpty = "{translate text="bookbag_confirm_empty"}";
  vufindString.viewBookBag = "{translate text="View Book Bag"}";
  vufindString.addBookBag = "{translate text="Add to Book Bag"}";
  vufindString.removeBookBag = "{translate text="Remove from Book Bag"}";
  </script>
  
  {if $errorMsg}<div class="error">{$errorMsg|translate}</div>{/if}
  {if $infoMsg}<div class="success">{$infoMsg|translate}</div>{/if}
  
  {if $showExport} <div class="success"><a class="save" target="_new" href="{$url}/Cart/Export?exportInit">{translate text="export_save"}</a></div>{/if}
  <form method="post" name="cartForm" action="{$url}/Cart/Home">
  {if !$bookBag->isEmpty()}
  <div class="toolbar">
    <ul>
      <li>
        <div class="control">
          <input type="checkbox" class="selectAllCheckboxes floatleft" name="selectAll" id="cartCheckboxSelectAll"/> <label for="cartCheckboxSelectAll" class="floatleft">{translate text="select_page"}</label>
        </div>
      </li>
      <li>| {translate text="with_selected"}:</li>
      <li>
        <input type="submit" class="fav button" name="saveCart" value="{translate text='bookbag_save_selected'}" title="{translate text='bookbag_save'}"/>
      </li>
      <li>
        <input type="submit" class="button mail" name="email" value="{translate text='bookbag_email_selected'}" title="{translate text='bookbag_email'}"/>
      </li>
      {if is_array($exportOptions) && count($exportOptions) > 0}
      <li>
        <input type="submit" class="export button" name="export" value="{translate text='bookbag_export_selected'}" title="{translate text='bookbag_export'}"/>
      </li>
      {/if}
      <li>
        <input type="submit" class="print button" name="print" value="{translate text='bookbag_print_selected'}" title="{translate text='print_selected'}"/>
      </li>
      <li>
        <input type="submit" class="button delete" name="delete" value="{translate text='bookbag_delete_selected'}" title="{translate text='bookbag_delete'}"/>
      </li>
      <li>
        <input type="submit" class="bookbagEmpty button" name="empty" value="{translate text='Empty Book Bag'}" title="{translate text='Empty Book Bag'}"/>
      </li>
      <div class="clearer"></div>
    </ul>
  </div>
  {/if}
   
  {include file="Cart/cart.tpl"}
  </form>
