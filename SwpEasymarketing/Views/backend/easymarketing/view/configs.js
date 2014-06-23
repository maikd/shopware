// {namespace name=backend/easymarketing/view}
// {block name=backend/easymarketing/view/configs}

Ext.define('Shopware.apps.Easymarketing.view.Configs', {
	
	extend: 'Ext.form.Panel',
	alias: 'widget.easymarketing-configs',
	layout: 'anchor',
	cls: 'shopware-form',
	border: false,
	title: '{s name=easymarketing/view/config/title}Einstellungen{/s}',
	isBuiltView: false,
	autoScroll: true,
	loadingMessage: 'Die Installation und Konfiguration wird durchgeführt...',

	defaults: {
		anchor: '100%',
		margin: 10
	},

	initComponent: function()
	{
		var me = this;
		me.dockedItems = [me.createToolbarBottom()];
		me.registerEvents();
		me.callParent(arguments);
	},

	registerEvents: function()
	{
		this.addEvents('save');
	},

	buildView: function()
	{
		var me = this;

		if (!me.isBuiltView)
		{
			me.add(me.getWidgetData());
			me.isBuiltView = true;
		}

		me.loadRecord(me.configs);
	},

	createToolbarBottom: function()
	{
		var me = this;

		return Ext.create('Ext.toolbar.Toolbar', {
			cls: 'shopware-toolbar',
			dock: 'bottom',
			ui: 'shopware-ui',
			items: ['->', me.createSaveButton()]
		});
	},

	createSaveButton: function()
	{
		var me = this;

		return Ext.create('Ext.button.Button', {
			text: '{s name=easymarketing/view/configs/button/save}Speichern{/s}',
			cls: 'primary',
			handler: function()
			{
				me.fireEvent('save', me);
			}
		})
	},

	getWidgetData: function()
	{
		var me = this;
		
		var categories = Ext.create('Ext.data.Store', {
            fields: ["id","name"],
            proxy: {
                 type: "ajax" ,
                 autoload: true,
                 api: {
                     read: "{url action=getCategories}",
                 },
                 reader: {
                     type: "json" ,
                     root: "data"
                 }
            }
        });

		return [{
			xtype: 'fieldset',
			title: 'API - Zugangsdaten',
			layout: 'anchor',
			defaults: {
				labelWidth: 140,
				anchor: '100%'
            },
			items: [{
				xtype: 'textfield',
				fieldLabel: '{s name=easymarketing/view/configs/textfield/APIToken}API Token{/s}',
				supportText: 'Tragen Sie hier den API Key ein, den Sie im Händlerbereich unter »Meine Daten > API«  finden.',
				name: 'APIToken',
				allowBlank: false
            }]
		}, {
			xtype: 'fieldset',
			title: 'Weitere Einstellungen',
			layout: 'anchor',
			defaults: {
				labelWidth: 150,
				xtype: 'combo',
				valueField: 'id',
				displayField: 'name',
				queryMode: 'local',
				anchor: '100%',
				allowBlank: false,
			},
			items: [{
				fieldLabel: '{s name=easymarketing/view/configs/textfield/RootCategory}Root-Kategorie{/s}',
				name: 'RootCategoryID',
				store: categories.load(),
				supportText: 'Es werden nur Daten an Easymarketing übermittelt, welche unterhalb der ausgewählten Kategorie liegen.'
			}, {
				fieldLabel: '{s name=easymarketing/view/configs/textfield/ActivateGoogleTracking}Google Tracking aktivieren{/s}',
				name: 'ActivateGoogleTracking',
				store: new Ext.data.ArrayStore({
					fields: ['id', 'name'],
					data: [[1, 'ja'], [0, 'nein']]
				}),
				supportText: 'Ist dies aktiviert, so werden die Google Trackingpixel im Webshop implementiert.'
			}, {
				fieldLabel: '{s name=easymarketing/view/configs/textfield/ActivateFacebookTracking}Facebook Tracking aktivieren{/s}',
				name: 'ActivateFacebookTracking',
				store: new Ext.data.ArrayStore({
					fields: ['id', 'name'],
					data: [[1, 'ja'], [0, 'nein']]
				}),
				supportText: 'Ist dies aktiviert, so werden die Facebook Trackingpixel im Webshop implementiert.'
			}, {
				fieldLabel: '{s name=easymarketing/view/configs/textfield/ShowFacebookLikeBadge}Zeige Facebook Like Button{/s}',
				name: 'ShowFacebookLikeBadge',
				store: new Ext.data.ArrayStore({
					fields: ['id', 'name'],
					data: [[1, 'ja'], [0, 'nein']]
				}),
				supportText: 'Ist dies aktiviert, so wird auf der Bestellbestätigungsseite der Facebook Like Button angezeigt.'
			}]
		}, {
			xtype: 'fieldset',
			title: 'Retargeting',
			layout: 'anchor',
			defaults: {
				labelWidth: 150,
				xtype: 'combo',
				valueField: 'id',
				displayField: 'name',
				queryMode: 'local',
				anchor: '100%',
				allowBlank: false,
			},
			items: [{
				fieldLabel: '{s name=easymarketing/view/configs/textfield/RetargetingAdScaleStatus}AdScale aktivieren{/s}',
				name: 'RetargetingAdScaleStatus',
				store: new Ext.data.ArrayStore({
					fields: ['id', 'name'],
					data: [[1, 'ja'], [0, 'nein']]
				}),
				supportText: 'Ist dies aktiviert, so wird der Retargeting Anbieter AdScale verwendet.'
			}]
		}]
	}
	
});

// {/block}