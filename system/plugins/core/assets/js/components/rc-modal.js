(function() {
    var Modal = {

        template: '#rc-modal-template',

        props: {
            openEvent: String,
            closeEvent: String,
            opened: Boolean
        },

        data: function() {
            return {
                isOpened: this.opened
            }
        },

        mounted: function() {

            if (this.openEvent) {
                this.$root.$on(this.openEvent, this.open);
            }

            if (this.closeEvent) {
                this.$root.$on(this.closeEvent, this.close);
            }

        },

        methods: {

            open: function() {
                this.isOpened = true;
            },

            close: function() {
                this.isOpened = false;
            }

        }

    };

    Vue.component('rc-modal', Modal);

}());