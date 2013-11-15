{if !empty($regexRecommendRecommendations)}
  <p>
    <ul>
    {foreach from=$regexRecommendRecommendations key=type item=url}
      <li>
        <a href="{$url}">{$type|cat:"_recommendation_text"|translate}</a>
      </li>
    {/foreach}
    </ul>
  <p>
{/if}