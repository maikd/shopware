// {namespace name=backend/easymarketing/model}
// {block name=backend/easymarketing/model/configs}

Ext.define('Shopware.apps.Easymarketing.model.Configs', {
	
	extend: 'Ext.data.Model',

	fields: [{
			name: 'APIToken',
			type: 'string'
		}, {
			name: 'RootCategoryID',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'ShowFacebookLikeBadge',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'RetargetingAdScaleStatus',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'APIStatus',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'ConfigureEndpointsStatus',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'GoogleConversionTrackerStatus',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'LeadTrackerStatus',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'GoogleSiteVerificationStatus',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'RetargetingAdScaleID',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'EasymarketingFirstCrawlData',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'EasymarketingLastCrawlDate',
			type: 'date',
			dateFormat: 'timestamp'
		}, {
			name: 'EasymarketingLastCrawlProductsCount',
			type: 'integer',
			defaultValue: 0
		}, {
			name: 'EasymarketingLastCrawlCategoriesCount',
			type: 'integer',
			defaultValue: 0
	}],

	proxy: {
		type: 'ajax',

        api: {
         	read: '{url action=readConfigs}',
            update: '{url action=saveConfigs}'
        },

		reader: {
			type: 'json',
			root: 'data'
		}
	}
	
});

// {/block}