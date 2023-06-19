<template>
   <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <!-- DESCRIPTION OF TRANSACTION -->
    <div class="mb-3">
      <label
        for="textDescription"
        class="form-label"
      >Description</label>
      <textarea
        class="form-control"
        id="textDescription"
        rows="2"
        v-model="description"
      ></textarea>
      <field-error-message
        :errors="errors"
        fieldName="description"
      ></field-error-message>
    </div>

    <!-- Category OF TRANSACTION -->
    <div class="mb-3">
      <label
        for="inputTypeTransaction"
        class="form-label"
      >Category </label>
      <select
        class="form-select"
        id="inputTypeTransaction"
        v-model="category_id" 
      >
        <option v-for="category in categories" v-bind:key="category.id" :value="category.id">{{category.name}}</option>
      </select>
      <field-error-message
        :errors="errors"
        fieldName="category_id"
      ></field-error-message>
    </div>

    <div class="mb-3 d-flex justify-content-end">
      <!-- SAVE BUTTON -->
      <button
        type="button"
        class="btn btn-primary px-5"
        @click="save"
      >
        Save
      </button>
      <!-- CANCEL BUTTON -->
      <button
        type="button"
        class="btn btn-light px-5"
        @click="cancel"
      >
        Cancel
      </button>
    </div>

  </form>
</template>

<script>
export default {
    name: "CurrentTransaction",
    props: {
      transaction: {
        type: Object,
        default: () => { return {}},
      },
    },
    data() {
      return {
        username: this.$store.state.user.username,
        categories: null,
        currentTransaction: this.$store.state.transaction,
        category_id: null,
        description: null,
        errors: null,
        vcard: this.$store.state.vcard,
        typeTransEdit: null,
        user_type: this.$store.state.user.user_type
      }
  },
  methods:{
  currentDateTime() {
      const current = new Date();
      var date = (new Date()).toISOString().split('T')[0];
      const time = current.getHours() + ":" + current.getMinutes() + ":" + current.getSeconds();
      const dateTime = date +' '+ time;
      return dateTime;
    },
    autoProperties () {
      this.currentTransaction = this.$props.transaction;
      if(this.user_type=="A"){
        this.currentTransaction.type = "C";
        this.currentTransaction.vcard = this.vcard.phone_number;
        this.currentTransaction.old_balance = this.vcard.balance;
        this.currentTransaction.new_balance = parseInt(this.currentTransaction.old_balance) + parseInt(this.currentTransaction.value);
      }
      else {
        this.currentTransaction.type = "D"
        this.currentTransaction.vcard = parseInt(this.username); 
        this.currentTransaction.old_balance = this.vcard.balance;
        this.currentTransaction.new_balance = this.currentTransaction.old_balance - this.currentTransaction.value;
      }
      this.currentTransaction.date = (new Date()).toISOString().split('T')[0];
      this.currentTransaction.datetime = this.currentDateTime();
      this.currentTransaction.category_id = this.category_id;
      this.currentTransaction.description = this.description;
    },
    async save () {
      if (this.$route.params.id && this.user_type=="V") {
        //PUT
        this.currentTransaction.category_id = this.category_id;
        this.currentTransaction.description = this.description;
        await this.$axios
          .put("/vcards/" + this.vcard.phone_number + "/transactions/" + this.currentTransaction.id, this.currentTransaction)
          .then((response) => {
            this.$toast.success(
              "transaction #" + response.data.data.id + " was updated successfully."
            )
            this.$router.back()
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.$toast.error("transaction #" + this.currentTransactionid + " was not updated due to validation errors!")
              this.errors = error.response.data.errors
              
            } else {
              this.$toast.error("transaction #" + this.currentTransactionid + " was not updated due to unknown server error!")
            }
          })
      } 
      else {
        //POST
        this.autoProperties();
        // if(this.currentTransaction.vcard == this.$props.transaction.payment_reference){
        //   this.errors = "fuck";
        //   return;
        // throw this.errors = "fuck";
        // }
        await this.$axios.post("/vcards/" + this.vcard.phone_number + "/transactions", this.currentTransaction)
          .then((response) => {
            this.$toast.success("transaction #" + response.data.data.id + " was created successfully.")
            this.$store.commit('updateCurrentVcardBalance', this.currentTransaction.new_balance);
             
            var pairTransaction = null
            
             
              if(this.user_type=="V" && this.currentTransaction.payment_type=="VCARD"){
              pairTransaction={"destiny":this.currentTransaction.payment_reference,"source":this.currentTransaction.vcard,"from":this.vcard.name}
              this.$socket.emit('existTransactions',pairTransaction)
              }
              else if(this.user_type=="A"){
              //admin
              pairTransaction={"destiny":this.currentTransaction.vcard,"source":this.currentTransaction.payment_reference,"from":""}
              this.$socket.emit('existTransactions',pairTransaction)
              
              }
              
            
            
            
            this.$router.back()
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.$toast.error("transaction was not created due to validation errors! ")
              this.$store.commit('setErrors', error.response.data.errors);
              this.errors = error.response.data.errors
              this.$emit('fills',this.errors)
              
              
            } else {
              this.$toast.error("transaction was not created due to unknown server error!")
            }
          })
      }
      },
      cancel () {
        this.$router.back()
      }
    },
    mounted(){
      if(this.$route.params != {}){ //isto e para o EDIT TRANSACTION
        this.$axios
          .get("/vcards/" + this.username + "/transactions/" + this.$route.params.id)
          .then((response) => {
            this.category_id = response.data.data.category_id.id;
            this.description = response.data.data.description;
            this.typeTransEdit = response.data.data.type;
            if(this.typeTransEdit == "D"){
              this.categories = this.$store.state.debitCategories;
            }
            if(this.typeTransEdit == "C"){
              this.categories = this.$store.state.creditCategories;
            }
          })
          .catch((error) => {
           console.log(error)
          })
      }
      if(this.$props.transaction.type == "D"){
        this.categories = this.$store.state.debitCategories;
      }
    }
}
</script>

<style>

</style>