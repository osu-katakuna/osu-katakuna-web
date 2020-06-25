<template>
  <div id="user-card">
    <h3>Spectator test</h3>
    <a>Status: {{status}}</a>
  </div>
</template>

<script>
export default {
  props: ['user_id'],
  data: () => {
    return {
      status: "unknown"
    };
  },
  mounted() {
    this.$socket.onopen = this.onOpen;
    if(this.$socket.readyState == 1) {
      this.onOpen();
    }

    this.$socket.onmessage = (message) => {
      message = JSON.parse(message.data);
      if(message.action == "spectate") {
        this.status = "status " + message.status + ": " + this.statusToMsg(message.status);
        if(message.status == 1) {
          console.log(message.frames);
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
      this.$socket.sendObj({
        "action": "spectate-user",
        "user-id": this.user_id
      });
    },
    statusToMsg: function(status) {
      if(status == 0) return "Spectate started.";
      if(status == 1) return "Spectate frame.";
      if(status == -1) return "COULD NOT SPECTATE THIS USER.";
      return "unknown";
    }
  }
}
</script>
