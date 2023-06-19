<template>
  <form class="row g-3 needs-validation" novalidate>
    <h3 class="mt-5 mb-3">Change Password</h3>
    <hr />
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputCurrentPassword" class="form-label"
          >Current Password</label
        >
        <input
          type="password"
          class="form-control"
          id="inputCurrentPassword"
          required
          v-model="passwords.current_password"
        />
        <field-error-message
          :errors="errors"
          fieldName="current_password"
        ></field-error-message>
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputPassword" class="form-label">New Password</label>
        <input
          type="password"
          class="form-control"
          id="inputPassword"
          required
          v-model="passwords.password"
        />
        <field-error-message
          :errors="errors"
          fieldName="password"
        ></field-error-message>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button
        type="button"
        class="btn btn-primary px-5"
        @click.prevent="changePassword"
      >
        Change Password
      </button>
    </div>
  </form>
</template>

<script>
export default {
  name: "ChangePassword",
  data() {
    return {
      passwords: {
        current_password: "",
        password: "",
      },
      errors: null,
    };
  },
  emits: ["changedPassword"],
  methods: {
    changePassword() {
      this.$axios
        .put(
          "administrators/" + this.$store.state.user.username + "/password",
          this.passwords
        )
        .then((response) => {
          this.$toast.success(
            "Administrator " +
              response.data.data.name +
              " password was updated successfully."
          );
          this.admin = response.data.data;
          this.$store.dispatch("loadUserAfterChange");
          this.$router.back();
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.$toast.error(
              "Administrator " +
                this.$store.state.user.name +
                " password was not updated due to validation errors!"
            );
            this.errors = error.response.data.errors;
          } else {
            this.$toast.error(
              "Administrator " +
                this.$store.state.user.name +
                " password was not updated due to unknown server error!"
            );
          }
        });
    },
  },
};
</script>

<style scoped>
</style>
