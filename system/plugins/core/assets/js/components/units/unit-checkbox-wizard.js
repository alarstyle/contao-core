(function() {

    var UnitCheckboxWizard = {

        extends: AbstractUnit,

        template: '#unit-checkbox-wizard-template',

        data: function() {
            return {
                options: [],
                valueArr: [],
            }
        },

        mounted: function() {
            this.options = this.config.options;
        },

        methods: {

            onInputChange: function(e) {
                if (e.target.checked) {
                    this.valueArr.push(e.target.value + '');
                }
                else {
                    _.remove(this.valueArr, function(n) {
                        return n === e.target.value + '';
                    });
                }
                console.log(this.valueArr);
                this.$emit('change', this.valueArr, this);
            },

            isChecked: function(value) {
                return _.indexOf(this.value, value + '') >= 0;
            }

        }

    };

    Vue.component('unit-checkbox-wizard', UnitCheckboxWizard);

}());