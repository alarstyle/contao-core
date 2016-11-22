(function () {

    var List = {

        data: function () {
            return {
                editing: false,
                groupsData: {
                    list: [],
                    creatable: false
                },
                fields: [],
                currentId: null
            }
        },

        watch: {},

        methods: {

            showGroups: function () {
                var _this = this;
                grow.action('getGroups')
                    .then(function (response) {
                        _this.groupsData.list = response.data.data.items;
                        _this.groupsData.creatable = response.data.data.creatable;
                    });
            },

            newGroup: function () {
                this.editGroup('new');
            },

            editGroup: function (id) {
                var _this = this;
                grow.action('getGroupsItem', {id: id})
                    .then(function (response) {
                        _this.currentId = id;
                        _this.fields = response.data.data.fields;
                        _this.editing = true;
                    });
            },

            saveGroup: function () {
                var _this = this;
                var fieldsValues = _this.$refs.form.getData();
                if (!Object.keys(fieldsValues).length) {
                    console.log('Nothing was changed');
                    return;
                }

                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));
                console.log(fieldsValues);
                grow.action('saveGroup', {id: _this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        if (response.data.success) {
                            console.log('SAVED');
                        }
                        else if (response.data.error) {
                            _this.showErrors(response.data.errorData);
                            //_this.$refs.form.showErrors(response.data.errorData);
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
                            _this.showErrors(response.data.errorData);
                        }
                    });
            },

            cancelEditGroup: function () {
                this.showList();
            },

            showErrors: function (errorData) {
                console.log(errorData);
                _.forEach(this.fields, function(field, i) {
                    if (!errorData[field.name]) return;
                    field.error = errorData[field.name][0];
                });
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
            }

        },

        mounted: function () {

            this.showGroups();

        }

    };

    window.ExtendedVue = Vue.extend(List);

}());