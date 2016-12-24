(function () {

    var Casinos = {

        extends: Listing,

        data: function() {
            return {
                countries: APP_DATA.availableCountries || [],
                currentCountry: APP_DATA.currentCountry || null,
                casinoCountries: null,
                step: null
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
                    this.casinoCountries = selectedCountries;
                    console.log(value);
                    console.log(options);
                    console.log(this.groupsList);
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
                console.log('COUNTRY SELECTED ' + countryId);
            },

            handleFormChange: function(value, unit, form) {
                if (unit.id === 'countries') {
                    this.updateCasinoCountries(value);
                }
            }

        },

        mounted: function() {
            var _this = this;
        }

    };

    window.Casinos = Vue.extend(Casinos);

}());