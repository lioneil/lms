<template>
  <div>
    <comment-form
      :commentable-id="commentableId"
      :commentable-type="commentableType"
      :parent-id="parentId"
      :title="title"
      @form:submit="submit"
      class="mb-6"
    ></comment-form>

    <div v-for="(comment, i) in items" flat class="mb-3" :key="i">
    <v-card color="transparent" flat>
      <v-card-text class="py-0">
        <v-hover v-slot:default="{ hover }">
          <v-row>
            <v-col cols="auto">
              <v-avatar size="48">
                <img
                  :src="comment.user.avatar"
                  :alt="comment.user.displayname"
                >
              </v-avatar>
            </v-col>
            <v-col>
              <h4 v-text="comment.user.displayname"></h4>
              <p class="body-2 muted--text" :title="comment.user.created_at" v-text="comment.user.created"></p>
              <p v-text="comment.body"></p>
              <div style="height: 32px;" :class="{ 'd-none': hover }"></div>
              <p class="mb-0">
                <!-- <v-icon v-show="hover" color="link">mdi-heart</v-icon> -->
                <comment-form
                  :commentable-id="commentableId"
                  :commentable-type="commentableType"
                  :parent-id="comment.id"
                  :title="comment.user.displayname"
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

    <!-- Replies -->
    <div v-for="(reply, j) in comment.replies" :key="j">
      <v-card color="transparent" flat>
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
                <h4 v-text="reply.user.displayname"></h4>
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
    </div>
    <!-- Replies -->
    </div>
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
    title: {
      type: String
    },
  },

  computed: {
    parentId () {
      return null
    }
  },

  methods: {
    submit (data) {
      this.$emit('form:submit', data)
    },
  }
}
</script>
