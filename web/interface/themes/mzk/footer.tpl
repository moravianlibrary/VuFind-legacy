{* Your footer *}
<div class="search-form-footer">
	<div class="col"><p><strong>{translate text='Search Options'}</strong></p>
	  <ul>
		<li><a href="{$path}/Search/History">{translate text='Search History'}</a></li>
		<li><a href="{$path}/Search/Advanced">{translate text='Advanced Search'}</a></li>
		<li><a href="{$path}/Search/Konspekt#bd">{translate text='Conspectus'}</a></li>
		<li><a href="{$path}/Search/Results?{'filter[]'|escape:url}={$newItemsLink|escape:url}">{translate text='New acquisitions'}</a></li>
		<li><a href="javascript:window.external.AddSearchProvider('{$url}/Search/OpenSearch?method=describe');">{translate text='Add as search engine'}</a></li>
	  </ul>
	</div>
	<div class="col"><p><strong>{translate text='Find More'}</strong></p>
	  <ul>
		<li><a href="{$path}/Browse/Home">{translate text='Browse the Catalog'}</a></li>
		<li><a href="{$path}/AlphaBrowse/Home">{translate text='Browse Alphabetically'}</a></li>
		<li><a href="https://listky.mzk.cz/">{translate text='Card Catalogues'}</a></li>
		{if $userLang == "cz"}
		<li><a href="https://www.mzk.cz/meziknihovni-vypujcni-sluzba">{translate text='Interlibrary loan'}</a></li>
		{/if}
		{if $userLang == "en"}
		<li><a href="http://www.mzk.cz/en/services/borrowing/international-interlibrary-loan-service">{translate text='Interlibrary loan'}</a></li>
		{/if}
	  </ul>
	</div>
	<div class="col last"><p><strong>{translate text='Need Help?'}</strong></p>
	  <ul>
		<li><a href="{$url}/Help/Home?topic=search" onClick="window.open('{$url}/Help/Home?topic=search', 'Help', 'width=625, height=510'); return false;">{translate text='Search Tips'}</a></li>
		<li><a href="http://www.ptejteseknihovny.cz/zadani-dotazu?q=BOA001">{translate text='Ask a Librarian'}</a></li>
		<li><a href="http://www.mzk.cz/faq">{translate text='FAQs'}</a></li>
		<li><a href="https://docs.google.com/spreadsheet/viewform?formkey=dHN5S2pMa0pEYnJyQUtzbWU3Wm9YcWc6MQ&entry_3={$url|escape:url}&entry_4={$user->email|escape:url}&TB_iframe=true&height=600&width=600" class="thickbox">{translate text='Feedback'}</a></li>
	  </ul>
	</div>
</div>
<br clear="all">
{* Comply with Serials Solutions terms of service -- this is intentionally left untranslated. *}
{if $module == "Summon"}Powered by Summon™ from Serials Solutions, a division of ProQuest.{/if}