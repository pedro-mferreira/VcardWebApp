<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Delete Admin"
    :msg="`Do you really want to delete the admin ${ adminToDeleteDescription }?`"
    @confirmed="deleteConfirmed"
  >
  </confirmation-dialog>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Administrators</h3>
    </div>
  </div>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addprj"
        @click="addAdmin"
      ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Admin</button>
    </div>
  </div>
  <admin-table
    :admins="admins"
    @delete="deleteAdmin"
  ></admin-table>
</template>

<script>
import AdminTable from "./AdminTable.vue"

export default {
  name: 'Admins',
  components: {
    AdminTable
  },
  data () {
    return {
      admins: [],
      adminToDelete: null
    }
  },
  computed: {
    adminToDeleteDescription () {
      return this.adminToDelete
        ? `${this.adminToDelete.name}`
        : ''
    }
  },
  methods: {
    loadAdmins () {
      this.$axios.get('administrators')
        .then((response) => {
          this.admins = response.data.data
        })
        .catch((error) => {
          console.log(error)
        })
    },
    addAdmin () {
      this.$router.push({ name: 'NewAdministrator' })
    },
    deleteConfirmed () {
      this.$axios.delete('administrators/' + this.adminToDelete.email)
        .then((response) => {
          let deletedAdmin = response.data.data
          let idx = this.admins.findIndex((t) => t.email === deletedAdmin.email)
          if (idx >= 0) {
            this.admins.splice(idx, 1)
          }
        })
        .catch((error) => {
          console.log(error)
        })
    },
    deleteAdmin (admin) {
      this.adminToDelete = admin
      let dlg = this.$refs.confirmationDialog
      dlg.show()
    },
  },
  mounted () {
    this.loadAdmins()
  }
}
</script>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-addprj {
  margin-top: 1.85rem;
}
</style>
