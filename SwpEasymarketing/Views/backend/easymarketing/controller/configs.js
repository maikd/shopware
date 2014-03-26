// {namespace name=backend/easymarketing/controller}
// {block name=backend/easymarketing/controller/configs}

Ext.define('Shopware.apps.Easymarketing.controller.Configs', {
	
	extend: 'Ext.app.Controller',

	init: function()
	{
		var me = this;

		me.control({
			'easymarketing-overview': {
				update: me.onUpdateOverview
			},
			'easymarketing-configs': {
				save: me.onSave,
			}
		});

		me.callParent(arguments);
	},

	onSave: function(view)
	{
		if (Ext.LoadMask && view.loadingMessage) {
    		Ext.apply(Ext.LoadMask.prototype, {
        		msg: view.loadingMessage
    		});
		}
		
		view.setLoading(true);
		
		view.getForm().updateRecord(view.configs);
		view.configs.save({
			callback: function(data, operation)
			{
				view.loadRecord(data);
				view.setLoading(false);
				
				if(operation.request.scope.reader.jsonData["message"])
				{
					Shopware.Notification.createGrowlMessage(operation.request.scope.reader.jsonData["message"], operation.request.scope.reader.jsonData["sub_message"]);
				}
			}
		});
	},
	
	onUpdateOverview: function(view)
	{
		view.setLoading(true);
		view.main.configsStore.load({
			callback: function(data, action)
			{
				view.configs = data[0];
				view.loadRecord(data[0]);
				view.setLoading(false);
			}
		});
	}
	
});

// {/block}