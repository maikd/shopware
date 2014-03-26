// {namespace name=backend/easymarketing/controller}
// {block name=backend/easymarketing/controller/main}

Ext.define('Shopware.apps.Easymarketing.controller.Main', {
	
	extend: 'Ext.app.Controller',
	mainWindow: null,

	init: function()
	{
		var me = this;

		var store = me.subApplication.getStore('Configs');

		me.mainWindow = me.subApplication.getView('Window').create().show();
		me.mainWindow.setLoading(true);

		store.load({
			callback: function(data)
			{
				var configs = data[0];
				
				me.mainWindow.configsStore = store;
				me.mainWindow.configs = configs;
				
				me.mainWindow.setLoading(false);
				me.mainWindow.createTabPanel();
				me.subApplication.setAppWindow(me.mainWindow);
			}
		});

		me.callParent(arguments);
	}
	
});

// {/block}