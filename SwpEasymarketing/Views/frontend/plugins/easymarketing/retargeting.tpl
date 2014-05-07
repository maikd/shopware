{block name='frontend_index_body_inline' append}

{* ----- TRACKING-CODE FOR HOME PAGE ----- *}
{if $Controller == "index"}

{if $EasymarketingConfig.RetargetingAdScaleStatus == 1 && !empty($EasymarketingConfig.RetargetingAdScaleID)}
<script type="text/javascript">
{literal}
	window.adscaleProductViews = window.adscaleProductViews ? window.adscaleProductViews : []; 
	window.adscaleProductViews.push({
		"aid":"{/literal}{$EasymarketingConfig.RetargetingAdScaleID}{literal}",
		"productIds": [""],
		"categoryIds": [""],
		"pageTypes": ["homepage"],
}); 
{/literal}
</script>
<script type="text/javascript" src="//js.adscale.de/pbr-a.js"></script>

<!-- adscale pixel -->
<ins style="display: none;" class="adscale-rt" data-accountId="{$EasymarketingConfig.RetargetingAdScaleID}" data-pixelName="Homepage"></ins> 
<script async defer type="text/javascript" src="//js.adscale.de/rt-a.js"></script> >
{/if}

{/if}

{* ----- TRACKING-CODE FOR ARTICLE DETAILPAGE ----- *}
{if $Controller == "detail"}

{if $EasymarketingConfig.RetargetingAdScaleStatus == 1 && !empty($EasymarketingConfig.RetargetingAdScaleID)}
<script type="text/javascript">
{literal}
	window.adscaleProductViews = window.adscaleProductViews ? window.adscaleProductViews : []; 
	window.adscaleProductViews.push({
		"aid":"{/literal}{$EasymarketingConfig.RetargetingAdScaleID}{literal}",
		"productIds": ["{/literal}{$sArticle.articleID|escape:"javascript"}{literal}"],
		"categoryIds": [{/literal}{foreach name=breadcrumb from=$sBreadcrumb item=breadcrumb}{if $smarty.foreach.breadcrumb.iteration < $smarty.foreach.breadcrumb.total}"{$breadcrumb.name}"{if ($smarty.foreach.breadcrumb.total - $smarty.foreach.breadcrumb.iteration) >= 2},{/if}{/if}{/foreach}{literal}],
		"pageTypes": ["products"],
}); 
{/literal}
</script>
<script type="text/javascript" src="//js.adscale.de/pbr-a.js"></script>

<!-- adscale pixel -->
<ins style="display: none;" class="adscale-rt" data-accountId="{$EasymarketingConfig.RetargetingAdScaleID}" data-pixelName="Product"></ins> 
<script async defer type="text/javascript" src="//js.adscale.de/rt-a.js"></script>>
{/if}

{/if}
 
{* ----- TRACKING-CODE FOR CATEGORY LISTING ----- *}
{if $Controller == "listing"}

{if $EasymarketingConfig.RetargetingAdScaleStatus == 1 && !empty($EasymarketingConfig.RetargetingAdScaleID)}
<script type="text/javascript">
{literal}
	window.adscaleProductViews = window.adscaleProductViews ? window.adscaleProductViews : []; 
	window.adscaleProductViews.push({
		"aid":"{/literal}{$EasymarketingConfig.RetargetingAdScaleID}{literal}",
		"productIds": [""],
		"categoryIds": [{/literal}{foreach name=breadcrumb from=$sBreadcrumb item=breadcrumb}"{$breadcrumb.name}"{if !$smarty.foreach.breadcrumb.last},{/if}{/foreach}{literal}],
		"pageTypes": ["categories"],
}); 
{/literal}
</script>
<script type="text/javascript" src="//js.adscale.de/pbr-a.js"></script>

<!-- adscale pixel -->
<ins style="display: none;" class="adscale-rt" data-accountId="{$EasymarketingConfig.RetargetingAdScaleID}" data-pixelName="Category"></ins> 
<script async defer type="text/javascript" src="//js.adscale.de/rt-a.js"></script>
{/if}

{/if}
 
{* ----- TRACKING-CODE FOR BASKET IF BASKETAMOUNT > 0 ----- *}
{if $sBasket.content && !$sAmount == "0" && !$sOrderNumber}
            
{if $EasymarketingConfig.RetargetingAdScaleStatus == 1 && !empty($EasymarketingConfig.RetargetingAdScaleID)}
<script type="text/javascript">
{literal}
	window.adscaleProductViews = window.adscaleProductViews ? window.adscaleProductViews : []; 
	window.adscaleProductViews.push({
		"aid":"{/literal}{$EasymarketingConfig.RetargetingAdScaleID}{literal}",
		"productIds": [{/literal}{foreach name=basket from=$sBasket.content item=sBasketItem key=key}"{$sBasketItem.articleID|escape:"javascript"}"{if !$smarty.foreach.basket.last},{/if}{/foreach}{literal}],
		"categoryIds": [""],
		"pageTypes": ["basket"],
}); 
{/literal}
</script>
<script type="text/javascript" src="//js.adscale.de/pbr-a.js"></script>

<!-- adscale pixel -->
<ins style="display: none;" class="adscale-rt" data-accountId="{$EasymarketingConfig.RetargetingAdScaleID}" data-pixelName="Basket"></ins> 
<script async defer type="text/javascript" src="//js.adscale.de/rt-a.js"></script>
{/if}

{/if}
 
{* ----- TRACKING-CODE FOR CHECKOUT SUCCESS ----- *}
{if $Controller == "checkout" && $sOrderNumber}

{if $EasymarketingConfig.RetargetingAdScaleStatus == 1 && !empty($EasymarketingConfig.RetargetingAdScaleID)}
<script type="text/javascript">
{literal}
	window.adscaleProductViews = window.adscaleProductViews ? window.adscaleProductViews : []; 
	window.adscaleProductViews.push({
		"aid":"{/literal}{$EasymarketingConfig.RetargetingAdScaleID}{literal}",
		"productIds": [{/literal}{foreach name=basket from=$sBasket.content item=sBasketItem key=key}"{$sBasketItem.articleID|escape:"javascript"}"{if !$smarty.foreach.basket.last},{/if}{/foreach}{literal}],
		"categoryIds": [""],
		"pageTypes": ["transactions"],
}); 
{/literal}
</script>
<script type="text/javascript" src="//js.adscale.de/pbr-a.js"></script>

<!-- adscale pixel -->
<ins style="display: none;" class="adscale-rt" data-accountId="{$EasymarketingConfig.RetargetingAdScaleID}" data-pixelName="Transactions"></ins> 
<script async defer type="text/javascript" src="//js.adscale.de/rt-a.js"></script>

<!-- adscale conversion tracking -->
<ins style="display: none;" class="adscale-cpx" data-accountId="{$EasymarketingConfig.RetargetingAdScaleConversionID}" data-pixelName="1"></ins> 
<script async defer type="text/javascript" src="//js.adscale.de/cpx-a.js"></script>
{/if}

{/if}
 
{/block}