export const staffmenuItems = [
    {
        id: 1,
        label: "menuitems.dashboards.text",
        icon: "bx-home-circle",
        link: "/dashboard"
    },
    {
        id: 2,
        label: "menuitems.chat.text",
        icon: "bx-chat",
        link: "/chat"
    },
    // {
    //     id: 3,
    //     label: "menuitems.users.text",
    //     icon: "bxs-user-rectangle",
    //     subItems: [
    //         {
    //             id: 3.1,
    //             label: "menuitems.users.list.list",
    //             link: "/user-list",
    //             parentId: 3
    //         },
    //         // {
    //         //     id: 3.2,
    //         //     label: "menuitems.users.list.add",
    //         //     link: "/add-user",
    //         //     parentId: 3
    //         // },
    //     ]
    // },
    {
        id: 4,
        label: "menuitems.products.text",
        icon: "bxs-capsule",
        subItems: [
            {
                id: 4.1,
                label: "menuitems.products.list.list",
                link: "/product-list",
                parentId: 4
            },
            // {
            //     id: 4.2,
            //     label: "menuitems.products.list.add",
            //     link: "/add-product",
            //     parentId: 4
            // },
        ]
    },
    {
        id: 7,
        label: "menuitems.stocks.text",
        icon: "bxs-box",
        subItems: [
            {
                id: 7.1,
                label: "menuitems.stocks.list.stock",
                link: "/stocks",
                parentId: 7
            },
            {
                id: 7.2,
                label: "menuitems.stocks.list.exp_product",
                link: "/expired-products",
                parentId: 7
            },
        ]
    },
    {
        id: 8,
        label: "menuitems.transactions.text",
        icon: "bxs-calculator",
        subItems: [
            {
                id: 8.1,
                label: "menuitems.transactions.list.transaction",
                link: "/transaction",
                parentId: 7
            },
            {
                id: 8.2,
                label: "menuitems.transactions.list.history",
                link: "/transaction-history",
                parentId: 8
            },
        ]
    },
    {
        id: 5,
        label: "menuitems.orders.text",
        icon: "bxs-cart",
        subItems: [
            {
                id: 5.1,
                label: "menuitems.orders.list.cart",
                link: "/cart",
                parentId: 5
            },
            {
                id: 5.2,
                label: "menuitems.orders.list.product",
                link: "/supplier-products",
                parentId: 5
            },
            {
                id: 5.3,
                label: "menuitems.orders.list.order",
                link: "/my-order",
                parentId: 5
            },
        ]
    },
    {
        id: 6,
        label: "menuitems.reports.text",
        icon: "bxs-report",
        subItems: [
            {
                id: 6.1,
                label: "menuitems.reports.list.product",
                link: "/product-report",
                parentId: 6
            },
            {
                id: 6.2,
                label: "menuitems.reports.list.sales",
                link: "/sales-report",
                parentId: 6
            },
           
        ]
    },
];
