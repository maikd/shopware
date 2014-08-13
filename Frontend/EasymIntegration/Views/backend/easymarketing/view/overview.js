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
	loadingMessage: 'Übersicht wird aktualisiert...',

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
				text: 'Daten erneut abrufen',
				handler: function()
				{
					me.fireEvent('updateEasymarketingData', me);
				}
			}, {
				xtype: 'button',
				cls: 'primary',
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
		this.addEvents('save', 'update', 'updateEasymarketingData');
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
				fieldLabel: '{s name=easymarketing/view/configs/textfield/ActivateGoogleRemarketingCode}Google Remarketing aktiv{/s}',
				name: 'ActivateGoogleRemarketingCode',
				renderer: function(value)
				{
					if (value == 1 && me.configs.get('TrackingStatus') == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/GoogleTrackingStatus}Google Tracking aktiv{/s}',
				name: 'ActivateGoogleTracking',
				renderer: function(value)
				{
					if (value == 1 && me.configs.get('TrackingStatus') == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/FacebookTrackingStatus}Facebook Tracking aktiv{/s}',
				name: 'ActivateFacebookTracking',
				renderer: function(value)
				{
					if (value == 1 && me.configs.get('TrackingStatus') == 1)
					{
						return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
					} else {
						return Ext.String.format('<div style="color:#C00">&#10006;</div>');
					}
				}
			},
			{
				xtype: 'fieldcontainer',
				fieldLabel: 'Google Site Verifikation Status',
				layout: 'hbox',
				items: [{
							xtype: 'displayfield',
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
							xtype: 'button',
							text: 'Google Site Verifikation durchführen / aufheben',
							cls: 'secondary small',
							handler: function()
							{
								if(me.configs.get('GoogleSiteVerificationStatus') == 1)
								{
									var message = 'Möchten Sie die Google Site Verifikation wirklich aufheben?';
								}  else {
									var message = 'Ich stimme zu, dass Google easymarketing bei meiner URL-Verifikation als weiteren "Inhaber" einträgt, damit easymarketing meine Daten für Google Shopping über die Schnittstelle pflegen und auslesen kann. Ich kann diese Zustimmung natürlich jederzeit widerrufen. easymarketing wird meine Daten unter keinen Umständen zu anderen Zwecken als meiner Kampagnen-Steuerung verwenden, weitergeben oder bei sich speichern.';
								}
			
								Ext.Msg.confirm('Google Site Verifikation', message, function(button)
								{
									if (button === 'yes')
									{
										Ext.Ajax.request({
															url: (me.configs.get('GoogleSiteVerificationStatus') == 1) ? '{url action=destroyGoogleSiteVerification}' : '{url action=performGoogleSiteVerification}',
															callback: function(options, success, xhr)
															{
																Shopware.Notification.createGrowlMessage('Aktion ausgeführt', 'Die Änderungen an der Google Site Verifikation wurden durchgeführt.');
																me.fireEvent('update', me);
															}
														});
									}
								});
							}
						}]
			},
			{
				xtype: 'displayfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/RetargetingStatus}Retargeting - AdScale aktiv{/s}',
				name: 'RetargetingAdScaleStatus',
				renderer: function(value)
				{
					if (value == 1)
					{
						if(me.configs.get('RetargetingAdScaleID') != '')
						{
							return Ext.String.format('<div style="color:#3C6">&#10003;</div>');
						} else {
							return Ext.String.format('<div style="color:#C00">keine AdScale-ID hinterlegt</div>');
						}
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
						if(me.configs.get('EasymarketingLastCrawlCategoriesCount') > 0)
						{
							return Ext.String.format('Erster Abruf der Daten ist erfolgt');
						} else {
							return Ext.String.format('Daten wurden bisher noch nicht abgerufen');
						}
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