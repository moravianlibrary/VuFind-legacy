<form method="get" action="{$path}/EBSCO/Search" name="searchForm">
	<span class="graytitle">{translate text='Search'}</span>
	<ul class="pageitem">
	  <li class="form">
	    <input type="text" name="lookfor"/>
	  </li>
	  <li class="form">
	  	<select id="type" name="type" class="select">
	  	{foreach from=$basicSearchTypes item=searchDesc key=searchVal}
			<option value="{$searchVal}"{if $searchIndex == $searchVal} selected{/if}>{translate text=$searchDesc}</option>
		{/foreach}
		</select>
	  </li>
	</ul>
	
	<div class="submit">
	    <input type="submit" name="submit" value="{translate text="Find"}"/>
	</div>
</form>
	
	<ul class="pageitem">
		<li class="menu">
			<a href="{$path}/EBSCO/Advanced"><span class="name">{translate text="Advanced Search"}</span><span class="arrow"></span></a>
		</li>
	</ul>
	{if $lastSort}<input type="hidden" name="sort" value="{$lastSort|escape}" />{/if}