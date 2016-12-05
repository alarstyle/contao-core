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
                if (this.active === i || this.$root.locked) return;
                var _this = this;
                this.$root.confirmExitIfUnsaved(function() {
                    _this.active = i;
                    if (_this.editingState) {
                        _this.$emit('group-edit', _this.list[i].id);
                    }
                    else {
                        _this.$emit('group-selected', _this.list[i].id);
                    }
                });

            },

            allClick: function () {
                if (this.active === null || this.editingState || this.$root.locked) return;
                this.active = null;
                this.$emit('group-selected', null);
            },

            newClick: function () {
                if (this.$root.locked) return;
                var _this = this;
                this.$root.confirmExitIfUnsaved(function() {
                    _this.active = null;
                    _this.$emit('new-group');
                });
            },

            editingStateOn: function () {
                if (this.$root.locked) return;
                this.editingState = true;
                this.active = null;
                this.$emit('editing-state', true);
            },

            editingStateOff: function () {
                if (this.$root.locked) return;
                var _this = this;
                this.$root.confirmExitIfUnsaved(function() {
                    _this.editingState = false;
                    _this.active = null;
                    _this.$emit('editing-state', false);
                });
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