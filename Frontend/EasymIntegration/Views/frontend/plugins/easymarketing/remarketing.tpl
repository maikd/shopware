{block name='frontend_index_body_inline' append}
        {* ----- TRACKING-CODE FOR HOME PAGE ----- *}
    {if $Controller == "index"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'home'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}
        {/if}

        {* ----- TRACKING-CODE FOR ARTICLE DETAILPAGE ----- *}
    {elseif  $Controller == "detail"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$price = $sArticle.price|replace:",":"."}
            {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: '`$sArticle.ordernumber`'"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'detail'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: '`$price` '"}
        {/if}

        {* ----- TRACKING-CODE FOR LISTING ----- *}
    {elseif $Controller == "listing"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'category'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}}
        {/if}
        {* ----- TRACKING-CODE FOR CHECKOUT ----- *}
    {elseif $Controller == "checkout"}
        {*var_dump($sTargetAction)*}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {if $sTargetAction == "cart"}
                {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: `$ecomm_prodid`"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'cart'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: '`$sBasket.sAmount`'"}
            {else}
                {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: `$ecomm_prodid`"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'purchase'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: '`$sBasket.sAmount`'"}
            {/if}

        {/if}
        {* ----- TRACKING-CODE FOR SEARCH ----- *}
    {elseif $Controller == "search"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'searchresults'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}
        {/if}
        {* ----- TRACKING-CODE FOR ALL OTHER PAGES ----- *}
    {else}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'other'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}
        {/if}
    {/if}
{/block}