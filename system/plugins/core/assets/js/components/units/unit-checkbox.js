(function() {

    var UnitCheckbox = {

        extends: AbstractUnit,

        template: '#unit-checkbox-template',

        methods: {

            onInputChange: function(e) {
                this.currentValue = e.target.checked ? 1 : 0;
                this.$emit('change', this.currentValue, this);
            }

        }

    };

    Vue.component('unit-checkbox', UnitCheckbox);

}());