(function () {

    var Listing = {

        extends: AbstractApp,

        data: function () {
            return {
                state: '',

                groupsList: [],
                groupsCreatable: false,
                groupsEditable: false,

                listHeaders: [],
                listItems: [],
                listCreatable: false,

                formFields: {},
                formSidebarFields: {},
                formErrors: {},

                currentId: null,
                currentGroupId: null
            }
        },

        computed: {
            formTitle: function () {
                switch (this.state) {

                    case 'edit_group':
                        var group = _.find(this.groupsList, {id: this.currentGroupId});
                        return group ? group.title : '';
                        break;

                }
                return '';
            }
        },

        watch: {},

        methods: {

            loadGroups: function () {
                var _this = this;
                grow.action('getGroups')
                    .then(function (response) {
                        _this.groupsList = response.data.data.groups;
                        _this.groupsCreatable = response.data.data.creatable;
                        _this.groupsEditable = response.data.data.editable;
                    });
            },

            showList: function (filterData) {
                var _this = this;
                this.$root.locked = true;
                grow.action('getList', filterData)
                    .then(function (response) {
                        _this.$root.locked = false;
                        _this.currentId = null;
                        _this.currentGroupId = null;
                        _this.listHeaders = response.data.data.headers;
                        _this.listItems = response.data.data.items;
                        _this.listCreatable = response.data.data.creatable;
                        _this.state = 'list';
                    });
            },

            groupSelected: function(groupId) {
                this.showList({groupId: groupId})
            },

            newItem: function () {
                this.editItem('new');
            },

            editItem: function (id) {
                var _this = this;
                grow.action('getListItem', {id: id})
                    .then(function (response) {
                        _this.currentId = id;
                        _this.formFields = response.data.data.fields;
                        _this.formSidebarFields = response.data.data.sidebar;
                        _this.formErrors = {};
                        _this.state = 'edit_item';
                    });
            },

            saveItem: function () {
                if (!this.$refs.form.isChanged && (!this.$refs.formSidebar || !this.$refs.formSidebar.isChanged)) {
                    grow.notify('Nothing was changed', {type: 'warning'});
                    return;
                }

                this.locked = true;

                var _this = this;
                var fieldsValues = _.defaults(
                    _this.$refs.form.getValues(),
                    _this.$refs.formSidebar ? _this.$refs.formSidebar.getValues() : []);
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                grow.action('saveItem', {id: _this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.unsaved = false;
                            _this.formErrors = {};
                            if (_this.currentId === 'new') {
                                _this.currentId = response.data.data.newId;
                            }
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed ', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                    });
            },

            deleteItem: function(id) {
                if (this.locked) return;
                if (id === undefined) {
                    id = this.currentId;
                }

                var _this = this;

                this.$root.confirmDelete(function() {
                    _this.locked = true;
                    grow.action('deleteItem', {id: id})
                        .then(function (response) {
                            _this.locked = false;
                            if (response.data.success) {
                                _this.showList();
                            }
                            else {

                            }
                        });
                });
            },

            disableItem: function(id) {
                var _this = this;
                grow.action('disableItem', {id: id})
                    .then(function (response) {
                        if (response.data.error) {

                        }
                    });
            },

            cancelEdit: function () {
                if (this.locked) return;
                if (this.state === 'edit_item') {

                }
                else {
                    this.$refs.groups.setActive(null);
                }
                this.showList();
            },

            saveClick: function () {
                if (this.locked) return;
                if (this.state === 'edit_item') {
                    this.saveItem();
                }
                else {
                    this.saveGroup();
                }
            },

            deleteClick: function() {
                if (this.locked) return;
                if (this.state === 'edit_item') {
                    this.deleteItem();
                }
                else {
                    this.deleteGroup();
                }
            },

            onListingOperation: function(id, operationName) {
                if (this.$root.locked) return;
                if (operationName === 'edit') {
                    this.editItem(id);
                }
                else if (operationName === 'delete') {
                    this.deleteItem(id);
                }
                else if (operationName === 'toggle') {

                }
            },

            clickEditGroup: function () {

            },

            newGroup: function () {
                if (this.currentGroupId === 'new') return;
                this.currentId = null;
                this.editGroup('new');
            },

            groupsEditingState: function (state) {
                if (state) {

                }
                else {
                    if (state !== 'list') {
                        this.showList();
                    }
                }
            },

            editGroup: function (id) {
                if (this.locked) return;
                this.locked = true;
                var _this = this;
                grow.action('getGroup', {id: id})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            _this.formFields = response.data.data.fields;
                            _this.formSidebarFields = response.data.data.sidebar;
                            _this.formErrors = {};
                            if (id === 'new' && _this.$refs.form) {
                                _this.$refs.form.reset();
                            }
                            _this.currentGroupId = id;
                            _this.state = 'edit_group';
                        }
                        else if (response.data.error) {
                            grow.notify('Loading failed', {type: 'danger'});
                        }
                    });
            },

            saveGroup: function () {

                this.locked = true;

                var _this = this;
                var fieldsValues = _this.$refs.form.getValues();
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                grow.action('saveGroup', {id: _this.currentGroupId, fields: fieldsValues})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.unsaved = false;
                            _this.formErrors = {};
                            if (_this.currentGroupId === 'new') {
                                _this.currentGroupId = response.data.data.newId;
                            }
                            _this.loadGroups();
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed ', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                    });
            },

            deleteGroup: function() {
                if (this.locked) return;
                var _this = this,
                    id = this.currentGroupId;

                this.$root.confirmDelete(function() {
                    _this.locked = true;

                    grow.action('deleteGroup', {id: id})
                        .then(function (response) {
                            _this.locked = false;
                            if (response.data.success) {
                                _this.cancelEdit();
                                _this.groupsList = _.reject(_this.groupsList, {id: id});
                            }
                            else {
                                grow.notify('Deleting failed', {type: 'danger'});
                            }
                        });
                });
            }

        },

        mounted: function () {

            this.state = 'list';

            this.loadGroups();
            this.showList();

        }

    };

    window.Listing = Vue.extend(Listing);

}());