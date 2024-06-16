<v-breadcrumbs
  class="px-4"
  :items="{{
    json_encode(breadcrumbs()->all()->map(function ($crumb, $i) {
      return [
        'text' => $crumb['text'],
        'href' => $crumb['url'],
        'disabled' => ($i+1) == breadcrumbs()->all()->count()
      ];
    }))
  }}"
  >
</v-breadcrumbs>
