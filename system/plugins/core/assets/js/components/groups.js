(function () {

    var Groups = {

        template: '#groups-template',

        props: {
            title: String,
            labelNew: String,
            labelAll: String,
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
            },
            sortable: {
                type: Boolean,
                default: false
            }
        },

        data: function () {
            return {
                active: null,
                editingState: false
            }
        },

        methods: {

            groupClick: function (i) {
                if (this.active === i) return;
                this.active = i;
                if (this.editingState) {
                    this.$emit('group-edit', this.list[i].id);
                }
                else {
                    this.$emit('group-selected', this.list[i].id);
                }
            },

            allClick: function () {
                if (this.active === null || this.editingState) return;
                this.active = null;
                this.$emit('group-selected', null);
            },

            newClick: function () {
                this.active = null;
                this.$emit('new-group');
            },

            editingStateOn: function () {
                this.editingState = true;
                this.active = null;
                this.$emit('editing-state', true);
            },

            editingStateOff: function () {
                this.editingState = false;
                this.active = null;
                this.$emit('editing-state', false);
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