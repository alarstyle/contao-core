(function() {

    var UnitText = {

        extends: AbstractUnit,

        template: '#unit-text-template',

        watch: {
            currentValue: function(currentValue) {
                this.$emit('change', this.currentValue, this);
            }
        },

        methods: {
            handleInputChange: function() {

            }
        }

    };

    Vue.component('unit-text', UnitText);

}());