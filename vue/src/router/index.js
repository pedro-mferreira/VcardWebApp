import { createRouter, createWebHashHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Dashboard from "../components/Dashboard.vue"
import Login from "../components/auth/Login.vue"
import Register from "../components/auth/Register.vue"
import ChangePassword from "../components/auth/ChangePassword.vue"
import Users from "../components/users/Users.vue"
import User from "../components/users/User.vue"
import Admin from "../components/administrators/Admin.vue"
import Admins from "../components/administrators/Admins.vue"
import Report from "../components/Report.vue"


import Transactions from "../components/transactions/Transactions.vue";
import EditTransaction from "../components/transactions/EditTransaction.vue";
import NewTransaction from "../components/transactions/NewTransaction.vue";
import DetailTransaction from "../components/transactions/DetailTransaction.vue";

import Vcards from "../components/administrators/Vcards/Vcards.vue";
import Vcard from "../components/administrators/Vcards/VCardDetail.vue";

import Category from "../components/categories/Category.vue"
import Categories from "../components/categories/Categories.vue";


import DefaultCategory from "../components/DefaultCategories/DefaultCategory.vue"
import DefaultCategories from "../components/DefaultCategories/DefaultCategories.vue";

import Statistics from "../components/Statistics/Statistics.vue";


const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/password',
    name: 'ChangePassword',
    component: ChangePassword
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Report,
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
  },
  {
    path: '/users/:id',
    name: 'User',
    component: User,
    //props: true
    // Replaced with the following line to ensure that id is a number
    props: route => ({ id: parseInt(route.params.id) })
  },
  {
    path: '/administrators/:id',
    name: 'Administrator',
    component: Admin,
    props: route => ({ id: (route.params.id) })
  },
  {
    path: '/administrators',
    name: 'Administrators',
    component: Admins,
  },
  {
    path: '/administrators/new',
    name: 'NewAdministrator',
    component: Admin,
    props: () => ({ id: null })
  },

  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  },

  {
    path: '/transactions',
    name: 'Transactions',
    component: Transactions
  },

  {
    path: '/transaction/:id',
    name: 'Transaction',
    component: EditTransaction,
    props: route => ({ id: parseInt(route.params.id) })
  },

  {
    path: '/transaction/new',
    name: 'NewTransaction',
    component: NewTransaction
  },
  {
    path: '/transaction/:id/details',
    name: 'DetailTransaction',
    component: DetailTransaction,
    props: route => ({id: parseInt(route.params.id)})
  },
  {
    path: '/categories',
    name: 'Categories',
    component: Categories
  },

  {
    path: '/category/:id',
    name: 'Category',
    component: Category,
    props: route => ({ id: parseInt(route.params.id) })
  },
  {
    path: '/category/new',
    name: 'NewCategory',
    component: Category,
    props: () => ({ id: null })
  },
  {
    path: '/administrators/vcards',
    name: 'VCards',
    component: Vcards
  },
  {
    path: '/administrators/vcards/:id',
    name: 'VCard',
    component: Vcard,
    props: route => ({ id: parseInt(route.params.id) })
  },
  {
    path: '/defaultcategories',
    name: 'DefaultCategories',
    component: DefaultCategories
  },

  {
    path: '/defaultcategories/:id',
    name: 'DefaultCategory',
    component: DefaultCategory,
    props: route => ({ id: parseInt(route.params.id) })
  },
  {
    path: '/defaultcategories/new',
    name: 'NewDefaultCategory',
    component: DefaultCategory,
    props: () => ({ id: null })
  },
  {
    path: '/statistics',
    name: 'Statistics',
    component: Statistics
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})
import store from '../store'

router.beforeEach((to, from, next) => {
  if ((to.name == 'Login') || (to.name == 'Home' )|| (to.name == 'Register')) {
    next()
    return
  }
  if((to.name == 'Administrators') || (to.name == 'NewAdministrator') || (to.name == 'Administrator')){
    if (store.state.user.user_type == 'A'){
      next()
      return
    }
    next(false)
    return
  }
  if (!store.state.user) {
    next({ name: 'Login' })
    return
  }
  if (to.name == 'Reports') {
    if (store.state.user.type != 'A') {
      next(false)
      return
    }
  }
  if (to.name == 'User') {
    if ((store.state.user.user_type == 'A') || (store.state.user.username == to.params.id)) {
      next()
      return
    }
    next(false)
    return
  }
  next()
})
export default router