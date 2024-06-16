export default [
  {
    path: '/admin/classrooms',
    name: 'admin.classrooms',
    redirect: {name: 'classrooms.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Classrooms',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-google-classroom',
    },
    children: [
      {
        path: '/admin/classrooms',
        name: 'classrooms.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Classrooms',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-google-classroom',
        },
      },
      {
        path: 'create',
        props: true,
        name: 'classrooms.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add Classroom',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-google-classroom',
        },
      },
      {
        path: 'trashed',
        name: 'classrooms.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Classrooms',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-google-classroom',
        },
      },
      {
        path: ':id/edit',
        name: 'classrooms.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
      {
        path: ':id',
        name: 'classrooms.show',
        component: () => import('../Show.vue'),
        meta: {
          title: ':slug',
          sort: 10,
          authenticatable: true,
        },
      },
    ],
  }
]
