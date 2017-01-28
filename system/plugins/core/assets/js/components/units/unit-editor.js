(function() {

    var defaultSettings = {
        plugins: 'autoresize visualblocks template link image lists code fullscreen growcms',
        menubar: false,
        toolbar:
            //'undo redo' +
            ' | styleselect formatselect blockquote removeformat' +
            ' | bold italic' +
            ' | alignleft aligncenter alignright alignjustify' +
            ' | bullist numlist' +
            ' | link unlink' +
            ' | image template' +
            ' | visualblocks code' +
            ' | fullscreen',
        autoresize_min_height: 100,
        autoresize_max_height: 500,
        autoresize_bottom_margin: 10,
        browser_spellcheck: true,
        block_formats: 'Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Paragraph=p'
        // style_formats: [
        //     { title: 'Bold text', inline: 'strong' },
        //     { title: 'Red text', inline: 'span', styles: { color: '#ff0000' } },
        //     { title: 'Red header', block: 'h1', styles: { color: '#ff0000' } },
        //     { title: 'Badge', inline: 'span', styles: { display: 'inline-block', border: '1px solid #2276d2', 'border-radius': '5px', padding: '2px 5px', margin: '0 2px', color: '#2276d2' } },
        //     { title: 'Table row 1', selector: 'tr', classes: 'tablerow1' }
        // ],
        //formats: {
            // alignleft: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'left' },
            // aligncenter: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'center' },
            // alignright: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'right' },
            // alignfull: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'full' },
            // bold: { inline: 'span', 'classes': 'bold' },
            // italic: { inline: 'span', 'classes': 'italic' },
            // underline: { inline: 'span', 'classes': 'underline', exact: true },
            // strikethrough: { inline: 'del' },
            // customformat: { inline: 'span', styles: { color: '#00ff00', fontSize: '20px' }, attributes: { title: 'My custom format' }, classes: 'example1' },
        //},
        // content_css: [
        //     '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
        //     '//www.tinymce.com/css/codepen.min.css'
        // ]
    };

    var UnitEditor = {

        extends: AbstractUnit,

        template: '#unit-editor-template',

        data: function() {
            return {
                editor: null,
                currentImageFieldId: null
            }
        },

        watch: {
            value: function() {
                console.log('value changed');
            },
            currentValue: function() {
                console.log('currentValue changed');
            }
        },

        methods: {

            filePicked: function(data) {
                this.$root.$off('filePicked', this.filePicked);
                this.$root.$off('filePickCanceled', this.filePickCanceled);
                if (this.id !== data.fieldId && !this.currentImageFieldId) return;
                var path = data.files[0].path;
                document.getElementById(this.currentImageFieldId).value = path;
                this.currentImageFieldId = null;
            },

            filePickCanceled: function() {
                this.$root.$off('filePicked', this.filePicked);
                this.$root.$off('filePickCanceled', this.filePickCanceled);
                this.currentImageFieldId = null;
            },

            handleEditorChange: function(e) {
                this.currentValue = this.editor.getContent();
                this.$emit('change', this.currentValue, this);
                console.log('Editor contents was changed.');
            }

        },

        mounted: function() {
            if (this.editor) return;
            var _this = this,
                configSettings = this.config.settings,
                settings = {
                    target: this.$refs.editor,
                    init_instance_callback: function (editor) {
                        _this.editor = editor;

                        if (_this.currentValue) {
                            editor.setContent(_this.currentValue);
                        }
                        editor.on('Change', _this.handleEditorChange);
                    },
                    file_browser_callback: function(field_name, url, type, win) {
                        _this.currentImageFieldId = field_name;
                        _this.$root.$on('filePicked', _this.filePicked);
                        _this.$root.$on('filePickCanceled', _this.filePickCanceled);
                        _this.$root.$emit('openFilePickerModal', {
                            fieldId: this.id,
                            multiple: false,
                            required: false
                        });
                    }
                };
            tinymce.init(_.defaults(settings, configSettings, defaultSettings));
        },

        destroyed: function() {
            if (this.editor) {
                this.editor.destroy();
            }
        }

        /*
         rotateleft	imagetools	Rotate the current image counterclockwise.
         rotateright	imagetools	Rotate the current image clockwise.
         flipv	imagetools	Flip the current image vertically.
         fliph	imagetools	Flip the current image horizontally.
         editimage	imagetools	Edit the current image in the image dialog.
         imageoptions	imagetools	Opens the image options dialog.
         quickimage	inlite	Inserts an image from the local machine.
         quicktable	inlite	Inserts an table 2x2.
         quicklink	inlite	Inserts an link in a quicker way.
         */

    };

    Vue.component('unit-editor', UnitEditor);

}());