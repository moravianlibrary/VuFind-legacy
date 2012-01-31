<form method="GET" action="{$url}/Search/Reserves" name="searchForm" class="search">
<span class="graytitle">{translate text="Search For Items on Reserve"}</span>
<ul class="pageitem">
  <li class="menu">
    <select name="course">
      <option value="">{translate text="By Course"}:</option>
      {foreach from=$courseList item=courseName key=courseId}
        <option value="{$courseId|escape}">{$courseName|escape}</option>
      {/foreach}
    </select>
  </li>
  <li class="menu">
    <select name="inst">
      <option value="">{translate text="By Instructor"}:</option>
        {foreach from=$instList item=instName key=instId}
          <option value="{$instId|escape}">{$instName|escape}</option>
        {/foreach}
    </select>
    </li>
  <li class="menu">
    <select name="dept">
      <option value="">{translate text="By Department"}:</option>
      {foreach from=$deptList item=deptName key=deptId}
        <option value="{$deptId|escape}">{$deptName|escape}</option>
      {/foreach}
    </select>
  </li>
  <li class="form">
    <input type="submit" name="submit" value="{translate text='Find'}"/><br>
  </li>
</ul>
</form>
