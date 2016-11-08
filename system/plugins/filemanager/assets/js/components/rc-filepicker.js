(function () {

    var FilePicker = {

        template: '#rc-filepicker-template',

        props: {
            fieldId: {type: String, required: true},
            multiple: {type: Boolean, default: false},
            required: {type: Boolean, default: false},
            type: {type: String, default: 'file'},
            value: String
        },

        data: function () {
            return {
                path: ''
            };
        },

        created: function () {

            this.$root.$on('filePicked', this.onFilePicked);
        },

        methods: {

            onFilePicked: function (data) {
                if (data.fieldId !== this.fieldId) return;

            },

            openFilePickerModal: function () {
                this.$root.$emit('openFilePickerModal', {
                    fieldId: this.fieldId,
                    multiple: this.multiple,
                    required: this.required
                });
            },

            clear: function () {

            }

        }

    };

    Vue.component('rc-filepicker', FilePicker);

}());