(function () {

    var FilePickerModal = {

        template: '#rc-filepicker-modal-template',

        data: function () {
            return {
                fieldId: '',
                multiple: false,
                initialized: false
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
                console.log('seleced', data);
            },

            select: function () {
                this.$refs.modal.close();
                this.$root.$emit('filePicked', {
                    fieldId: this.fieldId
                });
            },

            cancel: function () {
                this.$refs.modal.close();
            }

        }

    };

    Vue.component('rc-filepicker-modal', FilePickerModal);

}());