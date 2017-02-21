(function() {

    var UnitText = {

        extends: AbstractUnit,

        template: '#unit-kit-template',

        watch: {
            currentValue: function(currentValue) {
                console.log('TEXT CHANGED', currentValue);
                this.$emit('change', this.currentValue, this);
            }
        },

        methods: {
            handleInputChange: function() {

            }
        }

    };

    Vue.component('unit-kit', UnitText);

}());