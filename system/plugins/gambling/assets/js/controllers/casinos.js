(function () {

    var fieldsNamesToProcess = ['name', 'alias', 'metaTitle', 'metaDescription', 'topTitle', 'topText', 'bottomTitle', 'bottomText'];

    var Casinos = {

        extends: Listing,

        data: function() {
            return {
                countries: APP_DATA.availableCountries || [],
                currentCountry: APP_DATA.currentCountry || null,
                casinoCountries: null,
                step: null,

                casinoDataCountryId: null,
                casinoData: {},

                categoryRawFields: null,

                currentCasinoName: '',
                currentCasinoType: ''
            }
        },

        computed: {
            formTitle: function () {
                switch (this.state) {

                    case 'edit_group':
                        var group = _.find(this.groupsList, {id: this.currentGroupId});
                        return group ? group.title : '';
                        break;

                    case 'edit_item':
                        if (this.step === 1) {
                            return 'Step 1. ' + this.currentCasinoName;
                        }
                        else if (this.step === 2) {
                            return 'Step 2. ' + this.currentCasinoName;
                        }
                        break;

                }
                return '';
            }
        },

        watch: {

            state: function(state) {
                this.updateCasinoCountries();
                if (this.state === 'edit_item') {
                    this.step = 1;
                }
            }

        },

        methods: {

            updateCasinoCountries: function(value) {
                if (this.state === 'edit_item') {
                    if (value === undefined) {
                        value = this.formFields.countries.value;
                    }
                    var options = this.formFields.countries.config.options;
                    if (!value || !value.length || !options || !options.length) {
                        this.casinoCountries = null;
                        return;
                    }
                    var selectedCountries = _.filter(options, function(option) {
                        return _.includes(value, option.value)
                    });
                    this.casinoCountries = _.map(selectedCountries, function(country) {
                        return {
                            label: country.label,
                            id: country.value
                        }
                    });
                }
            },

            handleCountryChange: function(countryId) {
                if (this.currentCountry === countryId) return;

                var _this = this;

                this.locked = true;

                this.action('changeCountry', {countryId: countryId})
                    .then(function (response) {
                        _this.currentCountry = countryId;
                        _this.locked = false;
                        if (response.data.success) {
                            if (_this.state === 'edit_group') {
                                _this.editGroup(_this.currentGroupId);
                            }
                            else {
                                _this.$refs.groups.editingStateOff();
                                _this.$refs.groups.setActive(null);
                                _this.loadGroups();
                                _this.showList();
                            }
                        }
                        else if (response.data.error) {
                            grow.notify('Unknown error', {type: 'danger'});
                        }
                    });
            },

            handleEditCountrySelected: function(countryId) {
                this.casinoDataCountryId = countryId;
            },

            handleFormChange: function(value, unit, form) {
                switch(unit.id) {
                    case 'countries':
                        this.updateCasinoCountries(value);
                        break;
                    case 'type':
                }
            },

            handleStep1Click: function() {
                if (this.step === 1) return;
                this._goToStep1();
            },

            handleStep2Click: function() {
                if (this.step === 2) return;
                this._goToStep2();
            },

            handleNextClick: function() {
                if (!this.casinoCountries || !this.casinoCountries.length) {
                    grow.notify('No countries specified', {type: 'danger'});
                    return;
                }

                var promise = this.saveItem();
                if (!promise) return;
                var _this = this;
                promise.then(function() {
                    Vue.nextTick(function() {
                        _this._goToStep2();
                    });
                });
            },

            handleGroupsReorder: function(event) {
                console.log('handle');
                console.log(this.groupsList);
                console.log(this.groupsList.slice());

                var _this = this,
                    list = this.groupsList.slice(),
                    oldIndex = event.oldIndex,
                    newIndex = event.newIndex,
                    item = list[oldIndex],
                    previousItem = newIndex === 0 ? null : (oldIndex < newIndex ? list[newIndex] : list[newIndex-1]);

                this.groupsList = arrayMove(list, oldIndex, newIndex);

                return this.action('reorderGroups', {
                    id: item.id,
                    previousId: previousItem ? previousItem.id : null })
                    .then(function (response) {
                        if (response.data.success) {
                            //grow.notify('Saved successfully', {type: 'success'});
                        }
                        else {
                            grow.notify('Saving new order failed ', {type: 'danger'});
                        }
                    });
            },

            _goToStep1: function() {
                var _this = this;
                this.editItem(this.currentId)
                    .then(function() {
                        _this.step = 1;
                    });
            },

            _goToStep2: function() {
                if (this.currentId === 'new') return;

                if (!this.casinoCountries || !this.casinoCountries.length) {
                    grow.notify('No countries specified', {type: 'danger'});
                    return;
                }

                var _this = this;

                var fieldsValues = _.defaults(
                    _this.$refs.form.getValues(),
                    _this.$refs.formSidebar ? _this.$refs.formSidebar.getValues() : []);
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                this.action('getCasinoData', {id: this.currentId, fields: fieldsValues})
                    .then(function (response) {
                        var casinoData = response.data.data.casinoData,
                            countryId = _this.casinoCountries[0].id;

                        console.log('---------------');
                        console.log(casinoData);

                        switch(_this.currentCasinoType) {
                            case 'casino':
                                casinoData[countryId].main.betting_categories.hidden = true;
                                casinoData[countryId].main.betting_link.hidden = true;
                                casinoData[countryId].main.betting_same_window.hidden = true;
                                casinoData[countryId].main.betting_bonuses.hidden = true;
                                casinoData[countryId].main.bet_bonus_sign_up.hidden = true;
                                casinoData[countryId].main.bet_bonus_deposit.hidden = true;
                                break;
                            case 'betting':
                                casinoData[countryId].main.casino_categories.hidden = true;
                                casinoData[countryId].main.casino_link.hidden = true;
                                casinoData[countryId].main.casino_same_window.hidden = true;
                                casinoData[countryId].main.casino_signup_bonuses.hidden = true;
                                casinoData[countryId].main.cash_sign_up.hidden = true;
                                casinoData[countryId].main.spins_sign_up.hidden = true;
                                casinoData[countryId].main.deposit_bonuses.hidden = true;
                                break;
                        }



                        _this.casinoData = casinoData;

                        // _this.formFields = response.data.data.fields;
                        // _this.formSidebarFields = response.data.data.sidebar;
                        _this.formErrors = {};

                        _this.step = 2;
                        _this.casinoDataCountryId = countryId;

                        Vue.nextTick(function() {
                            _this.$refs.casinoDataCountry.setActive(0);
                        });
                    });
            },

            saveDataClick: function() {
                if (this.locked) return;
                this.saveCasinoData();
            },

            saveCasinoData: function() {
                var dataForm = this.$refs.dataForm[0],
                    dataFormSidebar = this.$refs.dataFormSidebar ? this.$refs.dataFormSidebar[0] : null;

                if (!dataForm.isChanged && (!dataFormSidebar || !dataFormSidebar.isChanged)) {
                    grow.notify('Nothing was changed', {type: 'warning'});
                    return;
                }

                this.locked = true;

                var _this = this;
                var currentId = _this.currentId,
                    countryId = _this.casinoDataCountryId;
                var fieldsValues = _.defaults(
                    dataForm.getValues(),
                    dataFormSidebar ? dataFormSidebar.getValues() : []);

                var simpleFieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                return this.action('saveCasinoData', {
                        id: currentId,
                        countryId: countryId,
                        fields: simpleFieldsValues})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            _this.casinoData[countryId] = response.data.data.casinoData;
                            //_this.casinoData[_this.casinoDataCountryId] = response.data.data.casinoData;
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.unsaved = false;
                            _this.formErrors = {};
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed ', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                    });
            },

            editGroup: function (id) {
                if (this.locked) return;
                this.locked = true;
                var _this = this;
                this.action('getGroup', {id: id})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            _this.step = null;
                            _this.categoryRawFields = _.defaultsDeep({}, response.data.data.fields);
                            _this.formFields = _this.getFields(response.data.data.fields);
                            _this.formSidebarFields = response.data.data.sidebar;
                            _this.formErrors = {};
                            if (_this.$refs.form) {
                                console.log('reseting');
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
                fieldsValues = this.getFieldsValues(fieldsValues);

                console.log(fieldsValues);

                this.action('saveGroup', {id: _this.currentGroupId, fields: fieldsValues})
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

            getFields: function(rawFields) {
                var _this = this,
                    fields = _.defaultsDeep({}, rawFields);
                _.forEach(fields, function(field, fieldName) {
                    if (!_.includes(fieldsNamesToProcess, fieldName) || !fields[fieldName].value) return;
                    if (typeof fields[fieldName].value === 'string') {
                        fields[fieldName].value = '';
                    }
                    else if (fields[fieldName].value[_this.currentCountry]) {
                        fields[fieldName].value = fields[fieldName].value[_this.currentCountry];
                    }
                    else {
                        fields[fieldName].value = '';
                    }
                });
                return fields;
            },

            getFieldsValues: function(fieldsValues) {
                var _this = this,
                    fields = {};
                _.forEach(this.categoryRawFields, function(field, fieldName) {
                    if (_.includes(fieldsNamesToProcess, fieldName)) {
                        fields[fieldName] = field.value || {};
                        if (typeof fields[fieldName] === 'string') {
                            fields[fieldName] = {};
                        }
                        fields[fieldName][_this.currentCountry] = fieldsValues[fieldName];
                        return;
                    }
                    fields[fieldName] = fieldsValues[fieldName];
                });

                return fields;
            }

        },

        mounted: function() {
            var _this = this;
            this.$on('edit-item', function(formFields) {
                _this.currentCasinoName = formFields.name.value;
                _this.currentCasinoType = formFields.type.value;
            });
            this.$on('save-success', function(fieldsValues) {
                _this.currentCasinoType = fieldsValues.type;
            });
        }

    };

    window.Casinos = Vue.extend(Casinos);

}());