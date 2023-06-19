<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">Date</th>
        <th scope="col">Value</th>
        <th scope="col">Method</th>
        <th scope="col">User</th>
        <th scope="col">Type</th>
        <th scope="col">Category</th>
        <th scope="col">New Balance</th>
        <th scope="col">Old Balance</th>
        
      </tr>
    </thead>
    <tbody>
      <tr v-for="transaction in paginatedTransactions" :key="transaction.id">
        <td>{{ transaction.date }}</td>
        <td v-if="transaction.type == 'C'">
          <span class="creditTransaction">+{{ transaction.value }}€</span>
        </td>
        <td v-else>
          <span class="debitTransaction">-{{ transaction.value }}€</span>
        </td>
        <td>{{ transaction.payment_type.name }}</td>

        <td v-if="transaction.pair_vcard != null">
          {{ transaction.pair_vcard.name }}
        </td>
        <td v-else>{{transaction.payment_reference}}</td>
      
        <td v-if="transaction.type == 'C'">Credit</td>
        <td v-else>Debit</td>
        <td>
          {{transaction.category_id==null?'Indefenida':transaction.category_id.name}}
        </td>

      
        <td>{{ transaction.new_balance}}€</td>
        <td>{{ transaction.old_balance}}€</td>
        
        
        <button
          class="btn btn-xs btn-light"
          @click="editTransaction(transaction)"
        >
          <i class="bi bi-xs bi-pencil"></i>
        </button>
        <button
          class="btn btn-xs btn-light"
          @click="detail(transaction)"
        >
          <i class="bi bi-info-circle-fill"></i>
        </button>
      </tr>
      <button
        type="button"
        class="btn btn-success px-4 btn-loadMore"
        v-if="currentPage * maxPerPage < transactions.length"
        @click="loadMore"
      >
        Load More
      </button>
    </tbody>
  </table>
</template>
<script>
export default {
  name: "TransactionsTable",
  props: {
    transactions: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      maxPerPage: 10,
      currentPage: 1,
    };
  },

  methods: {
    loadMore() {
      this.currentPage += 1;
    },

    editTransaction(transaction) {
      this.$emit("edit", transaction);
    },

    detail(transaction) {
      this.$store.commit('setTransaction', transaction);
      this.$router.push({ name: 'DetailTransaction', params: { id: transaction.id } })
    }
  },

  computed: {
    paginatedTransactions() {
      return this.transactions;
    },
  },
};
</script>

<style>
.creditTransaction {
  color: green;
}

.debitTransaction {
  color: red;
}

.btn-loadMore {
  margin-top: 1.85rem;
}
</style>




