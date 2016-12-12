(function() {

    var UnitRating = {

        extends: AbstractUnit,

        template: '#unit-rating-template',

        methods: {

            starClick: function(n) {
                this.currentValue = n / 2;
            },

            isActive: function(n) {
                console.log(n);
                return n/2 <= this.currentValue;
            }

        }

    };

    Vue.component('unit-rating', UnitRating);

}());