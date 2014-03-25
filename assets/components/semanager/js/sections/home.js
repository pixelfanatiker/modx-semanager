SEManager.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'semanager-panel-home'
            ,renderTo: 'semanager-panel-home-div'
        }]
    });
    SEManager.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(SEManager.page.Home,MODx.Component);
Ext.reg('semanager-page-home',SEManager.page.Home);