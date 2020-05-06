<template>
  <div id="user-card">
    <div v-if="error" class="alert alert-danger" role="alert">
      An unknown error has occured while getting this profile from our server. If you believe this is a bug, then submit an issue here: <a href="https://github.com/osu-katakuna/osu-katakuna-web/issues/new" class="alert-link">GitHub</a>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="container" v-if="!loaded">
          <span>Loading...</span>
        </div>
        <div class="container" id="user" v-if="loaded">
          <div class="row">
            <div class="col-sm-2">
              <img :src="avatar" alt="avatar" class="img-fluid rounded float-left">
            </div>
            <div class="col-sm">
              <div class="container">
                <h1>{{ name }}</h1>
                <div id="socialstatus" class="subtitle">
                  <i class="status-dot" :class="currentStatus.mode"></i>
                  <span>{{ currentStatus && statuses[currentStatus.mode] ? statuses[currentStatus.mode] : "Unknown" }} {{ currentStatus ? currentStatus.text : "" }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user_id'],
  data: () => {
    return {
      loaded: false,
      error: false,
      name: "",
      currentStatus: null,
      statuses: {
        "offline": "Offline",
        "online": "Online",
        "afk": "AFK",
        "playing": "Playing",
        "editing": "Editing",
        "modding": "Modding",
        "multiplayer": "Multiplayer",
        "watching": "Watching",
        "unknown": "Unknown",
        "testing": "Testing",
        "submitting": "Submitting",
        "paused": "Paused",
        "mp-lobby": "Lobby",
        "multiplaying": "Multiplaying",
        "direct": "osu!direct"
      }
    };
  },
  computed: {
    avatar: function() {
      return "https://a.katakuna.cc/" + this.user_id;
    }
  },
  mounted() {
    this.$socket.onopen = this.onOpen;
    console.log(this.$socket.readyState, this.$socket);
    if(this.$socket.readyState == 1) {
      this.onOpen();
    }

    this.$socket.onmessage = (message) => {
      message = JSON.parse(message.data);
      if(message.action == "user-card") {
        this.name = message.user_card.username;
        this.currentStatus = {
          "mode": message.user_card.action,
          "text": message.user_card.actionText
        };
        if(!this.loaded) {
          this.loaded = true;
        }
      }
    }

    this.$socket.onerror = () => {
      this.error = true;
      if(this.$socket.readyState == 3) {
        this.$socket.connect();
      }
    }
  },
  methods: {
    onOpen: function() {
      this.error = false;
      this.$socket.sendObj({
        "action": "listen-user-status",
        "user-id": this.user_id
      });
    }
  }
}
</script>
