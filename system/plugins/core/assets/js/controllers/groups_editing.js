(function () {

    var List = {

        data: function () {
            return {
                groupsList: [],
                groupsCreatable: false,
                groupsEditable: false,

                //formTitle: '',
                formFields: {},
                formErrors: {},

                currentId: null
            }
        },

        computed: {
            formTitle: function () {
                var group = _.find(this.groupsList, {id: this.currentId}),
                    groupName = '';
                if (group) {
                    var div = document.createElement("div");
                    div.innerHTML = group.fields[0];
                    groupName = div.textContent || div.innerText || '';
                }
                return groupName;
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
                this.editGroup('new');
            },

            editGroup: function (id) {
                var _this = this;
                grow.action('getGroupsItem', {id: id})
                    .then(function (response) {
                        if (response.data.success) {
                            _this.formFields = response.data.data.fields;
                            _this.currentId = id;
                        }
                        else if (response.data.error) {
                            grow.notify('Loading failed', {type: 'danger'});
                        }
                    });
            },

            saveGroup: function () {
                if (!this.$refs.form.isChanged) {
                    grow.notify('Nothing was changed', {type: 'warning'});
                    return;
                }

                var _this = this;
                var fieldsValues = _this.$refs.form.getValues();
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                grow.action('saveGroup', {id: _this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        if (response.data.success) {
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.formErrors = {};
                            if (_this.currentId !== 'new') return;
                            _this.currentId = response.data.data.newId;
                            console.log('!!!');
                            console.log(_this.currentId);
                            _this.loadGroups();
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed ', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                    });
            },

            deleteGroup: function () {
                if (!confirm("Are you sure?")) {
                    return;
                }

                var _this = this,
                    id = this.currentId;

                grow.action('deleteGroup', {id: id})
                    .then(function (response) {
                        if (response.data.success) {
                            _this.cancelEditGroup();
                            _this.groupsList = _.reject(_this.groupsList, {id: id});
                        }
                        else {
                            grow.notify('Deleting failed', {type: 'danger'});
                        }
                    });
            },

            cancelEditGroup: function () {
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