{block name='frontend_index_body_inline' append}
{if $EasymarketingConfig.TrackingStatus == 1}
	{if $EasymarketingConfig.ActivateGoogleTracking == 1 && !empty($EasymarketingConfig.GoogleConversionTrackerCode)}
		{$EasymarketingConfig.GoogleConversionTrackerCode}
	{/if}
    {if $EasymarketingConfig.ActivateFacebookTracking == 1 && !empty($EasymarketingConfig.FacebookConversionTrackerCode)}
		{$EasymarketingConfig.FacebookConversionTrackerCode}
	{/if}
{/if}
{/block}

{block name='frontend_checkout_finish_teaser' append}
	{if $EasymarketingConfig.ShowFacebookLikeBadge == 1 && !empty($EasymarketingConfig.FacebookLikeBadgeCode)}
		<div align="center" style="margin:auto; width:99%;">
			{$EasymarketingConfig.FacebookLikeBadgeCode}
     	</div>
    {/if}
{/block}