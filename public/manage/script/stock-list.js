new Vue({
    el: '#app',
    data: function () {
        return {
            formInline: {
                stockCode: '',
                stockName: ''
            },
            pagesize: 10,
            currentPage: 1,
            tableData: [],
            dataTotal: 0,
            dialogFormVisible: false,
            form: {
                edit:0,
                stockName: '',
                stockCode: '',
                stockArea: 'SZ',
                desciption: '',
                status: 1,
            },
            formLabelWidth: '120px'
        }
    },
    created: function () {
        this.stockList();
    },
    methods: {
        stockList() {
            let param = new FormData()
            param.append('page', this.currentPage);
            param.append('pagesize', this.pagesize);
            param.append('stockCode', this.formInline.stockCode);
            param.append('stockName', this.formInline.stockName);
            axios.post('http://stock.kaysonzhang.cn:9601/manage/stock/getList', param)
                .then((response) => {
                    let jdata = response.data;
                    console.log(jdata);
                    this.tableData = jdata.data;
                    this.dataTotal = jdata.total;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .then(function () {
                    // always executed
                });
        },
        onSubmit() {
            this.currentPage = 1;
            this.stockList();
            console.log('submit!');
        },
        handleSizeChange(val) {
            this.pagesize = val;
            this.stockList();
            console.log(`每页 ${val} 条`);
        },
        handleCurrentChange(val) {
            this.currentPage = val;
            this.stockList();
            console.log(`当前页: ${val}`);
        }

    }
})
;