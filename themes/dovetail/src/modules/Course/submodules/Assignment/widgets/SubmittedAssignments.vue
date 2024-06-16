<template>
	<v-card> 
		<v-list-item three-line>
			<!-- <v-list-item-icon>
				<v-icon large>mdi-flag-outline</v-icon>
			</v-list-item-icon> -->
			<v-list-item-content>
			  <v-list-item-title class="ma-0 primary--text">Assignments</v-list-item-title>
			  <v-list-item-subtitle>Submitted</v-list-item-subtitle>
			</v-list-item-content>
			<v-list-item-action class="d-flex align-center">
		  	<span class="muted--text text-h3">{{ total }}</span>
			</v-list-item-action>
		</v-list-item>
    <v-switch
      :input-value="isSubmittedExpanded"
      :label="isSubmittedExpanded ? 'Expanded' : 'Closed'"
      @change="isSubmittedExpanded = !isSubmittedExpanded"
      class="pl-4 mt-0"
    ></v-switch>
    <v-divider v-show="isSubmittedExpanded"></v-divider>
	  <v-list v-show="isSubmittedExpanded" dense>
      <v-list-item
        :key="item.title"
        @click=""
        v-for="(item, i) in items"
      >
        <v-list-item-content>
          <v-row>
            <v-col cols="4" class="ma-0 py-0 d-flex align-center">
              <v-list-item-title v-text="item.title"></v-list-item-title>
            </v-col>
            <v-col cols="4" class="ma-0 py-0 d-flex align-center">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-progress-linear 

                    v-bind="attrs"
                    :value="items[i].items.length"
                    v-on="on"
                    color="success"
                    >
                  </v-progress-linear>
                </template>
                <span>{{ item.items.length }} out of 100</span>
              </v-tooltip>
            </v-col>
            <v-col cols="4" class="ma-0 py-0 text-truncate">
              <v-tooltip v-for="(avatar, i) in item.items" :key="i" bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-avatar 
                    size="27"
                    v-bind="attrs"
                    v-on="on"
                    >
                    <img src="https://cdn.vuetifyjs.com/images/lists/1.jpg" />
                  </v-avatar>
                </template>
                <span>{{ item.items.length }} avatar</span>
              </v-tooltip>
            </v-col>
          </v-row>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>

export default {
  data () {
    return {
      isSubmittedExpanded: false,

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
            { title: 'Breakfast & brunch' },
            { title: 'New American' },
            { title: 'Sushi' },
            { title: 'Breakfast & brunch' },
            { title: 'New American' },
            { title: 'Sushi' },
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