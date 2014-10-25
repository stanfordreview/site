(function() {  
    tinymce.create('tinymce.plugins.tabs', {  
        init : function(ed, url) {  
            ed.addButton('tabs', {  
                title : 'Add tabs',  
                image : url+'/icons/tabs.png',  
                onclick : function() {  
                    var string = '[tabs]';
                    string += ' [tab title="Tab 1"]Tab content 1[/tab]';
                    string += ' [tab title="Tab 2"]Tab content 2[/tab]';
                    string += ' [tab title="Tab 3"]Tab content 3[/tab]';
                    string += ' [/tabs]';
                    ed.selection.setContent(string);
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }
    });  
    tinymce.PluginManager.add('tabs', tinymce.plugins.tabs);  
})();

