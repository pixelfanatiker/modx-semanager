SEManager.grid.Elements = function(config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p class="desc">{description}</p>'
        )
    });

    if (!config.tbar) {
        config.tbar = [{
            text: _('quick_create_'+config.type)
            ,handler: {
                xtype: 'modx-window-quick-create-'+config.type
                ,blankValues: true
            }
        }];
    }
    config.tbar.push('->',{
        xtype: 'modx-combo'
        ,name: 'filter_category'
        ,id: 'semanager-filter-category'+config.type
        ,emptyText: _('semanager.elements.filter_by_category')
        ,fields: ['id','category']
        ,displayField: 'category'
        ,valueField: 'id'
        ,width: 250
        ,pageSize: 10
        ,url: SEManager.config.connectorUrl
        ,baseParams: {
            action: 'elements/getcategorylist'
            ,type: config.type
        }
        ,listeners: {
            'select': {fn: this.filterByCategory, scope: this}
        }
    },'-',{
        xtype: 'textfield'
        ,name: 'filter_name'
        ,id: 'semanager-filter-name-'+config.type
        ,emptyText: _('semanager.elements.filter_by_name')+'...'
        ,listeners: {
            'change': {fn: this.filterByName, scope: this}
            ,'render': {fn: function(cmp) {
                new Ext.KeyMap(cmp.getEl(), {
                    key: Ext.EventObject.ENTER
                    ,fn: this.blur
                    ,scope: cmp
                });
            },scope:this}
        }
    },{
        xtype: 'button'
        ,id: 'semanager-filter-clear-'+config.type
        ,text: _('filter_clear')
        ,handler: this.clearFilter
    });

    /*
    var ec = new Ext.ux.grid.CheckColumn({
        header: _('semanager.elements.static')
        ,dataIndex: 'static'
        ,editable: false
        ,width: 20
        ,sortable: true
    });
    */

    this.cm = new Ext.grid.ColumnModel({
        columns: [this.exp,{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 15
        },{
            header: _('name')
            ,dataIndex: (config.type=='template')?'templatename':'name'
            ,width: 50
            ,sortable: true
            ,sortDir: 'ASC'
        },{
            header: _('semanager.elements.file')
            ,dataIndex: 'static_file'
            ,sortable: false
            ,editable: false
        },{
            header: _('semanager.elements.static')
            ,dataIndex: 'static'
            ,width: 30
            ,sortable: true
            ,editable: true
            ,renderer: this.renderDynField.createDelegate(this,[this],true)
        }]
        ,tools: [{
            id: 'plus'
            ,qtip: _('expand_all')
            ,handler: this.expandAll
            ,scope: this
        },{
            id: 'minus'
            ,hidden: true
            ,qtip: _('collapse_all')
            ,handler: this.collapseAll
            ,scope: this
        }]
        /* Editors are pushed here. I think that they should be in general grid
         * definitions (modx.grid.js) and activated via a config property (loadEditor: true) */
        ,getCellEditor: function(colIndex, rowIndex) {
            var field = this.getDataIndex(colIndex);
            if (field == 'static') {
                //var rec = config.store.getAt(rowIndex);
                var o = MODx.load({
                    xtype: 'combo-boolean'
                });
                return new Ext.grid.GridEditor(o);
            }
            return Ext.grid.ColumnModel.prototype.getCellEditor.call(this, colIndex, rowIndex);
        }

    });

    Ext.applyIf(config,{
        cm: this.cm
        ,fields: ['id','name','static','static_file', 'description','category','snippet','plugincode','templatename','content','disabled']
        ,id: 'semanager-grid-elements-' + config.type + 's'
        ,url: SEManager.config.connectorUrl
        ,baseParams: {
            action: 'elements/getlist'
            ,type: config.type
        }
        ,clicksToEdit: 2
        ,autosave: true
        ,save_action: 'elements/updatefromgrid'
        ,plugins: this.exp
        ,autoHeight: true
        ,paging: true
        //,remoteSort: true
        ,remoteSort: false
        ,listeners: {
            'afterAutoSave': {fn:function() {
                this.refresh();
            },scope:this}
            ,'afterEdit': {fn:function(e) {
                e.record.data.type = config.type;
            }}
        }
    });
    SEManager.grid.Elements.superclass.constructor.call(this, config);
};
Ext.extend(SEManager.grid.Elements, MODx.grid.Grid, {

    renderDynField: function(v,md,rec,ri,ci,s,g) {
        var r = s.getAt(ri).data;
        var f,idx;
        var oz = v;
        var xtype = this.config.dynProperty;
        if (!r[xtype] || r[xtype] == 'combo-boolean') {
            f = MODx.grid.Grid.prototype.rendYesNo;
            oz = f(v == 1,md);
        } else if (r[xtype] === 'datefield') {
            f = Ext.util.Format.dateRenderer('Y-m-d');
            oz = f(v);
        } else if (r[xtype] === 'password') {
            f = this.rendPassword;
            oz = f(v,md);
        } else if (r[xtype].substr(0,5) == 'combo' || r[xtype] == 'list' || r[xtype].substr(0,9) == 'modx-combo') {
            var cm = g.getColumnModel();
            var ed = cm.getCellEditor(ci,ri);
            var cb;
            if (!ed) {
                r.xtype = r.xtype || 'combo-boolean';
                cb = this.createCombo(r);
                ed = new Ext.grid.GridEditor(cb);
                cm.setEditor(ci,ed);
            } else if (ed && ed.field && ed.field.xtype == 'modx-combo') {
                cb = ed.field;
            }
            if (r[xtype] != 'list') {
                f = Ext.util.Format.comboRenderer(ed.field);
                oz = f(v);
            } else if (cb) {
                idx = cb.getStore().find(cb.valueField,v);
                rec = cb.getStore().getAt(idx);
                if (rec) {
                    oz = rec.get(cb.displayField);
                } else {
                    oz = v;
                }
            }
        }
        return Ext.util.Format.htmlEncode(oz);
    }

    ,onDirty: function(){
        console.log(this.config.panel);

        if (this.config.panel) {
            Ext.getCmp(this.config.panel).fireEvent('fieldChange');
        }
    }
    ,filterByCategory: function(category, selected){
        this.getStore().baseParams.categoryfilter = selected.id;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterByName: function(tf, newValue) {
        this.getStore().baseParams.namefilter = newValue || tf;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,clearFilter: function() {
        this.getStore().baseParams = {
            action: 'elements/getlist'
            ,type: this.config.type
        };
        Ext.getCmp('semanager-filter-category'+this.config.type).reset();
        Ext.getCmp('semanager-filter-name-'+this.config.type).reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('quick_update_' + this.config.type)
            ,handler: this.updateElement
        });
        this.addContextMenuItem(m);
    }
    ,updateElement: function(btn,e){
        var r = this.menu.record;
        r.clearCache = 1;
        var que = MODx.load({
            xtype: 'modx-window-quick-update-' + this.config.type
            ,record: r
            ,grid: this
            ,listeners: {
                'success' : {fn:function(){
                    this.refresh();
                },scope:this}
            }
        });
        que.reset();
        que.setValues(r);
        que.show(e.target);
    }
});

Ext.reg('semanager-grid-elements-chunks', SEManager.grid.Elements);
Ext.reg('semanager-grid-elements-plugins', SEManager.grid.Elements);
Ext.reg('semanager-grid-elements-snippets', SEManager.grid.Elements);
Ext.reg('semanager-grid-elements-templates', SEManager.grid.Elements);