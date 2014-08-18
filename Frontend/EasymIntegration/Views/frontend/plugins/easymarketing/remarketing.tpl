{block name='frontend_index_body_inline' append}
        {* ----- TRACKING-CODE FOR HOME PAGE ----- *}
    {if $Controller == "index"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$GoogleRemarketingAdvancedTags = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'home'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}
        {/if}

        {* ----- TRACKING-CODE FOR ARTICLE DETAILPAGE ----- *}
    {elseif  $Controller == "detail"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$price = $sArticle.price|replace:",":"."}
            {* replace required values *}
            {$googleRC = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: '`$sArticle.ordernumber`'"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'detail'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: '`$price` '"}
            {* add additional values*}
            {$googleRC = $googleRC|replace:"};":"ecomm_pvalue: '`$price`',};'"}
            {$GoogleRemarketingAdvancedTags = $googleRC|replace:"};":"ecomm_category: '`$sCategoryInfo.name`',};"}
        {/if}

        {* ----- TRACKING-CODE FOR LISTING ----- *}
    {elseif $Controller == "listing"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {* replace required values *}
            {$googleRC = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'category'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}}
            {* add additional values*}
            {$GoogleRemarketingAdvancedTags = $googleRC|replace:"};":"ecomm_category: '`$sCategoryInfo.name`',};"}
        {/if}
        {* ----- TRACKING-CODE FOR CHECKOUT ----- *}
    {elseif $Controller == "checkout"}
        {*var_dump($sTargetAction)*}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {if $sTargetAction == "cart"}
                {* replace required values *}
                {$googleRC = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: `$ecomm_prodid`"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'cart'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: '`$sBasket.sAmount`'"}
                {* add additional values*}
                {$GoogleRemarketingAdvancedTags = $googleRC|replace:"};":"ecomm_quantity: `$ecomm_quantity`,};"}
            {else}{* this is the purchase case*}
                {* replace required values *}
                {$googleRC = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: `$ecomm_prodid`"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'purchase'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: '`$sBasket.sAmount`'"}
                {* add additional values*}
                {$GoogleRemarketingAdvancedTags = $googleRC|replace:"};":"ecomm_quantity: `$ecomm_quantity`,};"}
            {/if}

        {/if}
        {* ----- TRACKING-CODE FOR SEARCH ----- *}
    {elseif $Controller == "search"}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$GoogleRemarketingAdvancedTags = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'searchresults'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}
        {/if}
        {* ----- TRACKING-CODE FOR ALL OTHER PAGES ----- *}
    {else}
        {if $EasymarketingConfig.ActivateGoogleRemarketingCode == 1 && !empty($EasymarketingConfig.GoogleRemarketingCode)}
            {$GoogleRemarketingAdvancedTags = $EasymarketingConfig.GoogleRemarketingCode|replace:"ecomm_prodid: 'REPLACE_WITH_VALUE'":"ecomm_prodid: ''"|replace:"ecomm_pagetype: 'REPLACE_WITH_VALUE'":"ecomm_pagetype: 'other'"|replace:"ecomm_totalvalue: 'REPLACE_WITH_VALUE'":"ecomm_totalvalue: ''"}
        {/if}
    {/if}
    {* ----- INSERT GOOGLE REMARKETING ADVANCED TAGGING PARAMS FOR ALL PAGES----- *}
    {if $hasaccount == "y"}
        {$GoogleRemarketingAdvancedTags|replace:"};":"hasaccount: 'y',};"}
    {else}
        {$GoogleRemarketingAdvancedTags}
    {/if}
{/block}