<template>
	<v-card> 
		<v-list-item three-line>
			<!-- <v-list-item-icon>
				<v-icon large>mdi-flag-outline</v-icon>
			</v-list-item-icon> -->
			<v-list-item-content>
			  <v-list-item-title class="ma-0 primary--text">Assignments</v-list-item-title>
			  <v-list-item-subtitle>Pending</v-list-item-subtitle>
			</v-list-item-content>
			<v-list-item-action class="d-flex align-center">
		  	<span class="muted--text text-h3">{{ total }}</span>
			</v-list-item-action>
		</v-list-item>
    <v-switch
      :input-value="isPendingExpanded"
      :label="isPendingExpanded ? 'Expanded' : 'Closed'"
      @change="isPendingExpanded = !isPendingExpanded"
      class="pl-4 mt-0"
    ></v-switch>
    <v-divider v-show="isPendingExpanded"></v-divider>
	  <v-list v-show="isPendingExpanded" dense>
      <v-list-item
        :key="item.title"
        @click=""
        v-for="item in items"
      >
        <v-list-item-content>
          <v-list-item-title v-text="item.title"></v-list-item-title>
        </v-list-item-content>
		    <v-list-item-action>
		    	<span class="muted--text text-caption font-weight-bold">
			      {{ item.items.length }}
			    </span>
		    </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
    	isPendingExpanded: false,
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