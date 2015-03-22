SEManager.grid.Options = function(config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p class="desc">{description}</p>'
        )
    });


    Ext.applyIf(config,{
         id: 'semanager-grid-options'
        ,url: SEManager.config.connectorUrl
        ,baseParams: {
             action: 'options/getoptions'
            ,type: config.type
        }
        ,menuConfig: {
             defaultAlign: 'tl-b?'
            ,enableScrolling: false
            ,cls: 'sm-menu'
        }
        ,save_action: 'options/savefromgrid'
        ,paging: true
        ,remoteSort: true
        ,autoHeight: true
        ,autosave: true

    });
    SEManager.grid.Options.superclass.constructor.call(this,config);

};

Ext.extend(SEManager.grid.Options, MODx.grid.Grid, {

});

Ext.reg('semanager-grid-options', SEManager.grid.Options);