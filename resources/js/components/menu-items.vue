<script>
export const adminmenuItems = [
    {
        id: 3,
        label: "menuitems.dashboards.text",
        icon: "bx-home-circle",
        link: "/dashboard"
    },
    {
        id: 2,
        label: "menuitems.users.text",
        icon: "bxs-user-rectangle",
        subItems: [
            {
                id: 3,
                label: "menuitems.users.list.list",
                link: "/user-list",
                parentId: 2
            },
            {
                id: 3,
                label: "menuitems.users.list.add",
                link: "/add-user",
                parentId: 2
            },
        ]
    },
    {
        id: 2,
        label: "menuitems.products.text",
        icon: "bxs-capsule",
        subItems: [
            {
                id: 3,
                label: "menuitems.products.list.list",
                link: "/product-list",
                parentId: 2
            },
            {
                id: 3,
                label: "menuitems.products.list.add",
                link: "/add-product",
                parentId: 2
            },
        ]
    },
    {
        id: 2,
        label: "menuitems.reports.text",
        icon: "bxs-report",
        subItems: [
            {
                id: 3,
                label: "menuitems.reports.list.product",
                link: "/product-report",
                parentId: 2
            },
            // {
            //     id: 3,
            //     label: "menuitems.reports.list.add",
            //     link: "/user-report",
            //     parentId: 2
            // },
        ]
    },
   
];

</script>
<template>
    
</template>