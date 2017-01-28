(function() {

    var UnitSelect = {

        extends: AbstractUnit,

        template: '#unit-select-template',

        data: function() {
            return {
                options: []
            }
        },

        methods: {

            handleChange: function(newValue) {
                this.currentValue = newValue;
                this.$emit('change', this.currentValue, this);
            }

        },

        mounted: function() {
            console.log("SELECT MOUNTED");
            this.options = this.config.options;
        },

        updated: function() {
            console.log('SELECT UPDATED');
        }

    };

    Vue.component('unit-select', UnitSelect);

}());