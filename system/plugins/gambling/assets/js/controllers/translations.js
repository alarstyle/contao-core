(function () {

    var Translations = {

        extends: AbstractApp,

        data: function() {
            return {
                countries: APP_DATA.availableCountries || [],
                currentCountry: APP_DATA.currentCountry || null,
                currentCategory: 'frontend',
                formFields: {},
                formErrors: {}
            }
        },

        watch: {

        },

        methods: {

            handleCountryChange: function(countryId) {
                if (this.currentCountry === countryId) return;

                var _this = this;

                this.locked = true;

                this.action('changeCountry', {countryId: countryId})
                    .then(function (response) {
                        _this.currentCountry = countryId;
                        _this.locked = false;
                        if (response.data.success) {
                            _this.loadVariables();
                        }
                        else {
                            grow.notify('Unknown error', {type: 'danger'});
                        }
                    });
            },

            handleChangeCategory: function() {

            },

            loadVariables: function () {
                if (this.locked) return;
                this.locked = true;
                var _this = this;
                this.action('loadVariables', {category: this.currentCategory, countryId: this.currentCountry})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            _this.formFields = response.data.data.fields;
                            _this.formErrors = {};
                            if (_this.$refs.form) {
                                window.foorm = _this.$refs.form;
                                _this.$refs.form.reset();
                            }
                        }
                        else {
                            grow.notify('Loading failed', {type: 'danger'});
                        }
                    });
            },

            saveClick: function () {
                if (this.locked) return;
                this.saveVariables();
            },

            saveVariables: function () {
                if (!this.$refs.form.isChanged) {
                    grow.notify('Nothing was changed', {type: 'warning'});
                    return;
                }

                this.locked = true;

                var _this = this;
                var fieldsValues = _.defaults(
                    _this.$refs.form.getValues());
                fieldsValues = JSON.parse(JSON.stringify(fieldsValues));

                return this.action('saveVariables', {category: _this.currentCategory, countryId: _this.currentCountry, fields: fieldsValues})
                    .then(function (response) {
                        _this.locked = false;
                        if (response.data.success) {
                            grow.notify('Saved successfully', {type: 'success'});
                            _this.unsaved = false;
                            _this.formErrors = {};
                            if (_this.currentId === 'new') {
                                _this.currentId = response.data.data.newId;
                            }
                            _this.$emit('save-success', fieldsValues);
                        }
                        else if (response.data.error) {
                            grow.notify('Saving failed', {type: 'danger'});
                            _this.formErrors = response.data.errorData;
                        }
                        else {
                            grow.notify('Unknown error', {type: 'danger'});
                        }
                    });
            },

            handleFormChange: function(value, unit, form) {

            }

        },

        mounted: function() {
            this.loadVariables();
        }

    };

    window.Translations = Vue.extend(Translations);

}());