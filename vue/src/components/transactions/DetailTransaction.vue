<template>
  <div>
    <div>
      <h1>Transaction #{{ transaction.id }}</h1>
    </div>
      <h4 class="balance">Current balance: {{vcard.balance}}</h4>
    <div class="info">
      <div class="row">
        <div class="col-md-6">
          <label v-if="transaction.type == 'C'">From</label>
          <label v-if="transaction.type == 'D'">To</label>
        </div>
        <div class="col-md-6">
          <p v-if="transaction.pair_vcard != null">{{transaction.pair_vcard.name}}</p>
          <p v-else>{{transaction.payment_reference}}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label>Date</label>
        </div>
        <div class="col-md-6">
          <p>{{transaction.datetime}}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label>Description</label>
        </div>
        <div class="col-md-6">
          <p v-if="transaction.description != null">{{transaction.description}}</p>
          <p v-else>No description</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label>Category</label>
        </div>
        <div class="col-md-6">
          <p v-if="transaction.category_id != null">{{transaction.category_id.name}}</p>
          <p v-else>Category undefined</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label>Value</label>
        </div>
        <div class="col-md-6">
          <p>{{transaction.value}}€</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      transaction: this.$store.state.transaction,
      vcard: this.$store.state.vcard
    };
  },
};
</script>

<style>
.info {
    padding-top: 50px;
}

label {
    font-weight: bold;
    font-size: 20px;
}

</style>