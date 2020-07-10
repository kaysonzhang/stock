new Vue({
    el: '#app',
    data: function () {
        var validatePhone = (rule, value, callback) => {
            if (value === '') {
                callback(new Error('请输入手机号码'));
            } else {
                callback();
            }
        };
        var validatePass = (rule, value, callback) => {
            if (value === '') {
                callback(new Error('请输入密码'));
            } else {
                callback();
            }
        };
        return {
            ruleForm: {
                phone: '',
                pass: '',
            },
            rules: {
                phone: [
                    {validator: validatePhone, trigger: 'blur'}
                ],
                pass: [
                    {validator: validatePass, trigger: 'blur'}
                ]
            }
        };
    },
    methods: {
        submitForm(formName) {
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    let param = new FormData()
                    param.append('phone', _this.ruleForm.phone);
                    param.append('pass', _this.ruleForm.pass);
                    axios.post('http://stock.kaysonzhang.cn:9601/login', param)
                        .then((response) => {
                            let jdata = response.data;
                            if (jdata.status) {
                                localStorage.setItem('Authorization', jdata.token);
                            } else {
                                alert("登录失败");
                            }
                        })
                        .catch(function (error) {
                            // handle error
                            console.log(error);
                        })
                        .then(function () {
                            // always executed
                        });
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        }
    }
});