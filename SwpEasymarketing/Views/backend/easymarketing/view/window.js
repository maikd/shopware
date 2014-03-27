// {namespace name=backend/easymarketing/view}
// {block name=backend/easymarketing/view/window}

Ext.define('Shopware.apps.Easymarketing.view.Window', {
	
	extend: 'Enlight.app.Window',
	alias: 'widget.easymarketing-window',
	stateId: 'easymarketing-window',
	stateful: true,
	layout: 'fit',
	width: 650,
	height: 550,
	autoShow: true,
	title: '{s name=easymarketing/view/window/title}Easymarketing{/s}',
	iconCls: 'easymarketing-icon',

	initComponent: function()
	{
		var me = this;
		me.callParent(arguments);
	},

	createTabPanel: function()
	{
		var me = this;

		me.overview = Ext.widget('easymarketing-overview', {
			configs: me.configs,
			main: me
		});
		me.overview.on('activate', me.overview.init);
		
		me.config = Ext.widget('easymarketing-configs', {
			configs: me.configs,
			main: me
		});
		me.config.on('activate', me.config.buildView);

		me.tabpanel = Ext.create('Ext.tab.Panel', {
			items: [me.overview, me.config]
		});

		me.add(me.tabpanel);
	}
	
});

// {/block}