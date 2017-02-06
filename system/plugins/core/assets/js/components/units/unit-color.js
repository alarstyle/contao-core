(function() {

    var UnitText = {

        extends: AbstractUnit,

        template: '#unit-color-template',

        computed: {
            prefixStyle: function() {
                return 'background-color: ' + this.currentValue;
            }
        },

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

    Vue.component('unit-color', UnitText);

}());