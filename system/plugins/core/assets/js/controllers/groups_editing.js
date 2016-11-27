(function () {

    var List = {

        extends: AbstractApp,

        data: function () {
            return {
                groupsList: [],
                groupsCreatable: false,
                groupsEditable: false,

                formFields: {},
                formErrors: {},

                currentId: null
            }
        },

        computed: {
            formTitle: function () {
                var group = _.find(this.groupsList, {id: this.currentId});
                return group ? group.title : '';
            }
        },

        methods: {

            loadGroups: function () {
                var _this = this;
                grow.action('getGroups')
                    .then(function (response) {
                        if (response.data.success) {
                            // Setting data
                            _this.groupsList = response.data.data.groups;
                            _this.groupsCreatable = response.data.data.creatable;

                            // Selecting group
                            if (_this.currentId === null || _this.currentId === 'new') return;
                            _this.$refs.groups.findAndSetActive({id: _this.currentId});
                        }
                        else if (response.data.error) {
                            grow.notify('Loading failed', {type: 'danger'});
                        }
                    });
            },

            newGroup: function () {
                if (this.currentId === 'new') return;
                this.editGroup('new');
            },

            editGroup: function (id) {
                var _this = this;
                grow.action('getGroupsItem', {id: id})
                    .then(function (response) {
                        if (response.data.success) {
                            _this.formFields = response.data.data.fields;
                            _this.formErrors = {};
                            // if (id === 'new' && _this.$refs.form) {
                            //     _this.$refs.form.reset();
                            // }
                            _this.currentId = id;
                        }
                        else if (response.data.error) {
                            grow.notify('Loading failed', {type: 'danger'});
                        }
                    });
            },

            saveGroup: function () {
                if (this.locked) return;

                if (!this.$refs.form.isChanged) {
                    grow.notify('Nothing was changed', {type: 'warning'});
                    return;
                }

                this.locked = true;

                var _this = this;
                var fieldsValues = _this.$refs.form.getValues();
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                grow.action('saveGroup', {id: _this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.formErrors = {};
                            if (_this.currentId === 'new') {
                                _this.currentId = response.data.data.newId;
                            }
                            _this.loadGroups();
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed ', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                    });
            },

            deleteGroup: function () {
                if (this.locked) return;

                var _this = this,
                    id = this.currentId;

                this.$root.confirmDelete(function() {
                    _this.locked = true;

                    grow.action('deleteGroup', {id: id})
                        .then(function (response) {
                            _this.locked = false;
                            if (response.data.success) {
                                _this.cancelEditGroup();
                                _this.groupsList = _.reject(_this.groupsList, {id: id});
                            }
                            else {
                                grow.notify('Deleting failed', {type: 'danger'});
                            }
                        });
                });
            },

            cancelEditGroup: function () {
                if (this.locked) return;

                this.currentId = null;
                this.$refs.groups.setActive(null);
            }

        },

        mounted: function () {

            this.loadGroups();

        }

    };

    window.ExtendedVue = Vue.extend(List);

}());