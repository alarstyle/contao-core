(function () {

    var Dropdown = {

        template: '#gr-dropdown-template',

        props: {
            position: String
        },

        data: function () {
            return {
                opened: false
            }
        },

        watch: {
            opened: function(opened) {
                if (opened) {
                    document.documentElement.addEventListener('click', this.documentClick, false);
                }
                else {
                    document.documentElement.removeEventListener('click', this.documentClick, false);
                }
            }
        },

        methods: {

            toggle: function () {
                this.opened = !this.opened;
            },

            documentClick: function() {
                if (this.$el.contains(event.target)) return;
                this.opened = false;
            }

        }

    };

    Vue.component('gr-dropdown', Dropdown);

}());