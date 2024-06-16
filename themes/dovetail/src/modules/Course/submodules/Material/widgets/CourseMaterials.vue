<template>
  <v-card> 
    <v-list-item three-line>
      <v-list-item-content>
        <v-list-item-title class="ma-0 primary--text">Courses</v-list-item-title>
        <v-list-item-subtitle>Modules</v-list-item-subtitle>
      </v-list-item-content>
      <!-- <v-list-item-action class="d-flex align-center">
        <span class="muted--text text-h3">{{ total }}</span>
      </v-list-item-action> -->
    </v-list-item>

    <v-switch
      :input-value="isMaterialsExpanded"
      :label="isMaterialsExpanded ? 'Expanded' : 'Closed'"
      @change="isMaterialsExpanded = !isMaterialsExpanded"
      class="pl-4 mt-0"
    ></v-switch>

    <v-divider v-show="isMaterialsExpanded"></v-divider>
    <v-list dense v-show="isMaterialsExpanded">
      <v-list-group
        v-for="item in items"
        :key="item.title"
      >
        <template v-slot:activator>
          <v-list-item-avatar size="25">
            <img src="https://picsum.photos/seed/picsum/200/300" />
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>{{ item.title }}<span class="muted--text">&nbsp({{ item.items.length }})</span></v-list-item-title>
          </v-list-item-content>
        </template>

        <v-list-item
          v-for="subItem in item.items"
          :key="subItem.title"
          @click=""
        >
          <v-list-item-avatar size="25">
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title v-text="subItem.title"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>
    </v-list>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
      isMaterialsExpanded: false,
      items: [
        {
          action: 'local_activity',
          title: 'Attractions',
          items: [
            { title: 'List Item' },
          ],
        },
        {
          action: 'restaurant',
          title: 'Dining',
          items: [
            { title: 'Breakfast & brunch' },
            { title: 'New American' },
            { title: 'Sushi' },
          ],
        },
        {
          action: 'school',
          title: 'Education',
          items: [
            { title: 'List Item' },
          ],
        },
        {
          action: 'directions_run',
          title: 'Family',
          items: [
            { title: 'List Item' },
          ],
        },
        {
          action: 'healing',
          title: 'Health',
          items: [
            { title: 'List Item' },
          ],
        },
        {
          action: 'content_cut',
          title: 'Office',
          items: [
            { title: 'List Item' },
          ],
        },
        {
          action: 'local_offer',
          title: 'Promotions',
          items: [
            { title: 'List Item' },
          ],
        },
      ],
    }
  },

  computed: {
    total () {
      let sum = 0;
      this.items.forEach(item => {
        sum += item.items.length
      })
      return sum
    }
  },
}
</script>