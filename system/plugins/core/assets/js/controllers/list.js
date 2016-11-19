(function () {

    var List = {

        data: function () {
            return {
                state: '',
                groupsData: {},
                listHeaders: [],
                listItems: [],
                list: [],
                fields: [],
                currentId: null
            }
        },

        watch: {},

        mounted: function () {

            this.showGroups();
            this.showList();

        },

        methods: {

            showGroups: function () {
                var _this = this;
                grow.action('getGroups')
                    .then(function (response) {
                        _this.groupsData.list = response.data.data.groups;
                        console.log(_this.groups);
                    });
            },

            showList: function () {
                var _this = this;
                this.currentId = null;
                grow.action('getList')
                    .then(function (response) {
                        _this.listHeaders = response.data.data.headers;
                        _this.listItems = response.data.data.items;
                        _this.list = response.data.data.list;
                        _this.state = 'list';
                    });
            },

            newItem: function () {
                this.editItem('new');
            },

            editItem: function (id) {
                var _this = this;
                grow.action('getListItem', {id: id})
                    .then(function (response) {
                        _this.currentId = id;
                        _this.fields = response.data.data.fields;
                        _this.state = 'edit_item';
                    });
            },

            saveItem: function () {
                var _this = this;
                var fieldsValues = _this.$refs.form.getData();
                if (!Object.keys(fieldsValues).length) {
                    console.log('Nothing was changed');
                    return;
                }

                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));
                console.log(fieldsValues);
                grow.action('saveItem', {id: _this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        if (response.data.error) {
                            _this.showErrors(response.data.errorData);
                            //_this.$refs.form.showErrors(response.data.errorData);
                        }
                    });
            },

            deleteItem: function($id) {
                var _this = this;
                if (!confirm("Are you sure?")) {
                    return;
                }

                grow.action('deleteItem', {id: $id})
                    .then(function (response) {
                        if (response.data.success) {
                            _this.showList();
                        }
                        else {
                            _this.showErrors(response.data.errorData);
                        }
                    });
            },

            disableItem: function($id) {
                var _this = this;
                grow.action('disableItem', {id: $id})
                    .then(function (response) {
                        if (response.data.error) {
                            _this.showErrors(response.data.errorData);
                        }
                    });
            },

            cancelEditItem: function () {
                this.showList();
            },

            showErrors: function (errorData) {
                console.log(errorData);
                _.forEach(this.fields, function(field, i) {
                    if (!errorData[field.name]) return;
                    field.error = errorData[field.name][0];
                });
            },

            onOperation: function(id, operationName) {
                console.log(id, operationName);
            }

        }

    };

    window.ExtendedVue = Vue.extend(List);

}());