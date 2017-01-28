(function () {

    var Posts = {

        extends: Listing,

        data: function() {
            return {
                countries: APP_DATA.availableCountries || [],
                currentCountry: APP_DATA.currentCountry || null
            }
        },

        watch: {
            state: function (state) {
                if (state !== 'edit_item') return;
                this.updateFields();
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
            },

            handleFormChange: function(value, unit, form) {
                if (unit.id !== 'category') return;
                this.updateFields(value);
            },

            updateFields: function(categoryValue) {
                var _this = this;

                Vue.nextTick(function() {
                    console.log('updateFields');

                    if (!_this.formSidebarFields.category || !_this.formSidebarFields.promotion_end) return;

                    var category = categoryValue || _this.formSidebarFields.category.value;
                    console.log(category);

                    if (category == 5) {
                        //Vue.set(_this.formFields.name, 'hidden', true);
                        Vue.set(_this.formSidebarFields.promotion_end, 'hidden', true);
                    }
                    else {
                        //Vue.set(_this.formFields.name, 'hidden', false);
                        Vue.set(_this.formSidebarFields.promotion_end, 'hidden', false);
                    }

                    console.log(_this.formSidebarFields.promotion_end.hidden);
                });
            }

        },

        beforeMount: function() {
            console.log("BEFORE");
            console.log(this.formSidebarFields);
        },

        created: function() {
            console.log("CREATED");
            console.log(this.formSidebarFields);
        },

        mounted: function() {
            console.log("MOUNT");
            console.log(APP_DATA.availableCountries);
            window.CONTROLLER = this;
        }

    };

    window.Posts = Vue.extend(Posts);

}());