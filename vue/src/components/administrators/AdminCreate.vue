<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">New Administrator</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            id="inputName"
            placeholder="Admin Name"
            required
            v-model="editingAdmin.name"
          />
          <field-error-message
            :errors="errors"
            fieldName="name"
          ></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            id="inputEmail"
            placeholder="Email"
            required
            v-model="editingAdmin.email"
          />
          <field-error-message
            :errors="errors"
            fieldName="email"
          ></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputPassword" class="form-label">Password</label>
          <input
            type="password"
            class="form-control"
            id="inputPassword"
            placeholder="Password"
            required
            v-model="editingAdmin.password"
          />
          <field-error-message
            :errors="errors"
            fieldName="password"
          ></field-error-message>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">
        Save
      </button>
      <button type="button" class="btn btn-light px-5" @click="cancel">
        Cancel
      </button>
    </div>

    <confirmation-dialog
      ref="confirmDelete"
      msg="Do you really want delete your account?"
      @confirmed="leaveConfirmed"
    >
    </confirmation-dialog>
  </form>
</template>

<script>
import ConfirmationDialog from "../global/ConfirmationDialog.vue";
export default {
  name: "AdminCreate",
  components: {
    ConfirmationDialog,
  },
  props: {
    admin: {
      type: Object,
      required: true,
    },
    errors: {
      type: Object,
      default: null,
    },
    
  },
  emits: ["save", "cancel"],
  data() {
    return {
      editingAdmin: this.admin,
      showModal: false,
    };
  },
  watch: {
    admin(newAdmin) {
      this.editingAdmin = newAdmin;
    },
  },
  methods: {
    /*
    deleteAccount() {
      let popUp = this.$refs.confirmDelete;
      popUp.show();
    },
    */
    leaveConfirmed() {
      this.$axios
        .delete("/administrators/" + this.$store.state.user.username)
        .then(() => {
          this.$toast.success("Delete successfull");
          this.$store.dispatch("logout");
          this.$router.push({ name: "Login" });
        })
        .catch(() => {
          this.$toast.error("Delete Unsuccessfull");
        });
    },
    save() {
      this.$emit("save", this.editingAdmin);
    },
    cancel() {
      this.$router.push({ name: "Home" });
    },
  },
};
</script>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
