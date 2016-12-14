(function () {

    var Posts = {

        extends: Listing,

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
            }

        },

        mounted: function() {
            console.log(APP_DATA.availableCountries);
        }

    };

    window.Posts = Vue.extend(Posts);

}());