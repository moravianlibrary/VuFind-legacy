<ul class="pageitem">
	<li class="menuAutoHeight">
		<div class="error">{$type}: {translate text="Not supported type"}</div>
		<div class="message">{translate text="Scan a book please"}</div>
	</li>
</ul>
<ul class="pageitem">
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
</ul> 

{* TODO: Should we display basic menu? *}
{*include file="Search/home.tpl"*}