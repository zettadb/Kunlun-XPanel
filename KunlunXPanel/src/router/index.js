import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/alteration',
    component: () => import('@/views/login/alteration'),
    hidden: true

  },
  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },
  // {
  //   path: '/machine/node',
  //   name: 'Node',
  //   component: () => import('@/views/machine/node'),
  // },

  //首页不能删除,不然登录会出问题,一定要注意!!!
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        meta: { 
          title: '首页',
          noCache: true,
          icon: 'el-icon-s-home'
        }
      }
    ]
  },
  {
    path: '/system',
    component: Layout,
    redirect: '/system/menu',
    name: 'menu',
    meta: { 
      title: '系统管理',
      //requiresAuth:true, 
      icon: 'el-icon-setting'
    },
    children: [
      {
        path: 'account',
        name: 'account',
        component: () => import('@/views/system/account'),
        meta: {
          title: '用户管理',
          //requiresAuth:true, 
          noCache: true
        }
      },
      {
        path: 'role',
        name: 'role',
        component: () => import('@/views/system/role'),
        meta: {
          title: '角色管理',
          //requiresAuth:true, 
          noCache: true
         }
      },
      {
        path: 'access',
        name: 'access',
        component: () => import('@/views/system/access'),
        meta: {
          title: '授权管理',
          //requiresAuth:true, 
          noCache: true 
        }
      },
    ]
  },
  {
    path: '/machine',
    component: Layout,
    redirect: '/machine',
    name: 'Machine',
    meta: {
      title: '计算机管理',
      requiresAuth:true, 
      icon: 'el-icon-s-platform' 
    },
    children: [
      {
        path: 'list',
        name: 'list',
        component: () => import('@/views/machine/list'),
        meta: {
          title: '计算机列表',
          requiresAuth:true, 
          // icon: 'table'
        }
      },
      {
        path: 'node',
        name: 'Node',
        component: () => import('@/views/machine/node'),
        // meta: { 
        //   title: '单台计算机节点列表',
        //   requiresAuth:true, 
        //   // icon: 'tree'
        // }
      }
    ]
  },
  {
    path: '/cluster',
    component: Layout,
    redirect: '/cluster',
    name: 'Cluster',
    meta: { 
      title: '集群管理',
      requiresAuth:true, 
      icon: 'el-icon-s-help' },
    children: [
      {
        path: 'index',
        name: 'Index',
        component: () => import('@/views/cluster/index'),
        meta: {
          title: '集群列表',
          requiresAuth:true, 
          // icon: 'table' 
        }
      },
      {
        path: 'cshow',
        name: 'Cshow',
        // component: () => import('@/views/cluster/cshow1'),
        // meta: {
        //   title: '集群展示',
        //   requiresAuth:true, 
        //   // icon: 'table' 
        // }
      },
      {
        path: 'backup',
        name: 'Backup',
        component: () => import('@/views/cluster/backup'),
        meta: { 
          title: '集群备份列表',
          requiresAuth:true, 
          //  icon: 'tree' 
          }
      },
      // {
      //   path: 'tree',
      //   name: 'Tree',
      //   // component: () => import('@/views/cluster/upgrade'),
      //   meta: { 
      //     title: '集群扩容',
      //     requiresAuth:true, 
      //     // icon: 'tree'
      //   }
      // }
    ]
  },
    {
    path: '/operation',
    component: Layout,
    children: [
      {
        path: 'index',
        name: 'Operation',
        component: () => import('@/views/operation/index'),
        meta: { 
          title: '操作记录',
          requiresAuth:true, 
          icon: 'form'
        }
      }
    ]
  },

  // 404 page must be placed at the end !!!
  { path: '*', redirect: '/404', hidden: true },
]

export const asyncRoutes = [
  {
    path: '/system',
    component: Layout,
    redirect: '/system/menu',
    name: 'menu',
    meta: { 
      title: '系统管理',
      //requiresAuth:true, 
      icon: 'el-icon-setting'
    },
    children: [
      {
        path: 'account',
        name: 'account',
        component: () => import('@/views/system/account'),
        meta: {
          title: '用户管理',
          //requiresAuth:true, 
          noCache: true
        }
      },
      {
        path: 'role',
        name: 'role',
        component: () => import('@/views/system/role'),
        meta: {
          title: '角色管理',
          //requiresAuth:true, 
          noCache: true
         }
      },
      {
        path: 'access',
        name: 'access',
        component: () => import('@/views/system/access'),
        meta: {
          title: '授权管理',
          //requiresAuth:true, 
          noCache: true 
        }
      },
    ]
  },
]

const createRouter = () => new Router({
  // mode: 'history', //打包时要注释
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
