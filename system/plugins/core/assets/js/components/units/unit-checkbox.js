(function() {

    var UnitCheckbox = {

        extends: AbstractUnit,

        template: '#unit-checkbox-template',

        watch: {
            currentValue: function(currentValue) {
                console.log('OOOOOH');
                console.log(currentValue);
            }
        },

        methods: {

            onInputChange: function(e) {
                this.currentValue = e.target.checked ? 1 : 0;
            }

        }

    };

    Vue.component('unit-checkbox', UnitCheckbox);

}());