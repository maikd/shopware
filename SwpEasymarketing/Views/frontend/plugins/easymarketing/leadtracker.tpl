{block name='frontend_index_body_inline' append}
 
{* ----- TRACKING-CODE FOR BASKET IF BASKETAMOUNT > 0 AND CONTACT PAGE ----- *}
{if ($sBasket.content && !$sAmount == "0" && !$sOrderNumber) || $Controller == "forms"}
            
{if $EasymarketingConfig.ActivateGoogleTracking == 1 && $EasymarketingConfig.GoogleTrackingStatus == 1 && !empty($EasymarketingConfig.LeadTrackerCode)}
{$EasymarketingConfig.LeadTrackerCode}
{$EasymarketingConfig.LeadTrackerImg}
{/if}

{/if}
 
{/block}