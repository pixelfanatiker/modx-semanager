SEManager.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
         border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container form-with-labels'
        ,items: [{
            html: '<h2>' + _('semanager.title') + ' <sup style="font-size: 0.5em">' + _('semanager.description') + '</sup></h2>'
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
                        xtype: 'button'
                        ,text: 'Синхронизировать все'
                        ,icon: MODx.config.template_url + 'images/restyle/icons/refresh.png'
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
                                        console.log(response.responseText);

                                    }
                                    ,failure: function(response) {
                                        console.log(response);
                                    }
                                    ,params: {
                                        action: '/mgr/common/syncall'
                                        ,root: '111111'
                                    }
                                });
                            }
                        }
                    },{
                        html: '<p>Синхронизирует все элементы и сканирует папку на наличие новых файлов</p>'
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
            },{
                //title: _('semanager.tabs.settings')
                title: 'Исключения'
                ,id: 'semanager-tab-settings'
                ,items: [{
                    xtype: 'semanager-tab-common'
                }]
            }]
        }]
    });
    SEManager.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(SEManager.panel.Home,MODx.Panel);
Ext.reg('semanager-panel-home',SEManager.panel.Home);