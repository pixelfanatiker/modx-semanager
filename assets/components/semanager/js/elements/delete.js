SEManager.page.UpdateProduct = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        panelXType: 'semanager-delete-element'
    });
    config.canDuplicate = false;
    config.canDelete = false;
    SEManager.page.UpdateProduct.superclass.constructor.call(this,config);
};
