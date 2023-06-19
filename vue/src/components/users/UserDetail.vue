<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">Vcard #{{ this.editingUser.phone_number }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            id="inputName"
            placeholder="User Name"
            required
            v-model="editingUser.name"
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
            v-model="editingUser.email"
          />
          <field-error-message
            :errors="errors"
            fieldName="email"
          ></field-error-message>
        </div>
        <div class="btn-group" style="width: 50%">
          <button
            type="button"
            class="btn btn-outline-secondary btn-block"
            data-toggle="button"
            aria-pressed="true"
            @click="changePassword"
          >
            Change Password
          </button>
          <button
            type="button"
            class="btn btn-outline-secondary btn-block"
            data-toggle="button"
            aria-pressed="false"
            @click="changeConfirmationCode"
          >
            Change Confirmation Code
          </button>
        </div>

          <div class="btn-group" style="display: inline;">
            <button
            style="background-color:rgb(182, 0, 0);border: none;color: white;margin-left:272px"
            type="button"
            class="btn btn-outline-secondary btn-block"
            @click="deleteAccount"
          >
            Delete Account
          </button>
        </div>
      

        <div class="mb-3" id="changePasswordDiv" style="display: none">
          <label for="inputNewPassword" class="form-label">New Password</label>
          <input
            type="password"
            class="form-control"
            id="inputNewPassword"
            placeholder="New Password"
            required
            v-model="editingUser.new_password"
          />
          <field-error-message
            :errors="errors"
            fieldName="new_password"
          ></field-error-message>
        </div>
        <div class="mb-3" id="changeConfirmationCodeDiv" style="display: none">
          <label for="inputConfirmationCode" class="form-label"
            >New Confirmation Code</label
          >
          <input
            type="password"
            class="form-control"
            id="inputConfirmationCode"
            placeholder="Confirmation Code"
            required
            v-model="editingUser.new_confirmation_code"
          />
          <field-error-message
            :errors="errors"
            fieldName="new_confirmation_code"
          ></field-error-message>
        </div>
        <div
          class="mb-3"
          v-if="editingUser.new_password || editingUser.new_confirmation_code"
        >
          <label for="inputOldPassword" class="form-label"
            >Current Password</label
          >
          <input
            type="password"
            class="form-control"
            id="inputOldPassword"
            placeholder="Current Password"
            required
            v-model="editingUser.old_password"
          />
          <field-error-message
            :errors="errors"
            fieldName="old_password"
          ></field-error-message>
        </div>
      </div>
      <div class="w-25">
        <div class="mb-3">
          <label class="form-label">Photo</label>
          <div class="form-control text-center">
            <img :src="photoFullUrl" class="w-100" />
          </div>
          <div>
            <label class="form-label">Change Photo</label>
            <div class="input-group">
              <input
                type="file"
                class="form-control"
                name="file"
                ref="file"
                id="file"
                
              />
            </div>
            <field-error-message
              :errors="errors"
              fieldName="photo"
            ></field-error-message>
          </div>
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
    
        <ConfirmationDelete_PopUp
          ref="confirmDelete"
          msg="Do you really want delete your account?"
          @confirmed="deleteConfirmed"
        >
        </ConfirmationDelete_PopUp>
  </form>
</template>

<script>
import ConfirmationDelete_PopUp from ".././global/ConfirmationDelete.vue"
export default {
  name: "UserDetail",
  components: {
    ConfirmationDelete_PopUp,
  },
  props: {
    user: {
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
      editingUser: this.user,
      file: "",
      showModal: false,
      confirmationCodeUser:'',
      passwordUser:'',
    };
  },
  watch: {
    user(newUser) {
      this.editingUser = newUser;
    },
  },
  computed: {
    photoFullUrl() {
      return this.editingUser.photo_url
        ? this.$serverUrl + "/storage/fotos/" + this.editingUser.photo_url
        : "./assets/img/avatar-none.png";
    },
  },
  methods: {
    changeConfirmationCode() {
      var x = document.getElementById("changeConfirmationCodeDiv");
      if (x.style.display === "none") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        this.editingUser.new_confirmation_code = undefined;
      }
    },
    changePassword() {
      var x = document.getElementById("changePasswordDiv");
      if (x.style.display === "none") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        this.editingUser.new_password = undefined;
      }
    },
    deleteAccount(){
      
      let popUp = this.$refs.confirmDelete
      popUp.show()
      
    },
    deleteConfirmed(pinConfirm,passwordConfirm){
      
      let credential={pin:pinConfirm,passwordUser:passwordConfirm,isAdmin:false}

    this.$axios
          .delete("/vcards/"+this.$store.state.user.username,{data:{credential}})
          .then(() => {
           this.$toast.success("Delete successfull");
            this.$store.dispatch("logout");  
            this.$router.push({ name: "Login" });   
          })
          .catch((error)=>{
            if (error.response.status == 422) {
              this.$toast.error(
                "Delete Account Unsuccessfull Invalid Credentials");
              
             }else{
                 this.$toast.error("Delete Unsuccessfull by server");
             }
          }); 

      
    },
    submitFile() {
      let formData = new FormData();
      formData.append("file", this.file);
      this.$axios({
        method: "post",
        url: "vcards/" + this.editingUser.phone_number + "/updatePhoto",
        data: formData,
        headers: { "Content-Type": "multipart/form-data" },
      })
        .then((response) => {
          console.log(response.data);
          
        })
        .catch((error) => {
          this.$toast.error("Ocorreu um erro a atualizar a foto");
          console.log(error);
        });
    },
    handleFileUpload() {
      this.editingUser.new_photo_url = this.$refs.file.files[0];
    },
    save() {
this.handleFileUpload()
      this.$emit("save", this.editingUser);
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
