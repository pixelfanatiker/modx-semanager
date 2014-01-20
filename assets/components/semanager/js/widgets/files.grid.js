SEManager.grid.Files = function(config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p class="desc">{description}</p>'
        )
    });

    if (!config.tbar) {
        config.tbar = [{
            xtype: 'button'
            ,text: 'Создать элементы из файлов'
            ,icon: MODx.config.template_url + 'images/restyle/icons/elements.png'
            ,cls:'x-btn-text-icon'
            ,style: {
                paddingLeft: '5px'
                ,float: 'left'
                ,marginRight: '20px'
            }
            ,listeners: {
                click: function(){
                    Ext.Ajax.request({
                        url: SEManager.config.connectorUrl
                        ,success: function(response) {
                           // this.refresh();
                            //SEManager.grid.Files.refresh();
                            //console.log(this.cm);
                            //p.setValue(response.responseText);
                            //p.enable();
                        }
                        ,params: {
                            action: 'files/newelem.class'
                        }
                    });
                }
            }
            ,handler: {
                xtype: 'modx-window-quick-createfrom-'+config.type
                ,blankValues: true
            }
        }];
    }
    config.tbar.push('->',{
        xtype: 'modx-combo'
        ,name: 'filter_category'
        ,id: 'semanager-filter-category-files'
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
        ,id: 'semanager-filter-name-files'
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
        ,id: 'semanager-filter-clear-files'
        ,text: _('filter_clear')
        ,handler: this.clearFilter
    });

    this.cm = new Ext.grid.ColumnModel({
        columns: [this.exp,{
            header: _('name')
            ,dataIndex: 'filename'
            ,width: 30
            ,sortable: true
        },{
            header: _('category')
            ,dataIndex: 'category'
            ,width: 30
            ,sortable: true
            ,renderer: this.categoryRender
        },{
            header: _('type')
            ,dataIndex: 'type'
            ,width: 30
            ,sortable: false
            ,editable: false
            ,renderer: this.typeRender
        },{
            header: _('path')
            ,dataIndex: 'path'
            ,sortable: false
            ,editable: false
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
                var rec = config.store.getAt(rowIndex);
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
        ,fields: ['filename','category','type', 'path']
        ,id: 'semanager-grid-elements-files'
        ,url: SEManager.config.connectorUrl
        ,baseParams: {
            action: 'files/getlist'
        }
        ,clicksToEdit: 2
        ,autosave: true
        ,save_action: 'files/updatefromgrid'
        ,plugins: this.exp
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,listeners: {
            'afterAutoSave': {fn:function() {
                this.refresh();
            },scope:this}
            ,'afterEdit': {fn:function(e) {
                e.record.data.type = config.type;
            }}
        }


    });
    SEManager.grid.Files.superclass.constructor.call(this, config);
};
Ext.extend(SEManager.grid.Files, MODx.grid.Grid, {

    typeRender: function(r) {

        if(r == 0){
            return 'no_type';
        }

        return r;

        //return _(r.slice(0,-1))
    }

    ,categoryRender: function(r) {

        //console.log(r);

        if(r == 0){
            return _('no_category');
        }
        return r;
    }

    ,renderDynField: function(v,md,rec,ri,ci,s,g) {
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
    ,getMenu: function(r) {


        console.log(r);

        var m = [];
        m.push({
            text: 'Make Element from File'
            ,handler: this.makeElement
        });
        this.addContextMenuItem(m);
    }
    ,makeElement: function(btn,e){
        var r = this.menu.record;
        r.clearCache = 1;

        if(r.type == null){
            MODx.msg.confirm({
                title: 'Выберите тип элемента'
                ,text: 'context_remove_confirm'
                //,url: MODx.config.connectors_url+'context/index.php'
                //,params: {
                //    action: 'remove'
                //   ,key: key
                //}
                //,listeners: {
                //    'success': {fn:function() {this.refresh();},scope:this}
                //}
            });
            //alert('dfdfasdf');
        }

        console.log(r);

        Ext.Ajax.request({
            url: SEManager.config.connectorUrl
            ,success: function(response) {
                console.log(response);
                //p.setValue(response.responseText);
                //p.enable();
            }
            ,params: {
                action: 'files/makeelement'
                ,element: r
            }
        });

        /*
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
        */
        //que.reset();
        //que.setValues(r);
        //que.show(e.target);
    }
});

Ext.reg('semanager-grid-files', SEManager.grid.Files);