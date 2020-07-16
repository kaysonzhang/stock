new Vue({
    el: '#app',
    data: function () {
        return {
            formInline: {
                stock_code: '',
                stock_name: ''
            },
            pagesize: 10,
            currentPage: 1,
            tableData: [],
            dataTotal: 0,
            dialogFormVisible: false,
            form: {
                edit: 0,
                stock_name: '',
                stock_code: '',
                stock_type: 'SZ',
                description: '',
                status: '1',
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
            param.append('stock_code', this.formInline.stock_code);
            param.append('stock_name', this.formInline.stock_name);
            axios.post(connect_url + '/manage/stock/getList', param)
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
        onSearch() {
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
        },
        onPost() {
            var _this = this;
            let param = new FormData()
            param.append('description', this.form.description);
            param.append('status', this.form.status);
            param.append('stock_code', this.form.stock_code);
            param.append('stock_name', this.form.stock_name);
            axios.post(connect_url + '/manage/stock/add', param)
                .then((response) => {
                    let jdata = response.data;
                    if (jdata.status) {
                        _this.dialogFormVisible = false;
                    }else{
                        _this.$alert(jdata.msg, '提示', {
                            confirmButtonText: '确定',
                            callback: action => {
                                this.$message({
                                    type: 'info',
                                    message: `action: ${ action }`
                                });
                            }
                        });
                    }
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .then(function () {
                    // always executed
                });
        }

    }
})
;