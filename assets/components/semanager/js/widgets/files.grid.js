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
            ,text: _('semanager.common.actions.fromfiles')
            ,icon: MODx.config.template_url + 'images/restyle/icons/elements.png'
            ,cls:'x-btn-text-icon'
            ,style: {
                paddingLeft: '5px'
                ,float: 'left'
                ,marginRight: '20px'
            }
            ,listeners: {
                click: function(){

                }
            }
            ,handler:this.makeElements
        }];
    }
    config.tbar.push('->',{
        xtype: 'modx-combo'
        ,name: 'filter_type'
        ,id: 'semanager-filter-type-files'
        ,emptyText: _('semanager.elements.filter_by_type')
        ,fields: ['id','type']
        ,displayField: 'type'
        ,valueField: 'id'
        ,width: 250
        ,pageSize: 10
        ,url: SEManager.config.connectorUrl
        ,baseParams: {
            action: 'files/gettypelist.class'
            ,type: config.type
        }
        ,listeners: {
            'select': {fn: this.filterByType, scope: this}
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
            ,sortable: false
        },{
            header: _('category')
            ,dataIndex: 'category'
            ,width: 30
            ,sortable: false
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
        }
        ]
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
        ,fields: ['filename','category','type', 'path','content']
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

    }

    ,categoryRender: function(r) {

        //console.log(r);

        if(r == 0){
            return _('no_category');
        }
        return r;
    }
/*
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
*/
    ,onDirty: function(){
        console.log(this.config.panel);

        if (this.config.panel) {
            Ext.getCmp(this.config.panel).fireEvent('fieldChange');
        }
    }
    ,filterByType: function(type, selected){
        this.getStore().baseParams = {
            action: 'files/getlist'
            ,type: selected.id
        };
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterByName: function(tf, newValue) {
        this.getStore().baseParams.namefilter = newValue || tf;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,updateGrid: function() {
        alert(123);
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }

    ,clearFilter: function() {
        this.getStore().baseParams = {
            action: 'files/getlist'
        };

        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function(r) {
        var m = [];
        m.push({
            text: _('semanager.common.actions.fromfile')
            ,handler: this.makeElement
        });
        m.push({
            text: _('semanager.common.actions.quickupdatefile')
            ,handler: this.updatefile
        });
        m.push({
            text: _('semanager.common.actions.deletefile')
            ,handler: this.deleteFiles
        });
        this.addContextMenuItem(m);
    }
    ,deleteFiles: function(btn,e){
        MODx.msg.confirm({
            title: _('semanager.common.actions.delete')
            ,text: _('semanager.common.actions.deletefileq')
            ,url: this.config.url
            ,params: {
                action: 'files/delete.class'
                ,path: this.menu.record.path
            }
            ,listeners: {
                'success': {fn:function(){
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,updatefile: function(btn,e){
        var r = this.menu.record;
        r.name = r.filename;
        r.source = '0';
        r.file = r.path;
        r.clearCache = 1;
        var que = MODx.load({
            xtype: 'modx-window-file-quick-update'
            ,url: this.config.url
            ,record: r
            ,grid: this
            ,action: 'files/updatefiles.class'
            ,listeners: {
                'success': {fn:function(){
                    this.refresh();
                },scope:this}
            }
        });
        que.reset();
        que.setValues(r);
        que.show(e.target);

    }
    ,makeElement: function(btn,e){
        MODx.msg.confirm({
            title: _('semanager.common.actions.create.element')
            ,text: _('semanager.common.actions.create.element.confirm')
            ,url: this.config.url
            ,params: {
                action: 'files/makeelement.class'
                ,path: this.menu.record.path
                ,category: this.menu.record.category
            }
            ,listeners: {
                'success': {fn:function(){
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,makeElements: function(btn,e){

         Ext.Msg.show({
             title: _('please_wait')
             ,msg: ('Создание элементов из файлов')
             ,width: 240
             ,progress:true
             ,closable:false
         });

         MODx.util.Progress.reset();
         for(var i = 1; i < 20; i++) {

            setTimeout('MODx.util.Progress.time('+i+','+MODx.util.Progress.id+')',i*1000);
         }

         MODx.Ajax.request({
             url: SEManager.config.connectorUrl
             ,params: {
                action: 'files/newelem.class'
             }
             ,listeners: {
             'success': {fn:function(r) {
                 MODx.util.Progress.reset();
                 Ext.Msg.hide();
                     this.refresh();
                 },scope:this}
                 ,'failure': {fn:function(r) {
                     MODx.util.Progress.reset();
                     Ext.Msg.hide();
                     return false;
                 },scope:this}
             }

         });

    }
});

Ext.reg('semanager-grid-files', SEManager.grid.Files);
