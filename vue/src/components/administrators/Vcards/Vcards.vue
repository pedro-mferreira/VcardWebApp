<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">List of VCards</h3>
    </div>
  </div>
  <table class="table table-striped vcards_list">
    <thead>
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Phone Number</th>
        <th scope="col">Email</th>
        <th scope="col">Balance</th>
        <th scope="col">Max Debit</th>
        <th scope="col">Blocked</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="vcard in paginatedVcards" :key="vcard.id">
        <td>{{ vcard.name }}</td>
        <td>{{ vcard.phone_number }}</td>
        <td>{{ vcard.email }}</td>
        <td>{{ vcard.balance }}</td>
        <td>{{ vcard.max_debit }}</td>
        <td v-if="vcard.blocked == 1">Yes</td>
        <td v-if="vcard.blocked == 0">No</td>
        <button
          type="button"
          class="bi bi-xs bi-plus-circle"
          @click="createTransaction(vcard)"
        ></button>
        <button class="btn btn-xs btn-light" @click="editVcard(vcard)">
          <i class="bi bi-xs bi-pencil"></i>
        </button>
        <button @click="deleteVcard(vcard)" class="btn btn-xs btn-light">
          <i class="bi bi-x-square-fill"></i>
        </button>
      </tr>
      <button
        type="button"
        class="btn btn-success px-4 btn-loadMore"
        v-if="currentPage * maxPerPage < vcards.length"
        @click="loadMore"
      >
        Load More
      </button>
    </tbody>
  </table>
</template>

<script>
export default {
  data() {
    return {
      vcards: [],
      maxPerPage: 10,
      currentPage: 1,
    };
  },

  methods: {
    loadMore() {
      this.currentPage += 1;
    },

    async deleteVcard(vcard) {
      let credential = { isAdmin: true };
      await this.$axios
        .delete("/vcards/" + vcard.phone_number, { data: { credential } })
        .then((response) => {
          console.log(response);
          this.$axios
            .get("/vcards")
            .then((response) => {
              console.log(response.data.data);
              this.vcards = response.data.data;
              this.$socket.emit("userHasBeenDeleted",vcard.phone_number)
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch((error) => {
          console.dir(error);
          this.$toast.error(error.response.data.error);
        });
    },

    editVcard(vcard) {
      //console.log(vcard);
      this.$store.commit("setVcardToEdit", vcard);
      this.$router.push({ name: "VCard", params: { id: vcard.phone_number } });
    },
    createTransaction(vcard) {
      this.$store.commit("setVcard", vcard);
      this.$router.push({
        name: "NewTransaction",
        params: { id: vcard.phone_number },
      });
    },
  },

  computed: {
    paginatedVcards() {
      return this.vcards.slice(0, this.currentPage * this.maxPerPage);
    },
  },

  mounted() {
    this.$axios
      .get("/vcards")
      .then((response) => {
        console.log(response.data.data);
        this.vcards = response.data.data;
      })
      .catch((error) => {
        console.log(error);
      });
  },
};
</script>

<style>
.vcards_list {
  margin-top: 15px;
}

.btn-loadMore {
  margin-top: 1.85rem;
}
</style>