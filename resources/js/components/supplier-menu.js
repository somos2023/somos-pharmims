export const suppliermenuItems = [
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
    {
        id: 3,
        label: "menuitems.products.text",
        icon: "bxs-capsule",
        subItems: [
            {
                id: 33,
                label: "menuitems.products.list.list",
                link: "/product-list",
                parentId: 3
            },
            {
                id: 33,
                label: "menuitems.products.list.add",
                link: "/add-product",
                parentId: 3
            },
        ]
    },
    {
        id: 4,
        label: "menuitems.shop.text",
        icon: "bxs-cart",
        subItems: [
            {
                id: 44,
                label: "menuitems.shop.list.order",
                link: "/orders",
                parentId: 4
            },
        ]
    },
    
    {
        id: 5,
        label: "menuitems.reports.text",
        icon: "bxs-report",
        subItems: [
            {
                id: 55,
                label: "menuitems.reports.list.product",
                link: "/product-report",
                parentId: 5
            },
            {
                id: 66,
                label: "menuitems.reports.list.sales",
                link: "/sales-report",
                parentId: 5
            },
        ]
    },
];
