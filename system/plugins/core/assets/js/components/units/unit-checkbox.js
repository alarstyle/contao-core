(function() {

    var UnitCheckbox = {

        extends: AbstractUnit,

        template: '#unit-checkbox-template',

        methods: {

            onInputChange: function(e) {
                this.$emit('change', e.target.checked, this);
            }

        }

    };

    Vue.component('unit-checkbox', UnitCheckbox);

}());