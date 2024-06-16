<template>
  <v-responsive :aspect-ratio="16/9" max-height="770" class="black">
    <video
      :src="dataset.content"
      @contextmenu.prevent="preventRightClick"
      autoplay
      class="dt-player-video"
      controls
      height="100%"
      width="100%"
      @ended="onEnded"
      @timeupdate="onTimeUpdate"
      >
    </video>
  </v-responsive>
</template>

<script>
import $auth from '@/core/Auth/auth'
import $api from '@/modules/Course/routes/api'

export default {
  props: ['value'],

  computed: {
    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  data: () => ({
    userFirstTimeFinishingVideo: true
  }),

  methods: {
    preventRightClick () {
      return false
    },

    onEnded (e) {
      this.$emit('content:ended', e)
    },

    onTimeUpdate (e) {
      let currentTime = e.target.currentTime
      let durationTime = e.target.duration
      let timestampCompletedFlag = Math.floor(durationTime - 10)
      var seconds = Math.floor(e.target.currentTime);

      if (seconds >= timestampCompletedFlag && durationTime != currentTime) {
        durationTime = currentTime

        if (this.userFirstTimeFinishingVideo) {
          let data = {
            user_id: $auth.getId(),
          }
          axios.patch($api.content.complete(this.dataset.id), data,
            {
              params: {
                with: ['playlist']
              }
            }
          ).then(response => {
            this.$emit('content:completed', response.data.data)
          })
          this.userFirstTimeFinishingVideo = false
        }
      }
    }
  },

  mounted () {
    this.preventRightClick()
  },
}
</script>
