<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
    v-if="showModal"
  >
  </confirmation-dialog>
  <div v-if="id == null">
    <admin-create
      :admin="admin"
      :errors="errors"
      @save="save"
      @cancel="cancel"
    ></admin-create>
  </div>
  <div v-if="id != null">
    <admin-detail
      :admin="admin"
      :errors="errors"
      @save="save"
      @cancel="cancel"
    ></admin-detail>
  </div>
</template>

<script>
import AdminDetail from "./AdminDetail.vue";
import AdminCreate from "./AdminCreate.vue";

export default {
  name: "Admin",
  components: {
    AdminDetail,
    AdminCreate,
  },
  props: {
    id: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      admin: this.newAdmin(),
      errors: null,
      showModal: false,
    };
  },
  computed: {
    operation() {
      return this.id == null ? "insert" : "update";
    },
  },
  watch: {
    // beforeRouteUpdate was not fired correctly
    // Used this watcher instead to update the ID
    id: {
      immediate: true,
      handler(newValue) {
        this.loadUser(newValue);
      },
    },
  },
  methods: {
    dataAsString() {
      return JSON.stringify(this.admin);
    },
    newAdmin() {
      return {
        id: null,
        name: "",
        email: "",
      };
    },
    loadUser(id) {
      this.errors = null;
      if (!id) {
        this.admin = this.newAdmin();
        this.originalValueStr = this.dataAsString();
      } else {
        this.$axios
          .get("administrators/" + id)
          .then((response) => {
            this.admin = response.data.data;
            this.originalValueStr = this.dataAsString();
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    save() {
      this.errors = null;
      if (this.operation == "insert") {
        this.$axios
          .post("administrators", this.admin)
          .then((response) => {
            this.$toast.success(
              "Administrator #" +
                response.data.data.name +
                " was created successfully."
            );
            this.admin = response.data.data;
            this.originalValueStr = this.dataAsString();
            this.$router.back()
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.$toast.error(
                "Administrator was not created due to validation errors!"
              );
              this.errors = error.response.data.errors;
            } else {
              this.$toast.error(
                "Administrator was not created due to unknown server error!"
              );
            }
          });
      } else {
        this.$axios
          .put("administrators/" + this.id, this.admin)
          .then((response) => {
            this.$toast.success(
              "Administrator " +
                response.data.data.name +
                " was updated successfully."
            );
            this.admin = response.data.data;
            this.$store.dispatch("loadUserAfterChange");
            this.originalValueStr = this.dataAsString();
            this.$router.push({ name: "Home" });
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.$toast.error(
                "Administaror " +
                  this.id +
                  " was not updated due to validation errors!"
              );
              this.errors = error.response.data.errors;
            } else {
              this.$toast.error(
                "Administrator " +
                  this.id +
                  " was not updated due to unknown server error!"
              );
            }
          });
      }
    },
    cancel() {
      this.$router.back();
    },
    leaveConfirmed() {
      if (this.nextCallBack) {
        this.nextCallBack();
      }
    },
  },
  beforeRouteLeave(to, from, next) {
    this.nextCallBack = null;
    let newValueStr = this.dataAsString();
    if (this.originalValueStr != newValueStr) {
      this.nextCallBack = next;
      this.showModal = true;
      let dlg = this.$refs.confirmationDialog;
      dlg.show();
    } else {
      next();
    }
  },
};
</script>
