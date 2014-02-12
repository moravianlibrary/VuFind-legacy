<form method="get" action="{$path}/Search/Results">
<span class="graytitle">{translate text='Search'}</span>
<ul class="pageitem">
  <li class="form">
    <input type="text" name="lookfor" speech x-webkit-speech/>
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
{if $lastSort}<input type="hidden" name="sort" value="{$lastSort|escape}" />{/if}
</form>

<ul class="pageitem">
  {* TODO: implement advanced search and browse for mobile template
  <li class="menu"><a href="{$path}/Browse/Home"><img alt="search" src="{$path}/interface/themes/mobile/iWebKit/images/browse.png" /><span class="name">Browse</span><span class="arrow"></span></a></li>
   *}
    <li class="menu">
    	<a href="{$path}/Search/Advanced"><span class="name">{translate text="Advanced Search"}</span><span class="arrow"></span></a>
    </li>
    <li class="menu">
    	{useragent_match pattern='/iPhone/i' var='iPhone'}
    	{useragent_match pattern='/iPad/i' var='iPad'}
    	{if $iPhone || $iPad}
    		{* Homepage: https://code.google.com/p/zxing/wiki/ScanningFromWebPages *}
    		<a href="zxing://scan/?ret={$url|escape}/Search/EAN%3Fcode%3D%7BCODE%7D%26format%3D%7BFORMAT%7D&SCAN_FORMATS=EAN_13">
    	{else}
			<a href="http://zxing.appspot.com/scan?ret={$url|escape}/Search/EAN%3Fcode%3D%7BCODE%7D%26format%3D%7BFORMAT%7D%26type%3D%7BTYPE%7D&SCAN_FORMATS=EAN_13">
		{/if}
			<span class="name">{translate text="Scan Barcode"}</span><span class="arrow"></span>
		</a>
  	</li>
  	{if $user}
  	<li class="menu">
  		<a href="{$path}/MyResearch/Home"><span class="name">{translate text="Your Account"}</span><span class="arrow"></span></a>
  	</li>
  	<li class="menu">
  		<a href="{$path}/MyResearch/Logout"><span class="name">{translate text="Logout"}</span><span class="arrow"></span></a>
  	</li>
  	{else}
  	<li class="menu">
  		<a href="{$sessionInitiator}"><span class="name">{translate text="Institutional Login"}</span><span class="arrow"></span></a>
  	</li>
  	{/if}
	{* TODO: Find this graphic -- <img alt="search" src="{$path}/interface/themes/mobile/iWebKit/images/login.png" /> *}
</ul>
