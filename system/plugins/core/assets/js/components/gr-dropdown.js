(function () {

    var Dropdown = {

        template: '#gr-dropdown-template',

        props: {
            position: String
        },

        data: function () {
            return {
                isOpened: false
            }
        },

        //created: function () {

            // if (this.openEvent) {
            //     this.$root.$on(this.openEvent, this.open);
            // }
            //
            // if (this.closeEvent) {
            //     this.$root.$on(this.closeEvent, this.close);
            // }

        //},

        methods: {

            open: function () {
                this.isOpened = true;
            },

            close: function () {
                this.isOpened = false;
            },

            toggle: function () {
                this.isOpened ? this.close() : this.open();
            }

        }

    };

    Vue.component('gr-dropdown', Dropdown);

}());