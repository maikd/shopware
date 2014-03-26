// {namespace name=backend/easymarketing/store}
// {block name=backend/easymarketing/store/configs}

Ext.define('Shopware.apps.Easymarketing.store.Configs', {
	
	extend: 'Ext.data.Store',
	storeId: 'easymarketing-store-configs',
	model: 'Shopware.apps.Easymarketing.model.Configs',
	batch: true,
	autoLoad: false,
	
	proxy: {
		type: 'ajax',
		api: {
			read: '{url action=getConfigs}',
			save: '{url action=saveConfigs}'
		},
		reader: {
			type: 'json',
			root: 'data'
		}
	}
	
});

// {/block}