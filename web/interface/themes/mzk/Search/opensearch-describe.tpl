<?xml version="1.0"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName>{translate text='MZK catalogue search'}</ShortName>
  <Description>{translate text='MZK catalogue search''}</Description>
  <Image height="16" width="16" type="image/png">{$site.url}/favicon.ico</Image>
  <Contact>{$site.email}</Contact>
  <Url type="text/html" method="get" template="{$site.url}/Search/Results?lookfor={literal}{searchTerms}&amp;page={startPage?}{/literal}"/>
  <Url type="application/rss+xml" method="get" template="{$site.url}/Search/Results?lookfor={literal}{searchTerms}{/literal}&amp;view=rss"/>
  <Url type="application/x-suggestions+json" method="get" template="{$site.url}/AJAX/Autocomplete?q={literal}{searchTerms}{/literal}&amp;type=AllFields&amp;format=JSON"/>
</OpenSearchDescription>