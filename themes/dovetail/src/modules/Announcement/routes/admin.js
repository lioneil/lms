export default [
  {
    path: '/admin/announcements',
    name: 'admin.announcements',
    redirect: {name: 'announcements.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Announcements',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/announcements',
        name: 'announcements.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Announcements',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'create',
        props: true,
        name: 'announcements.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add Announcement',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'trashed',
        name: 'announcements.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Announcements',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/edit',
        name: 'announcements.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },

      // Category
      {
        path: 'categories',
        name: 'announcements.categories.index',
        component: () => import('../submodules/Category/Index.vue'),
        meta: {
          title: 'Categories',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'categories/:id/edit',
        name: 'announcements.categories.edit',
        component: () => import('../submodules/Category/Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },

      //Show announcement
      {
        path: ':id',
        name: 'announcements.show',
        component: () => import('../Show.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ],
  }
]
