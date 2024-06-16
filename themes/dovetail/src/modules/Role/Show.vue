<template>
  <admin>
    <metatag :title="resource.data.name"></metatag>

    <page-header :back="{ to: { name: 'roles.index' }, text: trans('Roles') }">
      <template v-slot:title>{{ resource.data.name }}</template>
      <template v-slot:utilities>
        <can code="roles.show">
          <router-link tag="a" class="dt-link text-decoration-none mr-6" exact :to="{name: 'roles.edit'}">
            <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
            {{ trans('Edit') }}
          </router-link>
        </can>
        <can code="roles.destroy">
          <a href="#" @click.prevent="askUserToDestroyResource(resource)" class="dt-link text-decoration-none mr-6">
            <v-icon small class="mb-1">mdi-delete-outline</v-icon>
            {{ trans('Move to Trash') }}
          </a>
        </can>
      </template>
    </page-header>

    <template v-if="isFetchingResource">
      <skeleton-show></skeleton-show>
    </template>

    <v-row v-show="isFinishedFetchingResource">
      <v-col cols="12" md="7">
        <v-card>
          <v-card-text>
            <h2>{{ resource.data.name }}</h2>
            <p>{{ resource.data.code }}</p>
            <p>{{ resource.data.description }}</p>
          </v-card-text>

          <div class="d-flex mb-3">
            <v-divider></v-divider>
            <v-icon small color="muted" class="mx-3 mt-n2">mdi-shield-lock</v-icon>
            <v-divider></v-divider>
          </div>

          <v-card-text>
            <h4 class="mb-5">{{ trans('Permissions') }}</h4>
            <treeview-field v-model="search"></treeview-field>
            <treeview :items="resource.data.permissions" :search="search"></treeview>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </admin>
</template>

<script>
import $api from './routes/api'
import merge from 'lodash/merge'
import Role from './Models/Role'
import { mapActions } from 'vuex'

export default {
  components: {
    SkeletonShow: () => import('./cards/SkeletonShow'),
  },

  computed: {
    filter () {
      return undefined
      // return (item, search, textKey) => item[textKey].indexOf(search) > -1
    },
    isFetchingResource () {
      return this.resource.loading
    },
    isFinishedFetchingResource () {
      return !this.resource.loading
    },
  },

  data: () => ({
    api: $api,

    resource: new Role,
    search: null,
  }),

  methods: {
    ...mapActions({
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      errorDialog: 'dialog/error',
      showSnackbar: 'snackbar/show',
      loadDialog: 'dialog/loading',
    }),

    getResource () {
      this.resource.loading = true
      axios.get($api.show(this.$route.params.id))
        .then(response => {
          this.resource.data = response.data.data
          this.resource.data.permissions = _(response.data.data.permissions)
          .groupBy('group')
          .map((items, key) => ({
            name: _(key).startCase(),
            id: key,
            children: items.map((item) => {
              return merge(item, {id: item.code})
            }),
          }))
          .value()
        }).finally(() => { this.resource.loading = false })
    },

    askUserToDestroyResource (resource) {
      this.showDialog({
        color: 'warning',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon.vue'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: trans('You are about to move to trash the selected role.'),
        text: [trans('The user will be signed out from the app. Some data related to the account like comments and files will still remain.'), trans('Are you sure you want to deactivate and move :name to Trash?', {name: resource.data.name})],
        buttons: {
          cancel: { show: true, color: 'link' },
          action: {
            text: trans('Move to Trash'),
            color: 'warning',
            callback: (dialog) => {
              this.loadDialog(true)
              this.destroyResource(resource)
            }
          }
        }
      })
    },

    destroyResource (item) {
      item.loading = true
      axios.delete(
        $api.destroy(item.data.id)
      ).then(response => {
        this.hideDialog()
        this.showSnackbar({
          show: true,
          text: trans_choice('Role successfully moved to trash', 1)
        })
        this.$router.push({ name: 'roles.index' })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => { item.loading = false })
    },
  },

  mounted () {
    this.getResource()
  },
}
</script>
