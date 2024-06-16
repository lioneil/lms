export default [
  {
    code: 'settings',
    name: 'settings',
    meta: {
      title: 'Settings',
      icon: 'mdi-tune',
      authenticatable: true,
      description: 'Manage Settings',
      sort: 500,
      permission: 'settings.preferences',
      children: [
        'settings.general',
        'settings.comment',
        'settings.mail',
      ],
    },
    children: [
      {
        code: 'settings.general',
        name: 'settings.general',
        meta: {
          title: 'General',
          authenticatable: true,
          description: 'Manage site branding options',
          sort: 5,
          permission: 'settings.branding',
        },
      },
      {
        code: 'settings.comment',
        name: 'settings.comment',
        meta: {
          title: 'Comment',
          authenticatable: true,
          description: 'Manage site comment options',
          sort: 6,
          permission: 'settings.preferences',
        },
      },
      {
        code: 'settings.mail',
        name: 'settings.mail',
        meta: {
          title: 'Mail',
          authenticatable: true,
          description: 'Manage site mail options',
          sort: 7,
          permission: 'settings.preferences',
        },
      },
    ]
  }
]
