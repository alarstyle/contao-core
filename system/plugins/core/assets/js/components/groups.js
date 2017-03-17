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
            },
            confirmExitIfUnsaved: {
                type: Boolean,
                default: true
            }
        },

        data: function () {
            return {
                active: null,
                editingState: false,
                sortableOptions: {
                    handle: '.item-sort',
                    onSort: this.handleSort
                }
            }
        },

        methods: {

            handleSort: function(event) {
                this.$emit('reorder', event);
            },

            groupClick: function (id) {
                if (this.active === id || this.$root.locked) return;
                var _this = this;
                if (this.confirmExitIfUnsaved) {
                    this.$root.confirmExitIfUnsaved(function() {
                        _this._changeSelectedGroup(id);
                    });
                }
                else {
                    _this._changeSelectedGroup(id);
                }

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
                if (this.$root.locked || this.editingState) return;
                this.editingState = true;
                this.active = null;
                this.$emit('editing-state', true);
            },

            editingStateOff: function () {
                if (this.$root.locked || !this.editingState) return;
                var _this = this;
                this.$root.confirmExitIfUnsaved(function() {
                    _this.editingState = false;
                    _this.active = null;
                    _this.$emit('editing-state', false);
                });
            },

            setActive: function (index) {
                this.active = index != null ? this.list[index].id : null;
            },

            findAndSetActive: function (predicate) {
                var _this = this;
                Vue.nextTick(function() {
                    _this.active  = _.findIndex(_this.list, predicate);
                });
            },


            _changeSelectedGroup: function(id) {
                this.active = id;
                if (this.editingState) {
                    this.$emit('group-edit', id);
                }
                else {
                    this.$emit('group-selected', id);
                }
            }

        }

    };

    Vue.component('groups', Groups);

}());