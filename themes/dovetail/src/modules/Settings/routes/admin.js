export default [
  {
    path: '/admin/settings',
    name: 'admin.settings',
    redirect: {name: 'settings.general'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Settings',
      sort: 600,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/settings',
        name: 'settings.general',
        component: () => import('../General.vue'),
        meta: {
          title: 'General settings',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'comment',
        props: true,
        name: 'settings.comment',
        component: () => import('../Comment.vue'),
        meta: {
          title: 'Comment settings',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'mail',
        props: true,
        name: 'settings.mail',
        component: () => import('../Mail.vue'),
        meta: {
          title: 'Mail settings',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ]
  }
]
