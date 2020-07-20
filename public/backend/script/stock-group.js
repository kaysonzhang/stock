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
                group_name: '',
                user_id: '0',
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
            axios.post(connect_url + '/backend/group/getList', param)
                .then((response) => {
                    let jdata = response.data;
                    console.log(jdata);
                    this.tableData = jdata.data.data;
                    this.dataTotal = jdata.data.total;
                })
                .catch(function (error) {
                    _this.$message.error("出错了");
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
            this.dialogFormVisible = true;
            this.form.id = 0;
            this.form.group_name = '';
        },
        handleEdit(id) {
            var _this = this;
            let param = new FormData();
            this.form.id = id;
            param.append('id', id);
            axios.post(connect_url + '/backend/group/getGroup', param)
                .then((response) => {
                    let jdata = response.data;
                    if (jdata.code == 200) {
                        _this.form.group_name = jdata.data.group_name;
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
        handleDelete(id) {
            var _this = this;
            let param = new FormData()
            param.append('id', id);
            this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                axios.post(connect_url + '/backend/group/del', param)
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
            param.append('id', this.form.id);
            param.append('group_name', this.form.group_name);
            param.append('user_id', this.form.user_id);
            axios.post(connect_url + '/backend/group/save', param)
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