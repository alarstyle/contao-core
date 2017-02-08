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
            }

        }

    };

    window.Pages = Vue.extend(Pages);

}());