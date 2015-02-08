SEManager.panel.CommonTab = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'semanager-tab-common'
        ,bodyCssClass: 'panel-desc'
        ,border: false
        ,html: '<p>'+_('semanager.description')+'</p>'

    });
    SEManager.panel.CommonTab.superclass.constructor.call(this,config);
};

Ext.extend(SEManager.panel.CommonTab,MODx.Panel);
Ext.reg('semanager-tab-common',SEManager.panel.CommonTab);
