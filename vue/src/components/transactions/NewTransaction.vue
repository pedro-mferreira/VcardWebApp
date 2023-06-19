<template>
  <div>
  <h1>Create Transaction</h1>
  <div class="mx-2">
      <h5 class="mt-4">Balance: {{ balance }}€</h5>
    </div>

    <div class="mx-2">
      <h5 class="mt-4">Max debit: {{ max_debit }}€</h5>
    </div>
    <!-- VALUE OF TRANSACTION -->
    <div class="mb-3">
      <label
        for="inputValue"
        class="form-label"
      >Value</label>
      <input
        type="number"
        class="form-control"
        id="inputValue"
        placeholder="420.00"
        required
        min="0.01"
        :max="max_debit"
        v-model="currentTransaction.value"
      >
      <field-error-message
        :errors="errors"
        fieldName="value"
      ></field-error-message>
    </div>

    <!-- TYPE OF PAYMENT - PASSAR PARA COMPONENTE À PARTE E MUDAR O HARDCODE DOS VALUES DO PAYMENT_TYPE -->
    <div class="mb-3">
      <label
        for="selectPaymentType"
        class="form-label"
      >Type of Payment</label>
      <select
        class="form-select"
        id="selectPaymentType"
        v-model="currentTransaction.payment_type"
        required
      >
        <template v-for="pT in paymentTypes" >
          <template v-if="user_type=='A'">
            <option v-if="pT.code !='VCARD'" :key="pT.id" :value="pT.code">{{pT.name}}</option>
          </template>
          <template v-else>
            <option :key="pT.id" :value="pT.code">{{pT.name}}</option>
          </template>
        </template>
        <!-- <option :value="'MASTERCARD'">MASTERCARD</option>
        <option :value="'MB'">MB</option>
        <option :value="'MBWAY'">MBWAY</option>
        <option :value="'PAYPAL'">PAYPAL</option>
        <option v-if="user_type!='A'" :value="'VCARD'">VCARD</option>
        <option :value="'VISA'">VISA</option> -->
      </select>
      <field-error-message
        :errors="errors"
        fieldName="payment_type"
      ></field-error-message>
    </div>

    <!-- IF TYPEPAYMENT = IBAN -->
    <div class="mb-3" v-if="currentTransaction.payment_type=='IBAN'">
      <label
        for="inputIban"
        class="form-label"
      >Iban</label>
      <input
        type="text"
        class="form-control"
        id="inputIban"
        placeholder="PT50002700000001234567833"
        required
        v-model="currentTransaction.payment_reference"
        pattern="/([A-Z]{2})\s*\t*(\d\d)\s*\t*(\d\d\d\d)\s*\t*(\d\d\d\d)\s*\t*(\d\d)\s*\t*(\d\d\d\d\d\d\d\d\d\d)/g"
      >
      <field-error-message
        :errors="errors"
        fieldName="payment_reference"
      ></field-error-message>
    </div>

    <!-- IF TYPEPAYMENT = MASTERCARD -->
    <div class="mb-3" v-if="currentTransaction.payment_type=='MASTERCARD'">
      <label
        for="inputMastercard"
        class="form-label"
      >Mastercard Reference</label>
      <input
        type="text" 
        class="form-control"
        id="inputMastercard"
        placeholder="24041311167000042529377"
        required
        v-model="currentTransaction.payment_reference"
        pattern="/[0-9]{23}/g"
        
      >
      <field-error-message
        :errors="errors"
        fieldName="payment_reference"
      ></field-error-message>
    </div>


    <!-- IF TYPEPAYMENT = MB -->
    <div class="mb-3" v-if="currentTransaction.payment_type=='MB'">
      <label
        for="inputMBRef"
        class="form-label"
      >MB Entity-Reference</label>
      <input
        type="text"
        class="form-control"
        id="inputMBRef"
        placeholder="12345-123456789"
        required
        v-model="currentTransaction.payment_reference"
        pattern="/[0-9]{5}+(-[0-9]{9})/g"
      >
      <field-error-message
        :errors="errors"
        fieldName="payment_reference"
      ></field-error-message>
    </div>

    <!-- IF TYPEPAYMENT = MBWAY || VCARD -->
    <div class="mb-3" v-if="currentTransaction.payment_type=='MBWAY' || currentTransaction.payment_type=='VCARD'">
      <label
        for="inputVisa"
        class="form-label"
      >Phone Number</label>
      <input
        type="number"
        class="form-control"
        id="inputPhoneNumber"
        placeholder="912235556"
        required
        v-model="currentTransaction.payment_reference"
        pattern="/[0-9]{9}/g"
      >
      <field-error-message
        :errors="errors"
        fieldName="payment_type"
      ></field-error-message>
    </div>
    
    <!-- IF TYPEPAYMENT = PAYPAL -->
    <div class="mb-3" v-if="currentTransaction.payment_type=='PAYPAL'">
      <label
        for="inputPaypalEmail"
        class="form-label"
      >Paypal Email Account</label>
      <input
        type="email"
        class="form-control"
        id="inputPaypalEmail"
        placeholder="example@mail.com"
        required
        v-model="currentTransaction.payment_reference"
        pattern='/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/"'
        
      >
      <field-error-message
        :errors="errors"
        fieldName="payment_reference"
      ></field-error-message>
    </div>

    <!-- IF TYPEPAYMENT = VISA -->
    <div class="mb-3" v-if="currentTransaction.payment_type=='VISA'">
      <label
        for="inputVisa"
        class="form-label"
      >Visa Reference</label>
      <input
        type="number"
        class="form-control"
        id="inputVisa"
        placeholder="2404131116700004"
        required
        v-model="currentTransaction.payment_reference"
        pattern="/[0-9]{16}/g"
        
      >
      <field-error-message
        :errors="errors"
        fieldName="payment_reference"
      ></field-error-message>
    </div>
    <!-- ***** END OF TYPE OF PAYMENT - PASSAR PARA COMPONENTE A PARTE E MUDAR O HARDCODE DOS CODIGOS DO TYPE -->
    
      <transaction @fills='errorFills' :transaction="currentTransaction"></transaction>
  </div>
</template>

<script>
import Transaction from "./CurrentTransaction.vue";
export default {
  name: "NewTransaction",
  components: {
    Transaction,
  },
  data(){
    return {
      username: this.$store.state.user.username,
      max_debit: this.$store.state.vcard.max_debit,
      balance: this.$store.state.vcard.balance,
      paymentTypes: [],
      currentTransaction: {
        date: null,
        datetime: null,
        type: "D",
        value: null,
        vcard: parseInt(this.$store.state.user.username),    
        old_balance: null,
        new_balance: null,
        payment_type: null,
        payment_reference: null,
        pair_transaction: null,
        pair_vcard: null,
        category_id: null,
        description: null,
      },
      user_type: this.$store.state.user.user_type,
      errors: null,
      
    }
  },
  methods:{
     errorFills(errors){
       this.errors=errors

     },
  },

    mounted(){
     this.$axios
        .get("/payment-types")
        .then((response) => {
          console.log("faljfasd")
          console.log(response.data.data)
          this.paymentTypes = response.data.data;
          
        })
        .catch((error) => {
          console.log(error);
        });
      
    }
 
}
</script>

<style>

</style>