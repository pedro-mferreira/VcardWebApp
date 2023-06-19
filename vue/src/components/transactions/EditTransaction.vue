<template>
    <div>
      <h1>Edit Transaction</h1>
      <transaction></transaction>
    </div>
</template>

<script>
import Transaction from "./CurrentTransaction.vue";
export default {
  name: "EditTransaction",
  components: {
    Transaction
  },
};
</script>

<style>
</style>