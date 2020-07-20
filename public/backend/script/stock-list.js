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
                id: 0,
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
            axios.post(connect_url + '/backend/stock/getList', param)
                .then((response) => {
                    let jdata = response.data;
                    console.log(jdata);
                    this.tableData = jdata.data.data;
                    this.dataTotal = jdata.data.total;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
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
        handleAdd() {
            this.form.id = 0;
            this.form.stock_name = '';
            this.form.stock_code = '';
            this.form.stock_type = 'SZ';
            this.form.description = '';
            this.form.status = '1';
            this.dialogFormVisible = true;
        },
        updateBorad() {
            var _this = this;
            let param = new FormData();
            param.append('stock_code', '');
            axios.post(connect_url + '/backend/stock/updateBorad', param)
                .then((response) => {
                    let jdata = response.data;
                    if (jdata.code == 200) {
                        _this.$message({
                            type: 'success',
                            message: '更新成功!'
                        });
                    } else {
                        _this.$message.error(jdata.msg);
                    }
                })
                .catch(function (error) {
                    // handle error
                    _this.$message.error("出错了");
                    console.log(error);
                });
        },
        handleEdit(stock_code) {
            var _this = this;
            this.form.id = stock_code
            let param = new FormData();
            param.append('stock_code', stock_code);
            axios.post(connect_url + '/backend/stock/getStock', param)
                .then((response) => {
                    let jdata = response.data;
                    if (jdata.code == 200) {
                        _this.form.stock_name = jdata.data.stock_name;
                        _this.form.stock_code = jdata.data.stock_code;
                        _this.form.stock_type = jdata.data.stock_type;
                        _this.form.description = jdata.data.description;
                        _this.form.status = jdata.data.status;
                        _this.dialogFormVisible = true;
                    } else {
                        _this.$message.error(jdata.msg);
                    }
                })
                .catch(function (error) {
                    // handle error
                    _this.$message.error("出错了");
                    console.log(error);
                });
        },
        handleDelete(stock_code) {
            var _this = this;
            let param = new FormData()
            param.append('stock_code', stock_code);
            this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                axios.post(connect_url + '/backend/stock/del', param)
                    .then((response) => {
                        let jdata = response.data;
                        if (jdata.code == 200) {
                            _this.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                            _this.stockList();
                        } else {
                            _this.$message.error(jdata.msg);
                        }
                    })
                    .catch(function (error) {
                        // handle error
                        _this.$message.error("出错了");
                        console.log(error);
                    });

            }).catch(() => {
                _this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },
        onPost() {
            var _this = this;
            let param = new FormData()
            param.append('description', this.form.description);
            param.append('status', this.form.status);
            param.append('id', this.form.id);
            param.append('stock_code', this.form.stock_code);
            param.append('stock_name', this.form.stock_name);
            param.append('stock_type', this.form.stock_type);
            axios.post(connect_url + '/backend/stock/save', param)
                .then((response) => {
                    let jdata = response.data;
                    if (jdata.code == 200) {
                        _this.$message({
                            message: '保存成功',
                            type: 'success'
                        });
                        _this.dialogFormVisible = false;
                        _this.stockList();
                    } else {
                        _this.$message.error(jdata.msg);
                    }
                })
                .catch(function (error) {
                    // handle error
                    _this.$message.error("出错了");
                    console.log(error);
                });
        }
    }
})
;