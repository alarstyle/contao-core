(function () {

    var ConfirmModal = {

        template: '#gr-confirm-modal-template',

        data: function () {
            return {};
        },

        methods: {

            open: function () {
                this.$refs.modal.open();
            },

            okClick: function() {
                this.$emit('ok');
                this.$refs.modal.close();
            },

            cancelClick: function () {
                this.$emit('cancel');
                this.$refs.modal.close();
            }

        }

    };

    Vue.component('gr-confirm-modal', ConfirmModal);

}());