(function() {

    var UnitSelect = {

        extends: AbstractUnit,

        template: '#unit-select-template',

        data: function() {
            return {
                options: []
            }
        },

        mounted: function() {
            this.options = this.config.options;
        },

        methods: {

            handleChange: function(newValue) {
                this.currentValue = newValue;
            }

        }

    };

    Vue.component('unit-select', UnitSelect);

}());