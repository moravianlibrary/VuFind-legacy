Dostupnost:
{if $statusItems[0].present_total == 0 && $statusItems[0].absent_total == 0}
  {translate text='no items'}
{else}
  {if $statusItems[0].absent_total > 0}
    {if $statusItems[0].absent_avail > 0}
    <span class="available">
    {else}
    <span class="checkedout">
    {/if}
    {translate text='absent'} : {$statusItems[0].absent_avail} {translate text='availability_of'} {$statusItems[0].absent_total} 
    </span>
  {/if}
  {if $statusItems[0].present_total > 0}
    {if $statusItems[0].present_avail > 0}
    <span class="available">
    {else}
    <span class="checkedout">
    {/if}
    {translate text='present'} : {$statusItems[0].present_avail} {translate text='availability_of'} {$statusItems[0].present_total}
    </span>
  {/if}
{/if}