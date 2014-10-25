(function() {  
    tinymce.create('tinymce.plugins.button', {  
        init : function(ed, url) {  
            ed.addButton('button', {  
                title : 'Add a button',  
                image : url+'/icons/button.png',  
                onclick : function() {  
                    ed.selection.setContent('[button color="e.g. dark, grey" size="e.g. small, medium, big" link="" target="e.g. _self, _blank"]'+ed.selection.getContent()+'[/button]');
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }
    });  
    tinymce.PluginManager.add('button', tinymce.plugins.button);  
})();

