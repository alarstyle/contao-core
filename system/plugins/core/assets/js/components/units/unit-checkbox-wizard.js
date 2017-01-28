(function() {

    var UnitCheckboxWizard = {

        extends: AbstractUnit,

        template: '#unit-checkbox-wizard-template',

        data: function() {
            return {
                options: [],
            }
        },

        mounted: function() {
            this.options = this.config.options;
        },

        methods: {

            onInputChange: function(e) {
                if (e.target.checked) {
                    if (!this.currentValue) {
                        this.currentValue = [e.target.value + ''];
                    }
                    else {
                        this.currentValue = _.concat(this.currentValue, [e.target.value + '']);
                    }
                }
                else {
                    this.currentValue = _.filter(this.currentValue, function(n) {
                        return n !== e.target.value + '';
                    });
                }
                this.$emit('change', this.currentValue, this);
            },

            isChecked: function(value) {
                return _.indexOf(this.currentValue, value + '') >= 0;
            }

        }

    };

    Vue.component('unit-checkbox-wizard', UnitCheckboxWizard);

}());