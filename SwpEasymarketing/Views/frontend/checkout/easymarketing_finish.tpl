{block name='frontend_index_body_inline' append}
	{if $EasymarketingConfig.ActivateGoogleTracking == 1 && $EasymarketingConfig.GoogleTrackingStatus == 1}
		{$EasymarketingConfig.GoogleConversionTrackerCode}
		{$EasymarketingConfig.GoogleConversionTrackerImg}
	{/if}
{/block}

{block name='frontend_checkout_finish_teaser' append}
	{if $EasymarketingConfig.ShowFacebookLikeBadge == 1 && $EasymarketingConfig.FacebookLikeBadgeCode != ''}
		<div align="center" style="margin:auto; width:99%;">
			{$EasymarketingConfig.FacebookLikeBadgeCode}
     	</div>
    {/if}
{/block}