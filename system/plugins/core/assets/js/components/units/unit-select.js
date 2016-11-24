(function() {

    var UnitSelect = {

        extends: AbstractUnit,

        template: '#unit-select-template',

        data: function() {
            return {
                options: [],
                currentValue: null
            }
        },

        mounted: function() {
            this.options = this.config.options;
        },

        methods: {

            onChange: function(value) {
                this.$emit('change', value, this);
            }

        }

    };

    Vue.component('unit-select', UnitSelect);

}());