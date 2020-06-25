<template>
<fragment>
  <div v-if="error" class="alert alert-danger" role="alert">
    An unknown error has occured while getting the leaderboard from our server. If you believe this is a bug, then submit an issue here: <a href="https://github.com/osu-katakuna/osu-katakuna-web/issues/new" class="alert-link">GitHub</a>
  </div>
  <span v-if="loading">Loading...</span>
  <div v-if="!loading" id="users">
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Username</th>
          <th scope="col">E-mail</th>
          <th scope="col">Creation date/time</th>
          <th scope="col">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in pageElements" :key="user.id">
          <th scope="row">{{ user.id }}</th>
          <td>
            <a href="#" @click="() => showUserModal(user.id)">{{ user.username }}</a>&nbsp;
            <span v-if="user.ownerBadge" class="badge badge-warning"><i class="fas fa-crown"></i>&nbsp;Owner</span>
            <span v-if="user.developerBadge" class="badge badge-primary"><i class="fas fa-code"></i>&nbsp;Developer</span>
            <span v-if="user.moderatorBadge" class="badge badge-secondary"><i class="fas fa-gavel"></i>&nbsp;Moderator</span>
            <span v-if="user.bugHunterBadge" class="badge badge-info"><i class="fas fa-bug"></i>&nbsp;Bug Hunter</span>
            <span v-if="user.bot" class="badge badge-info"><i class="fas fa-robot"></i>&nbsp;Bot</span>
            <span v-if="user.banned" class="badge badge-danger"><i class="fas fa-user-slash"></i>&nbsp;Banned</span>
            <span v-if="user.deleted_at" class="badge badge-danger"><i class="fas fa-eraser"></i>&nbsp;Deleted</span>
          </td>
          <td scope="row">{{ user.email }}</td>
          <td scope="row">{{ user.created_at ? new Date(user.created_at).toGMTString() : '-' }}</td>
          <td scope="row">
            <div class="btn-group btn-group-sm" role="group" aria-label="Administration">
              <button type="button" class="btn btn-danger" @click="() => remove(user.id)"><i class="fas fa-eraser"></i>&nbsp;Delete</button>
              <button v-if="!user.banned" type="button" class="btn btn-secondary" @click="() => ban(user.id)"><i class="fas fa-user-slash"></i>&nbsp;Ban</button>
              <button v-if="user.banned" type="button" class="btn btn-primary" @click="() => pardon(user.id)"><i class="fas fa-user-check"></i>&nbsp;Pardon</button>
              <button v-if="!user.banned" type="button" class="btn btn-info"><i class="fas fa-user-tag"></i>&nbsp;Roles</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <nav aria-label="Users Pagination">
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
  <div class="modal fade" id="user_preview_modal" tabindex="-1" role="dialog" aria-labelledby="user_preview_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" v-if="showUser">
          <user-card :user_id="userID" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="() => RedirectToUserProfile(userID)">Open user profile</button>
        </div>
      </div>
    </div>
  </div>
</fragment>
</template>

<script>
import { Fragment } from 'vue-fragment'

export default {
  components: { Fragment },
  data: () => ({
    users: [],
    error: false,
    loading: true,
    currentPage: 1,
    maxElementsPerPage: 15,
    maxPagesShown: 3,
    updateTime: 15, // Will update content every specified second
    showUser: false,
    userID: 0
  }),
  created() {
    this.getAllUsers();
  },
  mounted() {
    setTimeout(this.updateUsers, this.updateTime * 1000);
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
    updateUsers: function() {
      this.getAllUsers().then(() => {
        if (!this.error)
          setTimeout(this.updateUsers, this.updateTime * 1000);
      });
    },
    getAllUsers: function() {
      return axios.get("/admin/api/users")
        .then(response => {
          this.users = response.data.users;
          this.loading = false;
        })
        .catch(error => {
          this.loading = true;
          this.error = true
        });
    },
    showUserModal: function(uid) {
      this.userID = uid;
      this.showUser = true;
      $('#user_preview_modal').modal();
      $('#user_preview_modal').on('hidden.bs.modal', () => {
        this.showUser = false;
      });
    },
    RedirectToUserProfile: function(uid) {
      window.location.href = "/u/" + uid;
    },
    remove: function(uid) {
      return axios.get("/admin/api/users/remove/" + uid)
        .then(response => {
          if(response.data.error) {
            alert("Error: " + message);
          } else {
            this.users = this.users.filter(u => (u.id != uid));
          }
        })
    },
    pardon: function(uid) {
      return axios.get("/admin/api/users/pardon/" + uid)
        .then(response => {
          if(response.data.error) {
            alert("Error: " + message);
          } else {
            this.getAllUsers();
          }
        })
    },
    ban: function(uid) {
      return axios.get("/admin/api/users/ban/" + uid)
        .then(response => {
          if(response.data.error) {
            alert("Error: " + message);
          } else {
            this.getAllUsers();
          }
        })
    }
  },
  computed: {
    pageElements: function() {
      if (this.loading || this.users === undefined) return [];
      if(this.currentPage >= this.pages) this.currentPage = this.pages;
      return this.users.slice(this.maxElementsPerPage * (this.currentPage - 1), this.maxElementsPerPage * this.currentPage);
    },
    pageNumerotation: function() {
      if (this.pages == 1) return [1];
      if (this.currentPage < this.maxPagesShown + 1)
        return _.range(1, this.pages > this.maxPagesShown ? this.maxPagesShown + 1 : this.pages + 1)

      return _.range((this.currentPage + 2) - this.maxPagesShown, this.pages < (this.currentPage + (this.maxPagesShown - 1)) ? this.pages + 1 : this.currentPage + (this.maxPagesShown - 1));
    },
    pages: function() {
      if (this.loading || this.users === undefined) return 1;
      return Math.ceil(this.users.length / this.maxElementsPerPage);
    }
  }
}
</script>
