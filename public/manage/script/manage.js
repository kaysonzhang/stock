new Vue({
    el: '#app',
    data: function () {
        return {
            isCollapse: false,
            asidewidth: 100,
            containerHeight:500,
        }
    },
    created: function () {
        this.containerHeight = `${document.documentElement.clientHeight}`;
    },
    methods: {
        siderCollapse() {
            this.isCollapse = this.isCollapse ? false : true;
            console.log(this.isCollapse);
        },
    }
});