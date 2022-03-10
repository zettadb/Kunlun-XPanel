import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

//初始化路由

export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/alteration',
    name: 'alteration',
    component: () => import('@/views/login/alteration'),
    hidden: true

  },
  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/dashboard/index'),
      meta: { title: '首页', icon: 'dashboard' }
    }]
  },
  {
    path: '/cluster',
    component: Layout,
    redirect: '/cluster/table',
    name: 'Cluster',
    meta: { title: '集群管理', icon: 'el-icon-s-help' },
    children: [
      {
        path: 'list',
        name: 'List',
        component: () => import('@/views/cluster/list'),
        meta: { title: '集群列表', icon: 'table' }
      },
      {
        path: 'backup',
        name: 'Backup',
        component: () => import('@/views/cluster/backup'),
        meta: { title: '集群备份列表', icon: 'tree' }
      },
      {
        path: 'tree',
        name: 'Tree',
        component: () => import('@/views/cluster/upgrade'),
        meta: { title: '集群扩容', icon: 'tree' }
      }
    ]
  },
  {
    path: '/example',
    component: Layout,
    redirect: '/example/table',
    name: 'Example',
    meta: { title: '计算机管理', icon: 'el-icon-s-help' },
    children: [
      {
        path: 'table',
        name: 'Table',
        component: () => import('@/views/table/index'),
        meta: { title: '计算机列表', icon: 'table' }
      },
      {
        path: 'tree',
        name: 'Tree',
        component: () => import('@/views/tree/index'),
        meta: { title: '节点列表', icon: 'tree' }
      }
    ]
  },
  
  // {
  //   path: '/form',
  //   component: Layout,
  //   children: [
  //     {
  //       path: 'index',
  //       name: 'Form',
  //       component: () => import('@/views/form/index'),
  //       meta: { title: '计算机管理', icon: 'form' }
  //     }
  //   ]
  // },

  {
    path: '/form',
    component: Layout,
    children: [
      {
        path: 'index',
        name: 'Form',
        component: () => import('@/views/form/index'),
        meta: { title: '操作记录', icon: 'form' }
      }
    ]
  },

  {
    path: '/system',
    component: Layout,
    redirect: '/system/menu',
    name: 'menu',
    meta: { title: '系统管理', icon: 'el-icon-setting' },
    children: [
      // {
      //   path: 'menu',
      //   name: 'menulist',
      //   component: () => import('@/views/system/menu'),
      //   meta: { 
      //     title: '菜单管理',
      //     noCache: true
      //   }
      // },
      {
        path: 'account',
        name: 'account',
        component: () => import('@/views/system/account'),
        meta: { title: '用户管理',noCache: true }
      },
      {
        path: 'role',
        name: 'role',
        component: () => import('@/views/system/role'),
        meta: { title: '角色管理',noCache: true }
      },
      {
        path: 'access',
        name: 'access',
        component: () => import('@/views/system/access'),
        meta: { title: '授权管理',noCache: true }
      },
      // {
      //   path: 'villagelist',
      //   name: 'villagelist',
      //   component: () => import('@/views/system/villagelist'),
      //   meta: { 
      //     title: '小区管理',
      //     noCache: true
      //   }
      // },
      // {
      //   path: 'timesaccount',
      //   name: 'timesaccount',
      //   component: () => import('@/views/system/timesaccount'),
      //   meta: { title: '用户管理',noCache: true }
      // },
    ]
  },


  // {
  //   path: '/nested',
  //   component: Layout,
  //   redirect: '/nested/menu1',
  //   name: 'Nested',
  //   meta: {
  //     title: 'Nested',
  //     icon: 'nested'
  //   },
  //   children: [
  //     {
  //       path: 'menu1',
  //       component: () => import('@/views/nested/menu1/index'), // Parent router-view
  //       name: 'Menu1',
  //       meta: { title: 'Menu1' },
  //       children: [
  //         {
  //           path: 'menu1-1',
  //           component: () => import('@/views/nested/menu1/menu1-1'),
  //           name: 'Menu1-1',
  //           meta: { title: 'Menu1-1' }
  //         },
  //         {
  //           path: 'menu1-2',
  //           component: () => import('@/views/nested/menu1/menu1-2'),
  //           name: 'Menu1-2',
  //           meta: { title: 'Menu1-2' },
  //           children: [
  //             {
  //               path: 'menu1-2-1',
  //               component: () => import('@/views/nested/menu1/menu1-2/menu1-2-1'),
  //               name: 'Menu1-2-1',
  //               meta: { title: 'Menu1-2-1' }
  //             },
  //             {
  //               path: 'menu1-2-2',
  //               component: () => import('@/views/nested/menu1/menu1-2/menu1-2-2'),
  //               name: 'Menu1-2-2',
  //               meta: { title: 'Menu1-2-2' }
  //             }
  //           ]
  //         },
  //         {
  //           path: 'menu1-3',
  //           component: () => import('@/views/nested/menu1/menu1-3'),
  //           name: 'Menu1-3',
  //           meta: { title: 'Menu1-3' }
  //         }
  //       ]
  //     },
  //     {
  //       path: 'menu2',
  //       component: () => import('@/views/nested/menu2/index'),
  //       name: 'Menu2',
  //       meta: { title: 'menu2' }
  //     }
  //   ]
  // },

  // {
  //   path: 'external-link',
  //   component: Layout,
  //   children: [
  //     {
  //       path: 'https://panjiachen.github.io/vue-element-admin-site/#/',
  //       meta: { title: 'External Link', icon: 'link' }
  //     }
  //   ]
  // },

  // 404 page must be placed at the end !!!
  { path: '*', redirect: '/404', hidden: true }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
