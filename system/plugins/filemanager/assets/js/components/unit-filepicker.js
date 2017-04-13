(function () {

    var FilePicker = {

        extends: AbstractUnit,

        template: '#unit-filepicker-template',

        props: {
            // : {type: String},
            // multiple: {type: Boolean, default: false},
            // required: {type: Boolean, default: false},
            // type: {type: String, default: 'file'},
            // value: String
        },

        data: function () {
            return {

            };
        },

        methods: {

            filePicked: function (data) {
                if (data.fieldId !== this.id) return;
                console.log(data.files[0]);
                this.currentValue = data.files[0].path;
                this.$emit('change', this.currentValue, this);
            },

            openFilePickerModal: function () {
                this.$root.$emit('openFilePickerModal', {
                    fieldId: this.id,
                    multiple: this.multiple,
                    required: this.required
                });
            },

            clear: function () {
                var component = this;
                setTimeout(function() {
                    component.currentValue = null;
                    component.$emit('change', component.currentValue, component);
                }, 100);

            }

        },

        created: function () {

            this.$root.$on('filePicked', this.filePicked);
        }

    };

    Vue.component('unit-filepicker', FilePicker);

}());