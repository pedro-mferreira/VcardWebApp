<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">VCard {{ vcard.phone_number }}</h3>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card" style="width: 30rem">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <span>Phone Number:</span> {{ vcard.phone_number }}
          </li>
          <li class="list-group-item"><span>Name:</span> {{ vcard.name }}</li>
          <li class="list-group-item"><span>Email:</span> {{ vcard.email }}</li>
          <li class="list-group-item">
            <span>Balance:</span> {{ vcard.balance }}€
          </li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="photo">
        <div class="mb-3">
          <label class="form-label">Photo</label>
          <div class="form-control text-center">
            <img :src="photoFullUrl" class="w-100" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="max_debit">Max debit</label>
    <input
      type="number"
      class="form-control w-25 max_debit"
      id="max_debit"
      v-model="vcard.max_debit"
      required
    />
  </div>

  <div class="form-check blockDiv">
    <label class="form-check-label blockedLabel" for="isBlocked">Blocked</label>
    <select class="form-select blocked" v-model="vcard.blocked">
      <option value="0">No</option>
      <option value="1">Yes</option>
    </select>
  </div>
  <div class="mb-3 d-flex buttonsSaveAndCancel">
    <button type="button" class="btn btn-primary px-5" @click="save">
      Save
    </button>
    <button type="button" class="btn btn-light px-5" @click="cancel">
      Cancel
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      vcard: this.$store.state.vcardToEdit,
    };
  },
  methods: {
    save() {
      if (this.vcard.max_debit > this.vcard.balance) {
        this.vcard.blocked = Number(this.vcard.blocked);
        this.$axios
          .put("/administrators/vcards/" + this.vcard.phone_number, this.vcard)
          .then((response) => {
            console.log(response.data.data);
            this.$toast.success(
              "Vcard #" +
                response.data.data.phone_number +
                " was updated successfully."
            );
            //send message if blocked activate
            if(this.vcard.blocked==1){
              this.$socket.emit("userHasBeenBlocked",this.vcard.phone_number)
            }
            
            this.$router.back();
          })
          .catch((error) => {
            console.log(error);
            this.$toast.error("Unable to update Vcard");
          });
      }
      else {
        this.$toast.error('Max Debit cannot be blank or lower than current balance')
      }
    },

    cancel() {
      this.$router.push({ name: "Home" });
    },
  },

  computed: {
    photoFullUrl() {
      return this.vcard.photo_url
        ? this.$serverUrl + "/storage/fotos/" + this.vcard.photo_url
        : "./assets/img/avatar-none.png";
    },
  },
};
</script>

<style>
.photo {
  width: 25%;
}

span {
  font-weight: bold;
}

.blockDiv {
  margin-top: 20px;
}
.buttonsSaveAndCancel {
  margin-top: 20px;
}

.blocked {
  margin-left: -20px;
  width: 30%;
}
.blockedLabel {
  margin-left: -20px;
}
.max_debit:invalid {
  border-color: #800000;
  border-width: 3px;
}
</style>