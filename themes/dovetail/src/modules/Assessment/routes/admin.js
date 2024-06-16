export default [
  {
    path: '/admin/assessments',
    name: 'admin.assessments',
    redirect: {name: 'assessments.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Assessments',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-ballot-outline',
    },
    children: [
      {
        path: '/admin/assessments',
        name: 'assessments.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Assessments',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-ballot-outline',
        },
      },
      {
        path: 'create',
        props: true,
        name: 'assessments.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add Assessment',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-ballot-outline',
        },
      },
      {
        path: 'trashed',
        name: 'assessments.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Assessments',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-ballot-outline',
        },
      },
      {
        path: ':id/edit',
        name: 'assessments.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
      {
        path: ':id',
        name: 'assessments.show',
        component: () => import('../Show.vue'),
        meta: {
          title: ':slug',
          sort: 10,
          authenticatable: true,
        },
      },

      // Field
      {
        path: ':id/fields',
        name: 'fields.index',
        component: () => import('../submodules/Field/Index.vue'),
        meta: {
          title: 'Manage Fields',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/fields/create',
        name: 'fields.create',
        component: () => import('../submodules/Field/Create.vue'),
        meta: {
          title: ':slug',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/fields/:field/edit',
        name: 'fields.edit',
        component: () => import('../submodules/Field/Edit.vue'),
        meta: {
          title: ':slug',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ],
  }
]
