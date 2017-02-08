(function () {

    var Casinos = {

        extends: Listing,

        data: function() {
            return {
                countries: APP_DATA.availableCountries || [],
                currentCountry: APP_DATA.currentCountry || null,
                casinoCountries: null,
                step: null,

                casinoDataCountryId: null,
                casinoData: {}
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
                            return 'Step 1';
                        }
                        else if (this.step === 2) {
                            return 'Step 2';
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
                            _this.$refs.groups.editingStateOff();
                            _this.$refs.groups.setActive(null);
                            _this.loadGroups();
                            _this.showList();
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
                if (unit.id === 'countries') {
                    this.updateCasinoCountries(value);
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
                        _this.casinoData = response.data.data.casinoData;
                        _this.casinoData = response.data.data.casinoData;

                        // _this.formFields = response.data.data.fields;
                        // _this.formSidebarFields = response.data.data.sidebar;
                        _this.formErrors = {};

                        console.log(response.data.data);

                        _this.step = 2;
                        _this.casinoDataCountryId = _this.casinoCountries[0].id;

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
            }

        }

    };

    window.Casinos = Vue.extend(Casinos);

}());