(function () {

    var FilePicker = {

        template: '#rc-filepicker-template',

        props: {
            multiple: {type: Boolean, default: false}
        },

        created: function () {
            this.$root.$on('filePicked', function (data) {
                console.log(data);
            });
        },

        methods: {

            openFilePickerModal: function () {
                var data = {
                    id: 'some_id',
                    multiple: this.multiple
                };
                this.$root.$emit('openFilePickerModal', data);
            }

        }

    };

    Vue.component('rc-filepicker', FilePicker);

}());