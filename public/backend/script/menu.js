const menus = [
    {
        name: "股票管理",
        id: "1",
        iconClass: "el-icon-finished",
        children: [
            {name: "股票列表", id: "1-1", url: "/backend/stock-list.html"},
            {name: "股票分组", id: "1-2", url: "/backend/stock-group.html"},
        ]
    },
    {
        name: "用户管理",
        id: "2",
        iconClass: "el-icon-s-custom",
        children: [
            {name: "用户列表", id: "2-1", url: "/backend/user.html"},
        ]
    }];