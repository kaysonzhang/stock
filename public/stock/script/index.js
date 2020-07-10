new Vue({
    el: '#app',
    data: function () {
        return {
            tableHeight: 500,
            activeName: 'all',
            sharesData: [],
            todos: [],
            activeIndex: '1',
            fullscreenLoading: true,
            tabs: false,
        }
    },
    created: function () {
        this.tableHeight = `${document.documentElement.clientHeight}` - 190;

        let that = this;
        getshares();

        let date = new Date();
        let hm = parseInt(date.getHours() + '' + date.getMinutes());
        //股票开盘时间才执行
        let week = parseInt(date.getDay());
        if (week > 0 && week < 6 && hm > 909 && hm < 1501) {
            console.log(parseInt(date.getDay()));
            setInterval(getshares, 10000);
        }

        function getshares() {
            axios.get('http://stock.kaysonzhang.cn:9601/')
                .then((response) => {
                    let jdata = response.data;
                    /*let all = [];
                    Object.keys(jdata.data).forEach(function (key) {
                        let d = jdata.data[key];
                        Object.keys(d).forEach(function (key) {
                            all.push(d[key]);
                        });
                    });
                    jdata.data['all'] = all;*/
                    that.sharesData = jdata;
                    if (!that.tabs) {
                        that.todos = jdata.sort;
                    }
                    that.tabs = true;
                    that.fullscreenLoading = false;

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
        price_sort(obj1, obj2) {
            let val1 = obj1.cur_price
            let val2 = obj2.cur_price
            return val1 - val2
        },
        pe_sort(obj1, obj2) {
            let val1 = obj1.pe
            let val2 = obj2.pe
            return val1 - val2
        },
        eur_sort(obj1, obj2) {
            let val1 = obj1.eur
            let val2 = obj2.eur
            return val1 - val2
        },
        famc_sort(obj1, obj2) {
            let val1 = obj1.famc
            let val2 = obj2.famc
            return val1 - val2
        },
        rise_sort(obj1, obj2) {
            let val1 = obj1.rise_and_fall_rate
            let val2 = obj2.rise_and_fall_rate
            return val1 - val2
        },
    }
});