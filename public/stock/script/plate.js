new Vue({
    el: '#app',
    data: function () {
        return {
            tableHeight: 500,
            tableData: [],
            activeName: 'industry_board',
            tabs: [
                {flag: 'industry_board', text: '行业'},
                {flag: 'concept_board', text: '概念'},
            ],
            activeIndex: '2',
        }
    },
    created: function () {
        this.tableHeight = `${document.documentElement.clientHeight}` - 135;

        let that = this;
        getBorad();

        let date = new Date();
        let hm = parseInt(date.getHours() + '' + date.getMinutes());

        //股票开盘时间才执行
        let week = parseInt(date.getDay());
        if (week > 0 && week < 6 && hm > 909 && hm < 1501) {
            setInterval(getBorad, 3000);
        }

        function getBorad() {
            axios.get('http://stock.kaysonzhang.cn:9601/index/board')
                .then((response) => {
                    that.tableData = response.data;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .then(function () {
                    // always executed
                });
        }

    },
    methods: {
        handleClick(tab, event) {
            console.log(tab, event);
        },
        handleSelect(key, keyPath) {
            console.log(key, keyPath);
        },
        rise_sort(obj1, obj2) {
            let val1 = obj1.rise_and_fall
            let val2 = obj2.rise_and_fall
            return val1 - val2
        },
    }
});