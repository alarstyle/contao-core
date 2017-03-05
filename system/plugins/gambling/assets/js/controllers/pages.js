(function () {

    var Pages = {

        extends: GroupsEditing,

        data: function() {
            return {
                countries: APP_DATA.availableCountries || [],
                currentCountry: APP_DATA.currentCountry || null
            }
        },

        methods: {

            handleCountryChange: function(countryId) {
                if (this.currentCountry === countryId) return;

                var _this = this;
                this.locked = true;

                this.action('changeCountry', {countryId: countryId, id: _this.currentId})
                    .then(function (response) {
                        _this.currentCountry = countryId;
                        _this.locked = false;
                        if (response.data.success) {
                            _this.formFields = [];
                            _this.formErrors = {};
                            Vue.nextTick(function() {
                                _this.formFields = response.data.data.fields;
                                _this.$emit('edit-group', response.data.data.fields);
                                _this.unsaved = false;
                            });
                        }
                        else {
                            grow.notify('Unknown error', {type: 'danger'});
                        }
                    });
            }

        },

        mounted: function() {
            var _this = this;
            // this.$on('edit-group', function(formFields) {
            //     _this.pageFormFields = formFields;
            // });
        }

    };

    window.Pages = Vue.extend(Pages);

}());