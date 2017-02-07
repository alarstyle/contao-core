(function() {

    var UnitMultilingualText = {

        extends: AbstractUnit,

        template: '#unit-multilingual-text-template',

        watch: {
            currentValue: function(currentValue) {
                this.$emit('change', currentValue, this);
            }
        },

        methods: {

        }

    };

    Vue.component('unit-multilingual-text', UnitMultilingualText);

}());