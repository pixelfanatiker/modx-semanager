SEManager.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
         border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container form-with-labels'
        ,items: [{
            html: '<h2>' + _('semanager.title') + ' </h2><p>' + _('semanager.description') + '</p>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: {
                autoHeight: true
                ,hideMode: 'offsets'
                ,border: true
            }

            ,stateful: true
            ,stateId: 'semanager-tabpanel-home'
            ,stateEvents: ['tabchange']
            //,activeItem: 0
            ,getState: function() {
                return { activeTab:this.items.indexOf(this.getActiveTab()) };
            }

            ,items: [{
                 title:  _('semanager.tabs.actions')
                ,id: 'semanager-tab-actions'
                ,layout: 'form'
                ,items: [{
                    border: false
                    ,bodyCssClass: 'panel-desc'
                    ,items: [{
                        html: '<p>' + _('semanager.sync.description') + '</p>'
                        ,border: false
                        ,style: {
                            lineHeight: '30px'
                        }
                    }]
                },{
                    bodyCssClass: 'main-wrapper'
                    ,border: false
                    ,items: [{
                        xtype: 'semanager-grid-files'
                    }]
                }]
            },{
                title: _('chunks')
                ,id: 'semanager-tab-chunks'
                ,layout: 'form'
                ,items: [{
                    html: '<p>'+_('chunks')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'semanager-grid-elements-chunks'
                    ,preventSaveRefresh: true
                    ,cls: 'main-wrapper'
                    ,type: 'chunk'
                }]
            },{
                title: _('plugins')
                ,id: 'semanager-tab-plugins'
                ,layout: 'form'
                ,items: [{
                    html: '<p>'+_('plugins')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'semanager-grid-elements-plugins'
                    ,preventSaveRefresh: true
                    ,cls: 'main-wrapper'
                    ,type: 'plugin'
                }]
            },{
                title: _('snippets')
                ,id: 'semanager-tab-snippets'
                ,layout: 'form'
                ,items: [{
                    html: '<p>' + _('snippets') + '</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'semanager-grid-elements-snippets'
                    ,preventSaveRefresh: true
                    ,cls: 'main-wrapper'
                    ,type: 'snippet'
                }]
            },{
                title: _('templates')
                ,id: 'semanager-tab-templates'
                ,layout: 'form'
                ,items: [{
                    html: '<p>'+_('templates')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'semanager-grid-elements-templates'
                    ,preventSaveRefresh: true
                    ,cls: 'main-wrapper'
                    ,type: 'template'
                }]
            }/*,{
                //title: _('semanager.tabs.settings')
                title: 'Исключения'
                ,id: 'semanager-tab-settings'
                ,items: [{
                    xtype: 'semanager-tab-common'
                }]
            }*/
            ]

        }]
    });
    SEManager.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(SEManager.panel.Home,MODx.Panel);
Ext.reg('semanager-panel-home',SEManager.panel.Home);
