/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    var href = window.location.href.split('/');
    if (href[6] === 'cara_order') {
        var base_url = document.getElementById('base_url').value;
        config.toolbarGroups = [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'forms', groups: [ 'forms' ] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] },
            '/',
            { name: 'styles', groups: [ 'styles' ] },
            { name: 'colors', groups: [ 'colors' ] },
            { name: 'tools', groups: [ 'tools' ] },
            { name: 'others', groups: [ 'others' ] },
            { name: 'about', groups: [ 'about' ] }
        ];

        config.removeButtons = 'Source,Save,Templates,NewPage,Preview,Print,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,RemoveFormat,CopyFormatting,CreateDiv,Blockquote,Language,BidiRtl,BidiLtr,SpecialChar,Table,HorizontalRule,Styles,Format,Font,FontSize,Maximize,About,ShowBlocks,BGColor,TextColor,Scayt,SelectAll,Find,Replace,Redo,Undo,Indent,Outdent,Flash,Smiley,PageBreak';
        config.extraPlugins = 'iframe,filebrowser';
        config.removePlugins = 'easyimage';
        config.filebrowserBrowseUrl = base_url + 'assets/filemanager/dialog.php?type=2&editor=ckeditor&fldr=';
        config.filebrowserUploadUrl = base_url + 'assets/filemanager/dialog.php?type=2&editor=ckeditor&fldr=';
        config.filebrowserImageBrowseUrl = base_url + 'assets/filemanager/dialog.php?type=1&editor=ckeditor&fldr=';
    }
};
