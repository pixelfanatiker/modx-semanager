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
                        ,text: _('semanager.common.actions.allsync')
                        ,icon: MODx.config.template_url + 'images/restyle/icons/refresh.png'
                        ,cls:'x-btn-text-icon'
                        ,style: {
                            paddingLeft: '5px'
                            ,float: 'left'
                            ,marginRight: '20px'
                        }
                        ,listeners: {
                            click: function(){

                                Ext.Msg.show({
                                    title: _('please_wait')
                                    ,msg: ('Синхронизация')
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

                                        action: 'common/syncall'
                                        ,root: '111111'
                                    }
                                    ,listeners: {
                                        'success': {fn:function(r) {
                                            MODx.util.Progress.reset();
                                            Ext.Msg.hide();
                                        },scope:this}
                                        ,'failure': {fn:function(r) {
                                            MODx.util.Progress.reset();
                                            Ext.Msg.hide();
                                            MODx.form.Handler.errorJSON(r);
                                            return false;
                                        },scope:this}
                                    }
                                });

                                /*
                                Ext.Ajax.request({
                                    url: SEManager.config.connectorUrl
                                    ,success: function(response) {
                                        //

                                    }
                                    ,failure: function(response) {
                                        //
                                    }
                                    ,params: {
                                        action: 'common/syncall'
                                        ,root: '111111'
                                    }
                                });
                                */
                            }
                        }
                    },{
                        html: '<p style="background-color: #F7F7F7;">' + _('semanager.sync.description') + '</p>'
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