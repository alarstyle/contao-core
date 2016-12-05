(function () {

    var FilePickerModal = {

        template: '#rc-filepicker-modal-template',

        data: function () {
            return {
                fieldId: '',
                multiple: false,
                initialized: false,
                somethingSelected: false,
                selectedFiles: []
            };
        },

        mounted: function () {
            var _this = this;
            this.$root.$on('openFilePickerModal', function (data) {
                _this.fieldId = data.fieldId;
                _this.multiple = data.multiple;
                _this.initialized = true;
                _this.$refs.modal.open();
            });
        },

        methods: {

            onFileSelected: function (data) {
                if (!data.length || !data[0].isImage) {
                    this.somethingSelected = false;
                    this.selectedFiles = [];
                    return;
                }
                this.somethingSelected = true;
                this.selectedFiles = data;
                console.log('seleced', data);
            },

            select: function () {
                this.$refs.modal.close();
                this.$root.$emit('filePicked', {
                    fieldId: this.fieldId,
                    files: this.selectedFiles
                });
            },

            cancel: function () {
                this.$refs.modal.close();
                this.$root.$emit('filePickCanceled', {
                    fieldId: this.fieldId
                });
            }

        }

    };

    Vue.component('rc-filepicker-modal', FilePickerModal);

}());