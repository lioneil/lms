<template>
  <div>
    <template v-for="(reply, i) in items">
      <v-card color="transparent" flat :key="i">
        <v-card-text class="py-0">
          <v-hover v-slot:default="{ hover }">
            <v-row>
              <v-col offset-md="1" cols="auto">
                <v-avatar size="48">
                  <img
                    :src="reply.user.avatar"
                    :alt="reply.user.displayname"
                  >
                </v-avatar>
              </v-col>
              <v-col>
                <h4>
                  <span class="mr-4" v-text="reply.user.displayname"></span>
                  <v-icon small class="font-weight-regular muted--text">mdi-reply</v-icon>
                  <span class="body-2 font-weight-regular muted--text" v-text="`replied to ${reply.parent.author}`"></span>
                </h4>

                <p class="body-2 muted--text" :title="reply.user.created_at" v-text="reply.user.created"></p>
                <p v-text="reply.body"></p>
                <div style="height: 32px;" :class="{ 'd-none': hover }"></div>
                <p class="mb-0">
                  <!-- <v-icon v-show="hover" color="link">mdi-heart</v-icon> -->
                  <comment-form
                    :commentable-id="commentableId"
                    :commentable-type="commentableType"
                    :parent-id="reply.id"
                    :title="reply.user.displayname"
                    @form:submit="submit"
                    class="mb-6"
                    >
                    <template v-slot:activator="{ on, attrs }">
                      <a
                        v-show="hover"
                        v-bind="attrs"
                        v-on="on"
                        class="dt-link t-d-none mr-3 mt-1"
                        v-text="trans('Reply')"
                        >
                      </a>
                    </template>
                  </comment-form>
                </p>
              </v-col>
            </v-row>
          </v-hover>
        </v-card-text>
      </v-card>

      <!-- Reply in Replies -->
      <replies-list
        @form:submit="submit"
        :items="reply.replies"
        :commentable-id="commentableId"
        :commentable-type="commentableType"
        :parent-id="reply.id"
        ></replies-list>
      <!-- Reply in Replies -->
    </template>
  </div>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    commentableId: {
      type: [String, Number]
    },
    commentableType: {
      type: String
    },
  },

  methods: {
    submit (data) {
      this.$emit('form:submit', data)
    },
  }
}
</script>
