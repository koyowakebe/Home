(function() {
    tinymce.create( 'tinymce.plugins.instagram_shortcode_button', {
        init: function( ed, url ) {
            ed.addButton( 'instagram', {
            title: 'instagram',
            icon: 'code',
            cmd: 'insta_cmd'
        });
        
        ed.addCommand( 'insta_cmd', function() {
            var selected_text = ed.selection.getContent();
            var return_text = '';
            return_text = '[insta link=]' + selected_text;
            ed.execCommand( 'mceInsertContent', 0, return_text );
        });
    },
    createControl : function( n, cm ) {
        return null;
    },
    });
    tinymce.PluginManager.add( 'instagram_button', tinymce.plugins.instagram_shortcode_button );
})();