<template>
  <form class="row g-3 needs-validation" novalidate>
    <h3 class="mt-5 mb-3">Login</h3>
    <hr />
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputUsername" class="form-label">Username</label>
        <input
          type="text"
          class="form-control"
          id="inputUsername"
          required
          v-model="credentials.username"
        />
        <field-error-message
          :errors="errors"
          fieldName="username"
        ></field-error-message>
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputPassword" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="inputPassword"
          required
          v-model="credentials.password"
        />
        <field-error-message
          :errors="errors"
          fieldName="password"
        ></field-error-message>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click.prevent="login">
        Login
      </button>
    </div>
  </form>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      credentials: {
        username: "",
        password: "",
      },
      errors: null,
    };
  },
  emits: ["login"],
  methods: {
    login() {
      
      this.$store
        .dispatch("login", this.credentials)
        .then(() => {
          this.$toast.success(
            "User " +
              this.$store.state.user.name +
              " has entered the application."
          );
          this.$emit("login");
          this.$router.push({ name: "Home" });
        })
        .catch((error) => {
          this.errors = error.response.data;
          this.credentials.password = "";
          
          this.$toast.error(this.errors.msg);

        });
    },
  },
};
</script>

<style scoped>
</style>
