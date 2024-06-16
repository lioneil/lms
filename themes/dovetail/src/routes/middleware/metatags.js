import store from '@/store'

export default function metatags(to, from, next) {
  const route = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

  if (route && route.meta) {
    let routeTitle = route.meta.title == ':slug' ? '' : `${route.meta.title} | `
    let routeDescription = route.meta.description
    let appTitle = store.getters['app/title']

    document.title = routeTitle ? `${routeTitle}${appTitle}` : appTitle;

    if (routeDescription) {
      document
        .querySelector('head meta[name="description"]')
        .setAttribute('content', routeDescription)
    }
  }

  // This goes through the matched routes from last to first, finding the closest route with a title.
  // eg. if we have /some/deep/nested/route and /some, /deep, and /nested have titles, nested's will be chosen.
  const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

  // Find the nearest route element with meta tags.
  const nearestWithMeta = to.matched.slice().reverse().find(r => r.meta && r.meta.metatags);
  const previousNearestWithMeta = from.matched.slice().reverse().find(r => r.meta && r.meta.metatags);

  // If a route with a title was found, set the document (page) title to that value.
  if(nearestWithTitle) document.title = nearestWithTitle.meta.title;

  // Remove any stale meta tags from the document using the key attribute we set below.
  Array.from(document.querySelectorAll('[data-vue-router-controlled]')).map(el => el.parentNode.removeChild(el));

  // Skip rendering meta tags if there are none.
  if(!nearestWithMeta) return next();

  // Turn the meta tag definitions into actual elements in the head.
  nearestWithMeta.meta.metatags.map(tagDef => {
    const tag = document.createElement('meta');

    Object.keys(tagDef).forEach(key => {
      tag.setAttribute(key, tagDef[key]);
    });

    // We use this to track which meta tags we create, so we don't interfere with other ones.
    tag.setAttribute('data-metatag-added-by-dovetail', '');

    return tag;
  })
  // Add the meta tags to the document head.
  .forEach(tag => document.head.appendChild(tag));

  next();
}
