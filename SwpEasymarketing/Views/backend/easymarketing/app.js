// {namespace name=backend/easymarketing}
// {block name=backend/easymarketing/app}

Ext.define('Shopware.apps.Easymarketing', {
	
	name: 'Shopware.apps.Easymarketing',
	extend: 'Enlight.app.SubApplication',
	loadPath: '{url action=load}',
	bulkLoad: true,

	controllers: ['Main', 'Configs'],
	views: ['Window', 'Overview', 'Configs'],
	stores: ['Configs'],
	models: ['Configs'],

	launch: function()
	{
		var me = this, mainController = me.getController('Main');
		return mainController.mainWindow;
	}
	
});

// {/block}