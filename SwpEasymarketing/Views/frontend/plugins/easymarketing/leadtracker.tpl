{block name='frontend_index_body_inline' append}
 
{* ----- TRACKING-CODE FOR BASKET IF BASKETAMOUNT > 0 AND CONTACT PAGE ----- *}
{if ($sBasket.content && !$sAmount == "0" && !$sOrderNumber) || $Controller == "forms"}

{if $EasymarketingConfig.TrackingStatus == 1}

{if $EasymarketingConfig.ActivateGoogleTracking == 1 && !empty($EasymarketingConfig.GoogleLeadTrackerCode)}
{$EasymarketingConfig.GoogleLeadTrackerCode}
{/if}

{if $EasymarketingConfig.ActivateFacebookTracking == 1 && !empty($EasymarketingConfig.FacebookLeadTrackerCode)}
{$EasymarketingConfig.FacebookLeadTrackerCode}
{/if}

{/if}

{/if}
 
{/block}