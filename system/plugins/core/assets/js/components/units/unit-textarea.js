(function() {

    var UnitTextarea = _.defaultsDeep({

        template: '#unit-textarea-template',

        data: function() {
            return {

            }
        },

        methods: {

        }

    }, AbstractUnit);

    Vue.component('unit-textarea', UnitTextarea);

}());