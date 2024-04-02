export const menuItems = [
    {
        id: 1,
        label: 'menuitems.dashboards.text',
        icon: 'bx-home-circle',
        subItems: [
            {
                id: 2,
                label: 'menuitems.dashboards.list.default',
                link: '/',
                parentId: 1
            },            
        ]
    },
    
    {
        id: 97,
        label: 'navbar.dropdown.megamenu.extrapages.title',
        icon: 'bx-file',
        subItems: [
                        {
                id: 101,
                label: 'menuitems.authentication.text',
                subItems: [
                    {
                        id: 102,
                        label: "menuitems.authentication.list.login",
                        link: "/auth/login-1",
                        parentId: 101
                    },
                    {
                        id: 103,
                        label: "menuitems.authentication.list.login-2",
                        link: "/auth/login-2",
                        parentId: 101
                    },
                    {
                        id: 104,
                        label: "menuitems.authentication.list.register",
                        link: "/auth/register-1",
                        parentId: 101
                    },
                    {
                        id: 105,
                        label: "menuitems.authentication.list.register-2",
                        link: "/auth/register-2",
                        parentId: 101
                    },
                    {
                        id: 106,
                        label: "menuitems.authentication.list.recoverpwd",
                        link: "/auth/recoverpw",
                        parentId: 101
                    },
                    {
                        id: 107,
                        label: "menuitems.authentication.list.recoverpwd-2",
                        link: "/auth/recoverpwd-2",
                        parentId: 101
                    },
                    {
                        id: 108,
                        label: "menuitems.authentication.list.lockscreen",
                        link: "/auth/lock-screen",
                        parentId: 101
                    },
                    {
                        id: 109,
                        label: "menuitems.authentication.list.lockscreen-2",
                        link: "/auth/lock-screen-2",
                        parentId: 101
                    },
                    {
                        id: 110,
                        label: "menuitems.authentication.list.confirm-mail",
                        link: "/auth/comfirm-mail",
                        parentId: 101
                    },
                    {
                        id: 111,
                        label: "menuitems.authentication.list.confirm-mail-2",
                        link: "/auth/comfirm-mail-2",
                        parentId: 101
                    },
                    {
                        id: 112,
                        label: "menuitems.authentication.list.verification",
                        link: "/auth/email-verification",
                        parentId: 101
                    },
                    {
                        id: 113,
                        label: "menuitems.authentication.list.verification-2",
                        link: "/auth/email-verification-2",
                        parentId: 101
                    },
                    {
                        id: 114,
                        label: "menuitems.authentication.list.verification-step",
                        link: "/auth/two-step-verification",
                        parentId: 101
                    },
                    {
                        id: 115,
                        label: "menuitems.authentication.list.verification-step-2",
                        link: "/auth/two-step-verification-2",
                        parentId: 101
                    }
                ]
            },
            {
                id: 116,
                label: 'menuitems.utility.text',
                icon: 'bx-file',
                subItems: [
                    {
                        id: 117,
                        label: 'menuitems.utility.list.starter',
                        link: '/pages/starter',
                        parentId: 116
                    },
                    {
                        id: 118,
                        label: 'menuitems.utility.list.maintenance',
                        link: '/pages/maintenance',
                        parentId: 116
                    },
                    {
                        id: 118,
                        label: "menuitems.utility.list.comingsoon",
                        link: "/pages/coming-soon",
                        parentId: 116
                    },
                ]
            },
        ]
    }
];

