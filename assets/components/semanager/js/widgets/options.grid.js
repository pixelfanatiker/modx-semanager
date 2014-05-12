SEManager.grid.Options = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'semanager-tab-options'
        ,url: SEManager.config.connectorUrl
        ,baseParams: { action: '/options/getOptions' }
        ,fields: ['key','value']
        ,paging: true
        ,remoteSort: true
        ,autoHeight: true
        //,save_action: '/options/getOptions'
        ,autosave: true
        ,columns: [{
            header: _('title')
            ,dataIndex: 'key'
            ,width: 100
            ,editor: {xtype: 'textfield', allowBlank: false}
        },{
            header: _('value')
            ,dataIndex: 'value'
            ,width: 100
            ,editor: {xtype: 'textfield', allowBlank: false}
        }]

    })
    SEManager.grid.Options.superclass.constructor.call(this,config)
};

Ext.extend(SEManager.grid.Options,MODx.grid.Grid);
Ext.reg('semanager-tab-options',SEManager.grid.Options);