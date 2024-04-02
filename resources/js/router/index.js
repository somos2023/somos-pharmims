import { createRouter, createWebHistory } from 'vue-router';
import store from "../store";


const routes = [
    {
        path: '/dashboard',
        name: 'dashboard',
        meta: { 
                title: 'Dashboard', 
                authRequired: true, 
            },
        component: () => import('../views/home.vue'),
    },
    {
        path: '/',
        name: 'landing',
        redirect: "/home",
        meta: { isGuest: true, title: 'Landing Page' },
        component: () => import('../views/main-view.vue'),
        children: [
                {
                    path: '/home',
                    name: 'home',
                    meta: { isGuest: true, title: 'Home' },
                    component: () => import('../views/hero/home.vue')
                },
                {
                    path: '/terms',
                    name: "terms",
                    meta: { isGuest: true, title: 'Terms' },
                    component: () => import('../views/hero/terms.vue')
                },
                {
                    path: '/faq',
                    name: "faq",
                    meta: { isGuest: true, title: 'FAQ' },
                    component: () => import('../views/hero/faq.vue')
                },
            ]
    },
    {
        path: '/user-selection',
        name: 'user-selection',
        meta: { isAuth: true, title: 'User Selection' },
        component: () => import('../views/utility/user-selection.vue')
    },
    {
        path: '/login/:role',
        name: 'login',
        meta: { title: 'Login', isAuth: true },
        component: () => import('../views/account/login.vue')
    },
    {
        path: '/profile',
        name: 'profile',
        meta: { title: 'Profile', authRequired: true,  },
        component: () => import('../views/contacts/profile.vue')
    },
    {
        path: '/chat',
        name: 'chat',
        meta: { title: 'Chat', authRequired: true, isRole23: true,  },
        component: () => import('../views/chat/chat_app.vue')
    },
    {
        path: '/user-list',
        name: 'user-list',
        meta: { title: 'User List', authRequired: true, isRole12: true  },
        component: () => import('../views/user/user-list.vue')
    },
    {
        path: '/add-user',
        name: 'add-user',
        meta: { title: 'Add User', authRequired: true, isRole1: true },
        component: () => import('../views/user/user-form.vue')
    },
    {
        path: '/edit-user/:id',
        name: 'edit-user',
        meta: { title: 'Edit User', authRequired: true, isRole1: true, },
        component: () => import('../views/user/edit-form.vue')
    },
    {
        path: '/product-list',
        name: 'product-list',
        meta: { title: 'Product List', authRequired: true,  },
        component: () => import('../views/product/product-list.vue')
    },
    {
        path: '/add-product',
        name: 'add-product',
        meta: { title: 'Add Product', authRequired: true, isRole3: true, },
        component: () => import('../views/product/product-form.vue')
    },
    {
        path: '/edit-product/:id',
        name: 'edit-product',
        meta: { title: 'Edit Product', authRequired: true, isRole23: true, },
        component: () => import('../views/product/edit-form.vue')
    },
    {
        path: '/stocks',
        name: 'stocks',
        meta: { title: 'Stocks', authRequired: true,  isRole12: true, },
        component: () => import('../views/product/stocks.vue')
    },
    {
        path: '/expired-products',
        name: 'expired-products',
        meta: { title: 'Expired Products', authRequired: true, isRole12: true, },
        component: () => import('../views/product/expired-products.vue')
    },
    {
        path: '/product-report',
        name: 'product-report',
        meta: { title: 'Product Report', authRequired: true, },
        component: () => import('../views/report/product-report.vue')
    },
    {
        path: '/sales-report',
        name: 'sales-report',
        meta: { title: 'Sales Report', authRequired: true, },
        component: () => import('../views/report/sales-report.vue')
    },
    {
        path: '/user-report',
        name: 'user-report',
        meta: { title: 'User Report', authRequired: true,  isRole12: true, },
        component: () => import('../views/report/user-report.vue')
    },
    {
        path: '/transaction',
        name: 'transaction',
        meta: { title: 'Transaction', authRequired: true,  isRole2: true, },
        component: () => import('../views/transaction/transaction.vue')
    },
    {
        path: '/transaction-history',
        name: 'transaction-history',
        meta: { title: 'Transaction History', authRequired: true,  isRole12: true, },
        component: () => import('../views/transaction/history.vue')
    },
    {
        path: '/transaction-details/:id',
        name: 'transaction-details',
        meta: { title: 'Transaction Details', authRequired: true,  isRole12: true, },
        component: () => import('../views/transaction/details.vue')
    },
    // Staff Order
    {
        path: '/supplier-products',
        name: 'supplier-product',
        meta: { title: 'Product', authRequired: true, isRole2: true  },
        component: () => import('../views/order/products.vue')
    },
    {
        path: '/cart',
        name: 'cart',
        meta: { title: 'Cart', authRequired: true, isRole2: true  },
        component: () => import('../views/order/cart.vue')
    },
    {
        path: '/checkout',
        name: 'checkout',
        meta: { title: 'Checkout', authRequired: true, isRole2: true, isCheckout: true  },
        component: () => import('../views/order/checkout.vue')
    },

    {
        path: '/place-order',
        name: 'place-order',
        meta: { title: 'Place Order', authRequired: true, isRole2: true, isPlaceOrder: true  },
        component: () => import('../views/order/place-order.vue')
    },
    {
        path: '/my-order',
        name: 'my-order',
        meta: { title: 'My Order', authRequired: true, isRole12: true,  },
        component: () => import('../views/order/orders.vue')
    },
    // Supplier Shop
    {
        path: '/orders',
        name: 'shop-orders',
        meta: { title: 'Orders', authRequired: true, isRole3: true,  },
        component: () => import('../views/shop/orders.vue')
    },
    {
        path: '/order-details/:id',
        name: 'order-details',
        meta: { title: 'Order Details', authRequired: true },
        component: () => import('../views/order/details.vue')
    },
    {
        path: '/settings',
        name: 'settings',
        meta: { title: 'Settings', authRequired: true, isRole12: true, },
        component: () => import('../views/account/settings.vue')
    },
    // {
    //     path: '/order-history',
    //     name: 'shop-order-history',
    //     meta: { title: 'Order History', authRequired: true,  },
    //     component: () => import('../views/shop/history.vue')
    // },
    {
        path: '/notification',
        name: 'notification',
        meta: { title: 'Notification', authRequired: true, isRole12: true, },
        component: () => import('../views/account/notification.vue')
    },


    {
        path: '/forget-password',
        meta: { title: 'Forget Password' },
        component: () => import('../views/account/forgot-password.vue')
    },
    {
        path: '/reset-password/:token',
        meta: { title: 'Reset Password' },
        component: () => import('../views/account/reset-password.vue')
    },
    {
        path: '/pages/starter',
        meta: { authRequired: true, title: 'Starter Page' },
        component: () => import('../views/utility/starter.vue')
    },
    {
        path: '/pages/maintenance',
        meta: { authRequired: true, title: 'Maintenance' },
        component: () => import('../views/utility/maintenance.vue')
    },
    {
        path: '/pages/coming-soon',
        meta: { authRequired: true, title: 'Comming Soon' },
        component: () => import('../views/utility/coming-soon.vue')
    },
    {
        path: '/pages/404',
        name: 'not-found',
        meta: { isGuest: true, title: '404' },
        component: () => import('../views/utility/404.vue')
    },
    {
        path: '/403',
        name: 'forbidden',
        meta: { isGuest: true, title: '403' },
        component: () => import('../views/utility/403.vue')
    },
    {
        path: '/pages/500',
        name: 'server-error',
        meta: { isGuest: true, title: '500' },
        component: () => import('../views/utility/500.vue')
    },
    {
    meta: { isGuest: true, title: 'Not Found' },
    path: '/:catchAll(.*)',
    name: 'not-found',
    component: () => import('../views/utility/404.vue')

  },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { top: 0 }
    },
})

// Before each route evaluates...
router.beforeEach((to, from, next) => {
    // set title name
    // if (to.meta.title != undefined) {
    //     document.title = to.meta.title + " | Pharmims";
    // }
    if (to.meta.isRole1 && !store.state.isAdmin) {
        next({ name: "forbidden" });
    }
    if (to.meta.isRole2 && !store.state.isStaff) {
        next({ name: "forbidden" });
    }
    if (to.meta.isRole3 && !store.state.isSupplier) {
        next({ name: "forbidden" });
    }
    if (to.meta.isRole12 && store.state.isSupplier) {
        next({ name: "forbidden" });
    }
    if (to.meta.isRole23 && store.state.isAdmin) {
        next({ name: "forbidden" });
    }

    if (to.meta.authRequired && !store.state.user.token) {
        next({ name: "user-selection" });
    } else if (store.state.user.token && to.meta.isAuth) {
        next({ name: "dashboard" });
    } else if (!store.state.checkout && to.meta.isCheckout) {
        next({ name: "dashboard" });
    } else if (!store.state.placeOrder && to.meta.isPlaceOrder) {
        next({ name: "dashboard" });
    } else {
        next();
    }

});

router.afterEach((to) => {
    document.title = to.meta?.title
    ? `${to.meta.title} | ${store.state.system.name}`
    : ''

    const faviconLink = store.state.system.icon || '';
    document.querySelector('link[rel="icon"]').href = faviconLink;

    if(store.state.isChat && to.name != 'chat' ){
        clearInterval(store.state.isChat);
    }
})

export default router;