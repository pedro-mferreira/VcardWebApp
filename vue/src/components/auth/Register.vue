<template>
  <form class="row g-3 needs-validation" novalidate>
    <h3 class="mt-5 mb-3">Register</h3>
    <hr />
   <div class="mb-3">
      <div class="mb-3">
        <label for="inputPhone" class="form-label">Phone Number</label>
        <input
          type="phone"
          maxlength="9"
          class="form-control"
          id="inputPhone"
          required
          v-model="vcardUser.phone_number"
        />
        <field-error-message
          :errors="errors"
          fieldName="phone_number"
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
          v-model="vcardUser.password"
        />
        <field-error-message
          :errors="errors"
          fieldName="password"
        ></field-error-message>
      </div>
    </div>

    

    <div class="mb-3">
      <div class="mb-3">
        <label for="input" class="form-label">Name</label>
        <input
          type="text"
          class="form-control"
          id="inputName"
          required
          v-model="vcardUser.name"
        />
        <field-error-message
          :errors="errors"
          fieldName="name"
        ></field-error-message>
      </div>
    </div>

    <div class="mb-3">
      <div class="mb-3">
        <label for="input" class="form-label">Mail</label>
        <input
          type="email"
          class="form-control"
          id="inputMail"
          required
          v-model="vcardUser.email"
        />
        <field-error-message
          :errors="errors"
          fieldName="email"
        ></field-error-message>
      </div>
    </div>

    <div class="mb-3">
      <div class="mb-3">
        <label for="inputconfirmation_code" class="form-label">Confirmation Transaction Code(PIN)</label>
        <input
          type="password"
          maxlength="4"
          class="form-control"
          id="inputconfirmation_code"
          required
          v-model="vcardUser.confirmation_code"
        />
        <field-error-message
          :errors="errors"
          fieldName="confirmation_code"
        ></field-error-message>
      </div>
    </div>

    <div class="mb-3">
      <div class="mb-3">
        <label for="inputPhoto" class="form-label">photo</label>
        <input
          type="file" 
          class="form-control"
          id="inputPhoto"
          v-on:change="onFileChange"
        />
        <field-error-message
          :errors="errors"
          fieldName="photo"
        ></field-error-message>
      </div>
    </div>

    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click.prevent="register">
        Register
      </button>
    </div>
  </form>
</template>

<script>
export default {
  name: "Register",
  data() {
    return {
      vcardUser: {
        phone_number: "",
        password: "",
        name:"",
        email:"",
        photo_url:"",
        confirmation_code:null
      },
      errors: null,
    };
  },
  methods: {
    onFileChange(e) {
      this.vcardUser.photo_url = e.target.files[0];
    },
    register() {
      console.log( this.vcardUser.photo_url)
      this.$store
        .dispatch("register", this.vcardUser)
        .then(() => {
          this.$toast.success("User " +this.$store.state.user.name +" has registered.");
          this.$router.push({ name: "Home" });
        })
        .catch((error) => {
          //erros que veem do laravel atraves da validacao
          this.errors = error.response.data.errors
          this.$toast.error("Register Invalid");
        });
    },
  },
};
</script>

<style scoped>
</style>