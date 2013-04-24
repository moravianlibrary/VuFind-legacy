<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
      <div class="resulthead">
        <h3>{translate text="Conspectus"}</h3>
      </div>
      <div class="page">
        <ul class="bulleted">
        {foreach from=$categories item=category}
          <li><a href="{$category.url}">{$category.value|escape}</a></li>
        {/foreach}
        </ul>
      </div>
    </div>
  </div>
</div>