// {namespace name=backend/easymarketing/view}
// {block name=backend/easymarketing/view/overview}

Ext.define('Shopware.apps.Easymarketing.view.Overview', {
	
	extend: 'Ext.form.Panel',
	alias: 'widget.easymarketing-overview',
	layout: 'anchor',
	title: '{s name=easymarketing/view/overview/title}Übersicht{/s}',
	layout: 'anchor',
	cls: 'shopware-form',
	border: false,
	isBuiltView: false,
	autoScroll: true,

	defaults: {
		anchor: '100%',
		labelWidth: '40%',
		margin: 10
	},

	initComponent: function()
	{
		var me = this;

		me.dockedItems = [{
			xtype: 'toolbar',
			cls: 'shopware-toolbar',
			dock: 'bottom',
			ui: 'shopware-ui',
			items: ['->', {
				xtype: 'button',
				cls: 'secondary',
				text: 'Übersicht aktualisieren',
				handler: function()
				{
					me.fireEvent('update', me);
				}
			}]
		}];

		me.registerEvents();
		me.callParent(arguments);
	},

	init: function()
	{
		var me = this;
		if (me.isBuiltView)
		{
			me.fireEvent('update', me)
		}
		else
		{
			me.buildView();
		}
	},

	registerEvents: function()
	{
		this.addEvents('save', 'update');
	},

	buildView: function()
	{
		var me = this;
		me.add([

		{
			xtype: 'fieldset',
			title: 'Setup',
			defaults: {
				anchor: '100%',
				labelWidth: '33%'
			},
			items: [{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/APIStatus}API Status{/s}',
				name: 'APIStatus',
				renderer: function(value)
				{
					if (value == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/ConfigureEndpointsStatus}Endpunkte an Easymarketing übermittelt{/s}',
				name: 'ConfigureEndpointsStatus',
				renderer: function(value)
				{
					if (value == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/GoogleConversionTrackerStatus}Conversion Tracker eingebaut{/s}',
				name: 'GoogleConversionTrackerStatus',
				renderer: function(value)
				{
					if (value == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/GoogleSiteVerificationStatus}Google Site Verifikation Status{/s}',
				name: 'GoogleSiteVerificationStatus',
				renderer: function(value)
				{
					if (value == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/RetargetingStatus}Retargeting - AdScale aktiv{/s}',
				name: 'RetargetingAdScaleID',
				renderer: function(value)
				{
					if (value > 0)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			}]
		}, {
		xtype: 'fieldset',
			title: 'Letzter Abruf von Easymarketing',
			defaults: {
				anchor: '100%',
				labelWidth: '33%'
			},
			items: [{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/EasymarketingLastCrawlDate}Letzter Abruf{/s}',
				name: 'EasymarketingLastCrawlDate',
				renderer: function(value)
				{
					if(value == '')
					{
						return Ext.String.format('Daten wurden bisher noch nicht abgerufen');
					} else {
						return Ext.String.format(Ext.util.Format.date(me.configs.get('EasymarketingLastCrawlDate'), 'd.m.Y - H:i:s') + ' Uhr');
					}
					
					return value;
				}
			}, {
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/EasymarketingLastCrawlCategoriesCount}Kategorien indexiert{/s}',
				name: 'EasymarketingLastCrawlCategoriesCount',
				renderer: function(value)
				{
					return value;
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/EasymarketingLastCrawlProductsCount}Produkte indexiert{/s}',
				name: 'EasymarketingLastCrawlProductsCount',
				renderer: function(value)
				{
					return value;
				}
			}]
		}
		
		]);

		me.loadRecord(me.configs);
		me.isBuiltView = true;
	}
	
});

// {/block}