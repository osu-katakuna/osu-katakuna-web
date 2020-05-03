<template>
<div>
  <div v-show="error" class="alert alert-danger" role="alert">
    An unknown error has occured while getting the leaderboard from our server. If you believe this is a bug, then submit an issue here: <a href="https://github.com/osu-katakuna/osu-katakuna-web/issues/new" class="alert-link">GitHub</a>
  </div>
  <nav aria-label="Gamemodes">
    <ul class="pagination">
      <li v-for="_gamemode in gamemodes" class="page-item" :class="{'active': _gamemode.mode === gamemode}">
        <a class="page-link" href="#" :key="_gamemode.mode" @click="() => changeGamemode(_gamemode.mode)">{{ _gamemode.name }} <span class="sr-only" v-if="_gamemode.mode === gamemode">(current)</span></a>
      </li>
    </ul>
  </nav>
  <br>
  <span v-show="loading">Loading...</span>
  <div v-show="!loading" id="leaderboard">
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Rank</th>
          <th scope="col">Username</th>
          <th scope="col">PP</th>
          <th scope="col">Score</th>
          <th scope="col">Accuracy</th>
          <th scope="col">Plays</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in pageElements" :key="user.id">
          <th scope="row">#{{ user.rank }}</th>
          <td>{{ user.username }}</td>
          <td>{{ Number(user.pp).toLocaleString() }}</td>
          <td>{{ Number(user.score).toLocaleString() }}</td>
          <td>{{ user.accuracy }}%</td>
          <td>{{ Number(user.plays).toLocaleString() }}</td>
        </tr>
      </tbody>
    </table>
    <nav aria-label="Leaderboard Pagination">
      <ul class="pagination justify-content-center">
        <li class="page-item">
          <a class="page-link" href="#" aria-label="Previous" v-on:click="previousPage">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li v-for="page in pageNumerotation" class="page-item" :class="{'active': page === currentPage}">
          <a class="page-link" href="#" :key="page" @click="() => changePage(page)">{{ page }} <span class="sr-only" v-if="page === currentPage">(current)</span></a>
        </li>
        <li class="page-item">
          <a class="page-link" href="#" aria-label="Next" v-on:click="nextPage">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>
</template>

<script>
export default {
  data: () => ({
    leaderboard: [],
    gamemode: "standard",
    error: false,
    loading: true,
    currentPage: 1,
    maxElementsPerPage: 50,
    maxPagesShown: 5,
    updateTime: 15, // Will update content every specified second
    gamemodes: [
      {name: "osu!standard", mode: "standard"},
      {name: "osu!taiko", mode: "taiko"},
      {name: "osu!mania", mode: "mania"},
      {name: "osu!ctb", mode: "ctb"},
      {name: "osu!standard(Relax)", mode: "relax"}
    ]
  }),
  created() {
    this.getAllGamemodes(true);
  },
  mounted() {
    setTimeout(this.updateGamemodes, this.updateTime * 1000);
  },
  methods: {
    nextPage: function() {
      this.currentPage++;
      if (this.currentPage > this.pages) this.currentPage = this.pages;
    },
    previousPage: function() {
      this.currentPage--;
      if (this.currentPage < 1) this.currentPage = 1;
    },
    changePage: function(page) {
      if (page > this.pages) return;
      if (page < 1) return;

      this.currentPage = page;
    },
    updateGamemodes: function() {
      this.getAllGamemodes(false).then(() => {
        if(!this.error)
          setTimeout(this.updateGamemodes, this.updateTime * 1000);
      });
    },
    getAllGamemodes: function(update_state) {
      return axios.get("/api/leaderboard/all")
        .then(response => {
          this.leaderboard = response.data.leaderboard;
          this.loading = false;
        })
        .catch(error => {
          this.loading = true;
          this.error = true
        });
    },
    changeGamemode: function(gm) {
      this.gamemode = gm;
      this.currentPage = 1;
    }
  },
  computed: {
    pageElements: function() {
      if (this.loading || this.leaderboard[this.gamemode] === undefined) return [];
      return this.leaderboard[this.gamemode].slice(this.maxElementsPerPage * (this.currentPage - 1), this.maxElementsPerPage * this.currentPage);
    },
    pageNumerotation: function() {
      if (this.pages == 1) return [1];
      if (this.currentPage < this.maxPagesShown + 1)
        return _.range(1, this.pages > this.maxPagesShown ? this.maxPagesShown + 1 : this.pages + 1)

      return _.range((this.currentPage + 2) - this.maxPagesShown, this.pages < (this.currentPage + (this.maxPagesShown - 1)) ? this.pages + 1 : this.currentPage + (this.maxPagesShown - 1));
    },
    pages: function() {
      if (this.loading || this.leaderboard[this.gamemode] === undefined) return 1;
      return Math.ceil(this.leaderboard[this.gamemode].length / this.maxElementsPerPage);
    }
  }
}
</script>
