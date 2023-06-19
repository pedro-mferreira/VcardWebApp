<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
    v-if="showModal"
  >
  </confirmation-dialog>
  <user-detail
    :user="user"
    :errors="errors"
    @save="save"
    @cancel="cancel"
  ></user-detail>
</template>

<script>
import UserDetail from "./UserDetail.vue";

export default {
  name: "User",
  components: {
    UserDetail,
  },
  props: {
    id: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      user: this.newUser(),
      errors: null,
      showModal:false,
    };
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
      return JSON.stringify(this.user);
    },
    newUser() {
      return {
        id: null,
        name: "",
        email: "",
        gender: "M",
        photo_url: null,
      };
    },
    loadUser(id) {
      this.errors = null;
      if (!id || id < 0) {
        this.user = this.newUser();
        this.originalValueStr = this.dataAsString();
      } else {
        this.$axios
          .get("vcards/" + id)
          .then((response) => {
            this.user = response.data.data;
            this.originalValueStr = this.dataAsString();
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    save() {
      this.errors = null;
      let formData = new FormData();
      formData.append("photo_url", this.user.photo_url);
      formData.append("phone_number", this.user.phone_number);
      formData.append("name", this.user.name);
      formData.append("email", this.user.email);
      formData.append("confirmation_code", this.user.confirmation_code);
      if (this.user.new_photo_url) {
        formData.append("new_photo_url", this.user.new_photo_url);
      }
      if (this.user.new_confirmation_code) {
        formData.append(
          "new_confirmation_code",
          this.user.new_confirmation_code
        );
      }
      if (this.user.new_password) {
        formData.append("new_password", this.user.new_password);
      }
      if (this.user.old_password) {
        formData.append("old_password", this.user.old_password);
      }

      for (var key of formData.entries()) {
        console.log(key[0] + ", " + key[1]);
      }

      formData.append("_method", "PUT");
      this.$axios
        .post("/vcards/" + this.id, formData)
        .then((response) => {
          this.$toast.success(
            "Vcard " +
              response.data.data.phone_number +
              " was updated successfully."
          );
          this.user = response.data.data;
          this.$store.dispatch("loadUserAfterChange");
          this.originalValueStr = this.dataAsString();
          this.$router.back();
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.$toast.error(
              "Vcard " + this.id + " was not updated due to validation errors!"
            );
            this.errors = error.response.data.errors;
          } else {
            this.$toast.error(
              "Vcard " +
                this.id +
                " was not updated due to unknown server error!"
            );
          }
        });
    },
    cancel() {
      // Replace this code to navigate back
      // this.loadUser(this.id)
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
      this.showModal=true;
      let dlg = this.$refs.confirmationDialog;
      dlg.show();

    } else {
      next();
    }
  },
};
</script>
