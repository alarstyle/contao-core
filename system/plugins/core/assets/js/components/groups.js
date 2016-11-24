(function () {

    var Groups = {

        template: '#groups-template',

        props: {
            title: String,
            newLabel: String,
            list: {
                type: Array,
                default: []
            },
            creatable: {
                type: Boolean,
                default: false
            },
            editable: {
                type: Boolean,
                default: false
            }
        },

        data: function () {
            return {
                active: null
            }
        },

        methods: {

            groupClick: function (i) {
                if (this.active === i) return;
                this.active = i;
                this.$emit('group-selected', this.list[i].id);
            },

            newClick: function () {
                this.active = null;
                this.$emit('new-group');
            },

            setActive: function (index) {
                this.active = index;
            },

            findAndSetActive: function (predicate) {
                var _this = this;
                Vue.nextTick(function() {
                    _this.active  = _.findIndex(_this.list, predicate);
                });
            }

        }

    };

    Vue.component('groups', Groups);

}());