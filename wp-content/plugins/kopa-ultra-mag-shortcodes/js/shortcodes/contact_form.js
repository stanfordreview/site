(function() {  
    tinymce.create('tinymce.plugins.contact_form', {  
        init : function(ed, url) {  
            ed.addButton('contact_form', {  
                title : 'Contact Form',  
                image : url+'/icons/contact_form.png',  
                onclick : function() {  
                    ed.selection.setContent('[contact_form caption="Contact Us" description=""][/contact_form]');
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }
    });  
    tinymce.PluginManager.add('contact_form', tinymce.plugins.contact_form);  
})();

