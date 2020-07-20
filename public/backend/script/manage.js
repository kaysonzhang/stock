new Vue({
    el: '#app',
    data: function () {
        return {
            isCollapse: false,
            asidewidth: 100,
            asideHeight:500,
            asideMenu:menus,
            iframeUrl:'/backend/stock-list.html',
        }
    },
    created: function () {
        this.asideHeight = `${document.documentElement.clientHeight}`-10;
    },
    methods: {
        siderCollapse() {
            this.isCollapse = this.isCollapse ? false : true;
            console.log(this.isCollapse);
        },
        iframeOpen(url){
            this.iframeUrl = url;
        },
    }
});