(function () {

    var List = {

        data: function () {
            return {
                state: '',

                groupsList: [],
                groupsCreatable: false,

                listHeaders: [],
                listItems: [],
                listCreatable: false,

                formFields: {},
                formErrors: {},
                currentId: null
            }
        },

        watch: {},

        mounted: function () {

            this.state = 'list';

            this.showGroups();
            this.showList();

        },

        methods: {

            showGroups: function () {
                var _this = this;
                grow.action('getGroups')
                    .then(function (response) {
                        _this.groupsList = response.data.data.groups;
                        _this.groupsCreatable = response.data.data.creatable;
                    });
            },

            showList: function (filterData) {
                var _this = this;
                this.currentId = null;
                grow.action('getList', filterData)
                    .then(function (response) {
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
                        _this.state = 'edit_item';
                    });
            },

            saveItem: function () {

                if (!this.$refs.form.isChanged) {
                    grow.notify('Nothing was changed', {type: 'warning'});
                    return;
                }

                var _this = this;
                var fieldsValues = _this.$refs.form.getValues();
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                grow.action('saveItem', {id: _this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        if (response.data.success) {
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.formErrors = {};
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed ', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                    });
            },

            deleteItem: function(id) {
                var _this = this;
                if (!confirm("Are you sure?")) {
                    return;
                }

                grow.action('deleteItem', {id: id})
                    .then(function (response) {
                        if (response.data.success) {
                            _this.showList();
                        }
                        else {

                        }
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

            cancelEditItem: function () {
                this.showList();
            },

            onListingOperation: function(id, operationName) {
                if (operationName === 'edit') {
                    this.editItem(id);
                }
                else if (operationName === 'delete') {
                    this.deleteItem(id);
                }
                else if (operationName === 'toggle') {

                }
            },

            newGroup: function() {



            }

        }

    };

    window.ExtendedVue = Vue.extend(List);

}());